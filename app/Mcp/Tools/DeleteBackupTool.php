<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Tool;

class DeleteBackupTool extends Tool
{
    public string $name = 'delete_backup';
    
    public string $description = 'Delete a specific database backup by filename.';
    
    /**
     * @var array<string, array>
     */
    public array $inputSchema = [
        'type' => 'object',
        'properties' => [
            'filename' => [
                'type' => 'string',
                'description' => 'The backup filename to delete (e.g., "backup_2025-01-15_120000.sql.gz")',
            ],
        ],
        'required' => ['filename'],
    ];
    
    public function execute(array $input = []): array
    {
        try {
            $filename = $input['filename'] ?? '';
            
            // Validate filename to prevent directory traversal
            if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
                return [
                    'success' => false,
                    'error' => 'Invalid filename',
                ];
            }

            $backupPath = storage_path('app/backups/' . $filename);

            if (file_exists($backupPath)) {
                unlink($backupPath);
                return [
                    'success' => true,
                    'message' => 'Backup deleted successfully: ' . $filename,
                    'filename' => $filename,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Backup file not found: ' . $filename,
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
