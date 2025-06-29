<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePictureToUsers extends Migration
{
    public function up()
    {
        echo "🔧 Adding profile_picture column to users table...\n";

        // Simple approach: Try to add column, catch error if exists
        try {
            $this->forge->addColumn('users', [
                'profile_picture' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'comment' => 'Path to user profile picture'
                ]
            ]);

            echo "✅ Profile picture column added successfully!\n";

            // Verify with simple query
            try {
                $result = $this->db->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
                if ($result->getNumRows() > 0) {
                    echo "✅ Verified: Column 'profile_picture' exists in users table.\n";
                } else {
                    echo "⚠️  Warning: Could not verify column addition.\n";
                }
            } catch (\Exception $e) {
                echo "⚠️  Could not verify column: " . $e->getMessage() . "\n";
            }
        } catch (\Exception $e) {
            // Column might already exist
            if (
                strpos($e->getMessage(), 'Duplicate column') !== false ||
                strpos($e->getMessage(), 'already exists') !== false
            ) {
                echo "⚠️  Column 'profile_picture' already exists. Skipping addition.\n";
            } else {
                echo "❌ Error adding column: " . $e->getMessage() . "\n";
                throw $e; // Re-throw if it's a different error
            }
        }
    }

    public function down()
    {
        echo "🔙 Rolling back: Removing profile_picture column from users table...\n";

        try {
            $this->forge->dropColumn('users', 'profile_picture');
            echo "✅ Profile picture column removed successfully!\n";

            // Verify removal
            try {
                $result = $this->db->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
                if ($result->getNumRows() === 0) {
                    echo "✅ Verified: Column 'profile_picture' removed from users table.\n";
                } else {
                    echo "⚠️  Warning: Column might still exist.\n";
                }
            } catch (\Exception $e) {
                echo "⚠️  Could not verify column removal: " . $e->getMessage() . "\n";
            }
        } catch (\Exception $e) {
            // Column might not exist
            if (
                strpos($e->getMessage(), "Can't DROP") !== false ||
                strpos($e->getMessage(), "doesn't exist") !== false
            ) {
                echo "⚠️  Column 'profile_picture' doesn't exist. Nothing to remove.\n";
            } else {
                echo "❌ Error removing column: " . $e->getMessage() . "\n";
                throw $e; // Re-throw if it's a different error
            }
        }
    }
}
