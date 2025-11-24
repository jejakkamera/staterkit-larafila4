<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Tool;
use Carbon\Carbon;

class ListBackupsTool extends Tool
{
    public string $name = 'list_backups';
    
    public string $description = 'List all available database backups with their sizes and dates.';
    
    /**
     * @var array<string, array>
     */
    public array $inputSchema = [
        'type' => 'object',
        'properties' => [
            'limit' => [
                'type' => 'integer',
                'description' => 'Maximum number of backups to return (default: 10)',
            ],
        ],
        'required' => [],
    ];
    
    public function execute(array $input = []): array
    {
        try {
            $backups = [];
            $backupPath = storage_path('app/backups');
            $limit = $input['limit'] ?? 10;

            if (is_dir($backupPath)) {
                $files = array_diff(scandir($backupPath), array('.', '..'));
                
                foreach ($files as $file) {
                    if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['sql', 'gz'])) {
                        $filePath = $backupPath . '/' . $file;
                        $backups[] = [
                            'name' => $file,
                            'size_bytes' => filesize($filePath),
                            'size_human' => $this->humanFilesize(filesize($filePath)),
                            'date' => filemtime($filePath),
                            'date_formatted' => Carbon::createFromTimestamp(filemtime($filePath))->format('d M Y H:i:s'),
                        ];
                    }
                }

                // Sort by date descending (newest first)
                usort($backups, function($a, $b) {
                    return $b['date'] - $a['date'];
                });

                // Limit results
                $backups = array_slice($backups, 0, $limit);
            }

            return [
                'success' => true,
                'total' => count($backups),
                'backups' => $backups,
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
