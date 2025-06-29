<?php

// ================================================================
// FILE 4: CreateAuditLogsTable.php
// ================================================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        echo "Baseline migration: Audit logs table already exists in production database.\n";
        echo "Marking migration as completed for tracking purposes.\n";

        try {
            $result = $this->db->query("SELECT COUNT(*) as count FROM audit_logs");
            $count = $result->getRow()->count;
            echo "Verified: Audit logs table contains $count records.\n";
        } catch (\Exception $e) {
            echo "Warning: Could not verify audit_logs table: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        echo "Baseline migration rollback: Would drop audit_logs table.\n";
        echo "SKIPPING: This would destroy production data.\n";
    }
}

// ================================================================
// ALTERNATIVE: Single Baseline Migration File
// ================================================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BaselineDatabaseMigration extends Migration
{
    public function up()
    {
        echo "🗄️ Baseline Migration: Initializing migration system for existing database.\n";
        echo "📊 Database: digital_signature\n";
        echo "📅 Date: " . date('Y-m-d H:i:s') . "\n\n";

        // Tables that should exist
        $expectedTables = ['users', 'documents', 'signatures', 'audit_logs'];
        $totalRecords = 0;

        echo "📋 Verifying existing tables:\n";

        foreach ($expectedTables as $table) {
            try {
                $result = $this->db->query("SELECT COUNT(*) as count FROM `$table`");
                $count = $result->getRow()->count;
                $totalRecords += $count;
                echo "  ✅ $table: $count records\n";
            } catch (\Exception $e) {
                echo "  ❌ $table: ERROR - " . $e->getMessage() . "\n";
                throw new \Exception("Required table '$table' not found or inaccessible.");
            }
        }

        echo "\n📊 Total records in database: $totalRecords\n";
        echo "✅ Baseline migration completed successfully.\n";
        echo "🚀 Future database changes will be tracked through migrations.\n";
    }

    public function down()
    {
        echo "⚠️  Baseline migration rollback attempted.\n";
        echo "🛑 SKIPPING: Cannot rollback baseline migration as it would destroy production data.\n";
        echo "ℹ️  To reset migration system, manually truncate 'migrations' table.\n";
    }
}
