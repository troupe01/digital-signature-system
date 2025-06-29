<?php
// File: public/generate_migrations.php (Simple Version)

// Database configuration (from your .env file)
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'digital_signature';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Migration Generator</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .table-section { border: 2px solid #333; margin: 20px 0; padding: 15px; border-radius: 8px; }
        .field { background: #f0f0f0; padding: 8px; margin: 3px 0; border-radius: 4px; }
        .key { background: #e0e0ff; padding: 8px; margin: 3px 0; border-radius: 4px; }
        .migration-code { background: #263238; color: #ffffff; padding: 15px; margin: 10px 0; white-space: pre-wrap; border-radius: 8px; font-size: 12px; overflow-x: auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; font-weight: bold; }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        h3 { color: #2980b9; }
        .summary { background: #ecf0f1; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🗄️ Database Migration Generator for Digital Signature System</h1>";

try {
    // Connect to MySQL directly
    $pdo = new PDO("mysql:host=$hostname;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p class='success'>✅ Connected to database: <strong>$database</strong></p>";

    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<div class='summary'>";
    echo "<h2>📊 Database Overview</h2>";
    echo "<p class='info'>Found " . count($tables) . " tables: <strong>" . implode(', ', $tables) . "</strong></p>";
    echo "</div>";

    $migrationOrder = ['users', 'documents', 'signatures', 'audit_logs']; // Proper order for foreign keys
    $analyzedTables = [];

    foreach ($tables as $table) {
        echo "<div class='table-section'>";
        echo "<h3>🔹 Table: <strong>$table</strong></h3>";

        try {
            // Get table structure
            $stmt = $pdo->query("DESCRIBE `$table`");
            $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get row count
            $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $rowCount = $stmt->fetchColumn();

            // Get indexes
            $stmt = $pdo->query("SHOW INDEX FROM `$table`");
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get foreign keys
            $stmt = $pdo->query("
                SELECT 
                    COLUMN_NAME,
                    CONSTRAINT_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE 
                    TABLE_SCHEMA = '$database' 
                    AND TABLE_NAME = '$table' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h4>📋 Fields (" . count($fields) . "):</h4>";
            foreach ($fields as $field) {
                $nullable = $field['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
                $key = $field['Key'] ? " [{$field['Key']}]" : '';
                $default = $field['Default'] !== null ? " DEFAULT: {$field['Default']}" : '';
                $extra = $field['Extra'] ? " {$field['Extra']}" : '';

                echo "<div class='field'>";
                echo "<strong>{$field['Field']}</strong>: {$field['Type']} | $nullable$key$default$extra";
                echo "</div>";
            }

            // Process indexes
            $processedIndexes = [];
            foreach ($indexes as $index) {
                $keyName = $index['Key_name'];
                if (!isset($processedIndexes[$keyName])) {
                    $processedIndexes[$keyName] = [
                        'name' => $keyName,
                        'type' => $index['Non_unique'] == 0 ? ($keyName == 'PRIMARY' ? 'PRIMARY' : 'UNIQUE') : 'INDEX',
                        'fields' => []
                    ];
                }
                $processedIndexes[$keyName]['fields'][] = $index['Column_name'];
            }

            if (!empty($processedIndexes)) {
                echo "<h4>🔑 Keys/Indexes (" . count($processedIndexes) . "):</h4>";
                foreach ($processedIndexes as $key) {
                    echo "<div class='key'>";
                    echo "<strong>{$key['name']}</strong>: {$key['type']} on [" . implode(', ', $key['fields']) . "]";
                    echo "</div>";
                }
            }

            if (!empty($foreignKeys)) {
                echo "<h4>🔗 Foreign Keys (" . count($foreignKeys) . "):</h4>";
                foreach ($foreignKeys as $fk) {
                    echo "<div class='key'>";
                    echo "<strong>{$fk['CONSTRAINT_NAME']}</strong>: {$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}";
                    echo "</div>";
                }
            }

            echo "<p><strong>📊 Data Rows:</strong> <span class='" . ($rowCount > 0 ? 'success' : 'warning') . "'>$rowCount records</span></p>";

            // Generate migration code
            echo "<h4>🔧 Migration Code:</h4>";
            $migrationCode = generateMigrationCode($table, $fields, $processedIndexes, $foreignKeys);
            echo "<div class='migration-code'>" . htmlspecialchars($migrationCode) . "</div>";

            // Store for summary
            $analyzedTables[$table] = [
                'fields' => $fields,
                'indexes' => $processedIndexes,
                'foreign_keys' => $foreignKeys,
                'row_count' => $rowCount,
                'migration_code' => $migrationCode
            ];
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error analyzing table '$table': " . $e->getMessage() . "</p>";
        }

        echo "</div>";
    }

    // Generate complete migration strategy
    echo "<div class='summary'>";
    echo "<h2>🎯 Complete Migration Strategy</h2>";

    $totalRows = array_sum(array_column($analyzedTables, 'row_count'));
    echo "<p><strong>Total Data Rows:</strong> <span class='success'>$totalRows</span></p>";

    echo "<h3>📋 Migration Files to Create:</h3>";
    echo "<ol>";

    $timestamp = date('Y-m-d-His');
    $migrationNumber = 1;

    foreach ($migrationOrder as $table) {
        if (isset($analyzedTables[$table])) {
            $fileTimestamp = date('Y-m-d-His', strtotime("+$migrationNumber seconds"));
            echo "<li><code>php spark make:migration Create" . ucfirst($table) . "Table</code></li>";
            echo "<p style='margin-left: 20px; color: #666;'>File: {$fileTimestamp}_Create" . ucfirst($table) . "Table.php</p>";
            $migrationNumber++;
        }
    }

    echo "</ol>";

    echo "<h3>🚀 Step-by-Step Implementation:</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
    echo "<ol>";
    echo "<li><strong>Backup your database first!</strong><br><code>mysqldump -u root -p digital_signature > backup.sql</code></li>";
    echo "<li><strong>Create migration files:</strong><br>";
    foreach ($migrationOrder as $table) {
        if (isset($analyzedTables[$table])) {
            echo "<code>php spark make:migration Create" . ucfirst($table) . "Table</code><br>";
        }
    }
    echo "</li>";
    echo "<li><strong>Copy generated code</strong> into each migration file</li>";
    echo "<li><strong>Test migration:</strong><br><code>php spark migrate:status</code><br><code>php spark migrate</code></li>";
    echo "<li><strong>Verify:</strong> Check that tables still exist and data is intact</li>";
    echo "</ol>";
    echo "</div>";

    echo "</div>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection error: " . $e->getMessage() . "</p>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Check if XAMPP MySQL is running</li>";
    echo "<li>Verify database name is 'digital_signature'</li>";
    echo "<li>Check username/password (usually root with no password for XAMPP)</li>";
    echo "<li>Make sure database exists in phpMyAdmin</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div></body></html>";

// Migration code generator function
function generateMigrationCode($tableName, $fields, $indexes, $foreignKeys)
{
    $className = 'Create' . ucfirst($tableName) . 'Table';

    $code = "<?php\n\n";
    $code .= "namespace App\\Database\\Migrations;\n\n";
    $code .= "use CodeIgniter\\Database\\Migration;\n\n";
    $code .= "class $className extends Migration\n{\n";
    $code .= "    public function up()\n    {\n";
    $code .= "        // Check if table already exists (for existing database)\n";
    $code .= "        if (\$this->db->tableExists('$tableName')) {\n";
    $code .= "            echo \"Table '$tableName' already exists. Skipping creation.\\n\";\n";
    $code .= "            return;\n";
    $code .= "        }\n\n";
    $code .= "        \$this->forge->addField([\n";

    foreach ($fields as $field) {
        $code .= "            '{$field['Field']}' => [\n";

        // Map field type
        $ciType = mapMySQLTypeToCIType($field['Type']);
        $code .= "                'type' => '$ciType',\n";

        // Add constraint for varchar, char, etc.
        if (preg_match('/\((\d+)\)/', $field['Type'], $matches)) {
            if (!in_array($ciType, ['TEXT', 'BLOB', 'LONGTEXT'])) {
                $code .= "                'constraint' => {$matches[1]},\n";
            }
        }

        // Add unsigned for integers
        if (strpos($field['Type'], 'unsigned') !== false) {
            $code .= "                'unsigned' => true,\n";
        }

        // Add auto increment
        if (strpos($field['Extra'], 'auto_increment') !== false) {
            $code .= "                'auto_increment' => true,\n";
        }

        // Add nullable
        if ($field['Null'] === 'YES') {
            $code .= "                'null' => true,\n";
        }

        // Add default value
        if ($field['Default'] !== null && $field['Default'] !== '') {
            if (is_numeric($field['Default']) || $field['Default'] === 'CURRENT_TIMESTAMP') {
                $code .= "                'default' => '{$field['Default']}',\n";
            } else {
                $code .= "                'default' => '{$field['Default']}',\n";
            }
        }

        $code .= "            ],\n";
    }

    $code .= "        ]);\n\n";

    // Add keys
    foreach ($indexes as $index) {
        if ($index['type'] === 'PRIMARY') {
            $code .= "        \$this->forge->addPrimaryKey('" . $index['fields'][0] . "');\n";
        } elseif ($index['type'] === 'UNIQUE') {
            $fields_str = "'" . implode("', '", $index['fields']) . "'";
            $code .= "        \$this->forge->addUniqueKey([" . $fields_str . "]);\n";
        } elseif ($index['name'] !== 'PRIMARY') {
            $fields_str = "'" . implode("', '", $index['fields']) . "'";
            $code .= "        \$this->forge->addKey([" . $fields_str . "]);\n";
        }
    }

    $code .= "\n        \$this->forge->createTable('$tableName');\n";

    // Add foreign keys
    if (!empty($foreignKeys)) {
        $code .= "\n        // Add foreign keys\n";
        foreach ($foreignKeys as $fk) {
            $code .= "        \$this->forge->addForeignKey('{$fk['COLUMN_NAME']}', '{$fk['REFERENCED_TABLE_NAME']}', '{$fk['REFERENCED_COLUMN_NAME']}', 'CASCADE', 'CASCADE');\n";
        }
    }

    $code .= "    }\n\n";
    $code .= "    public function down()\n    {\n";
    $code .= "        \$this->forge->dropTable('$tableName');\n";
    $code .= "    }\n";
    $code .= "}\n";

    return $code;
}

function mapMySQLTypeToCIType($mysqlType)
{
    $mysqlType = strtoupper($mysqlType);

    if (strpos($mysqlType, 'TINYINT(1)') !== false) return 'TINYINT';
    if (strpos($mysqlType, 'TINYINT') !== false) return 'TINYINT';
    if (strpos($mysqlType, 'SMALLINT') !== false) return 'SMALLINT';
    if (strpos($mysqlType, 'MEDIUMINT') !== false) return 'MEDIUMINT';
    if (strpos($mysqlType, 'BIGINT') !== false) return 'BIGINT';
    if (strpos($mysqlType, 'INT') !== false) return 'INT';
    if (strpos($mysqlType, 'DECIMAL') !== false) return 'DECIMAL';
    if (strpos($mysqlType, 'FLOAT') !== false) return 'FLOAT';
    if (strpos($mysqlType, 'DOUBLE') !== false) return 'DOUBLE';
    if (strpos($mysqlType, 'CHAR') !== false) return 'CHAR';
    if (strpos($mysqlType, 'VARCHAR') !== false) return 'VARCHAR';
    if (strpos($mysqlType, 'TEXT') !== false) return 'TEXT';
    if (strpos($mysqlType, 'BLOB') !== false) return 'BLOB';
    if (strpos($mysqlType, 'DATE') !== false) return 'DATE';
    if (strpos($mysqlType, 'TIME') !== false) return 'TIME';
    if (strpos($mysqlType, 'DATETIME') !== false) return 'DATETIME';
    if (strpos($mysqlType, 'TIMESTAMP') !== false) return 'TIMESTAMP';
    if (strpos($mysqlType, 'ENUM') !== false) return 'ENUM';
    if (strpos($mysqlType, 'SET') !== false) return 'SET';

    return 'VARCHAR'; // Default fallback
}
