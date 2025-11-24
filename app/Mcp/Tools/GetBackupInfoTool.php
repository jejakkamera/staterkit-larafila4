<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Tool;
use App\Models\BackupProgress;
use Carbon\Carbon;

class GetBackupInfoTool extends Tool
{
    public string $name = 'get_backup_info';
    
    public string $description = 'Get detailed information about a specific backup or the latest backup progress.';
    
    /**
     * @var array<string, array>
     */
    public array $inputSchema = [
        'type' => 'object',
        'properties' => [
            'backup_id' => [
                'type' => 'string',
                'description' => 'Optional UUID of the backup to get info about',
            ],
            'filename' => [
                'type' => 'string',
                'description' => 'Optional filename of the backup',
            ],
        ],
        'required' => [],
    ];
    
    public function execute(array $input = []): array
    {
        try {
            $backupId = $input['backup_id'] ?? null;
            $filename = $input['filename'] ?? null;

            // If backup_id provided, get progress info
            if ($backupId) {
                $progress = BackupProgress::where('backup_id', $backupId)->first();
                if ($progress) {
                    return [
                        'success' => true,
                        'type' => 'progress',
                        'backup_id' => $progress->backup_id,
                        'status' => $progress->status,
                        'progress_percentage' => $progress->progress,
                        'message' => $progress->message,
                        'updated_at' => $progress->updated_at->format('d M Y H:i:s'),
                    ];
                }
            }

            // If filename provided, get file info
            if ($filename) {
                // Validate filename
                if (strpos($filename, '..') !== false || strpos($filename, '/') !== false) {
                    return [
                        'success' => false,
                        'error' => 'Invalid filename',
                    ];
                }

                $backupPath = storage_path('app/backups/' . $filename);
                if (file_exists($backupPath)) {
                    return [
                        'success' => true,
                        'type' => 'file',
                        'filename' => $filename,
                        'size_bytes' => filesize($backupPath),
                        'size_human' => $this->humanFilesize(filesize($backupPath)),
                        'created_at' => Carbon::createFromTimestamp(filemtime($backupPath))->format('d M Y H:i:s'),
                        'created_timestamp' => filemtime($backupPath),
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'No backup_id or filename provided',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function humanFilesize($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
