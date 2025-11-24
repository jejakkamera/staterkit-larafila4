<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Tool;
use Illuminate\Support\Str;
use App\Jobs\BackupDatabaseJob;
use App\Models\BackupProgress;

class CreateBackupTool extends Tool
{
    public string $name = 'create_backup';
    
    public string $description = 'Create a new database backup. Returns backup ID and queued status.';
    
    /**
     * @var array<string, array>
     */
    public array $inputSchema = [
        'type' => 'object',
        'properties' => [
            'description' => [
                'type' => 'string',
                'description' => 'Optional description for this backup (e.g., "Before major update", "Daily backup")',
            ],
        ],
        'required' => [],
    ];
    
    public function execute(array $input = []): array
    {
        try {
            // Create backups directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Generate backup ID
            $backupId = Str::uuid()->toString();
            $description = $input['description'] ?? 'AI-initiated backup via MCP';

            // Create initial progress record
            BackupProgress::create([
                'backup_id' => $backupId,
                'status' => 'pending',
                'progress' => 0,
                'message' => 'Backup queued: ' . $description,
            ]);

            // Dispatch job ke queue
            BackupDatabaseJob::dispatch();

            return [
                'success' => true,
                'backup_id' => $backupId,
                'status' => 'queued',
                'message' => 'Database backup initiated: ' . $description,
                'description' => $description,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to create backup',
            ];
        }
    }
}
