<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\BackupProgress;
use Illuminate\Support\Str;

class BackupDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $backupPath;
    protected $timestamp;
    protected $backupId;

    public function __construct()
    {
        $this->backupId = Str::uuid()->toString();
        $this->timestamp = now()->format('Y-m-d_H-i-s');
        $this->backupPath = storage_path('app/backups');
    }

    public function handle()
    {
        try {
            // Create progress record
            $progress = BackupProgress::create([
                'backup_id' => $this->backupId,
                'status' => 'in_progress',
                'progress' => 10,
                'message' => 'Initializing backup...',
                'started_at' => now(),
            ]);

            // Create backups directory if it doesn't exist
            if (!is_dir($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            $progress->update([
                'progress' => 25,
                'message' => 'Creating database backup...',
            ]);

            $dbConnection = env('DB_CONNECTION', 'sqlite');
            
            if ($dbConnection === 'sqlite') {
                // Backup SQLite database
                $this->backupSqlite($progress);
            } else {
                // Backup MySQL database
                $this->backupMysql($progress);
            }
        } catch (\Exception $e) {
            $progress->update([
                'status' => 'failed',
                'progress' => 0,
                'message' => 'Error: ' . $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error('Database backup job error', [
                'backup_id' => $this->backupId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    protected function backupSqlite(&$progress)
    {
        // Untuk SQLite, cari file database.sqlite di database directory
        $dbPath = null;
        
        // Cek beberapa lokasi umum
        $possiblePaths = [
            database_path('database.sqlite'),
            database_path(env('DB_DATABASE')),
            env('DB_DATABASE'),
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path) && is_file($path)) {
                $dbPath = $path;
                break;
            }
        }
        
        if (!$dbPath) {
            throw new \Exception('SQLite database file not found. Checked: ' . implode(', ', $possiblePaths));
        }

        $backupFile = $this->backupPath . '/sqlite_' . $this->timestamp . '.db.gz';

        // Copy SQLite database and compress it
        $command = sprintf(
            'gzip -c %s > %s',
            escapeshellarg($dbPath),
            escapeshellarg($backupFile)
        );

        Log::info('Starting SQLite backup', [
            'backup_id' => $this->backupId,
            'database' => $dbPath,
            'timestamp' => $this->timestamp,
        ]);

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($backupFile)) {
            $fileSize = filesize($backupFile);
            
            $progress->update([
                'status' => 'completed',
                'progress' => 100,
                'message' => 'Backup completed successfully!',
                'filename' => basename($backupFile),
                'file_size' => $fileSize,
                'completed_at' => now(),
            ]);

            Log::info('SQLite backup completed successfully', [
                'backup_id' => $this->backupId,
                'file' => basename($backupFile),
                'size' => $fileSize,
            ]);
        } else {
            $errorMessage = implode("\n", $output);
            
            $progress->update([
                'status' => 'failed',
                'progress' => 0,
                'message' => 'Backup failed: ' . ($errorMessage ?: 'Unknown error'),
                'completed_at' => now(),
            ]);

            Log::error('SQLite backup failed', [
                'backup_id' => $this->backupId,
                'error' => $errorMessage,
                'return_code' => $returnCode,
            ]);
            
            throw new \Exception('Backup failed: ' . $errorMessage);
        }
    }

    protected function backupMysql(&$progress)
    {
        $host = env('DB_HOST');
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        
        $backupFile = $this->backupPath . '/' . $database . '_' . $this->timestamp . '.sql.gz';

        // Gunakan --password tanpa spasi untuk mysqldump
        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s 2>/dev/null | gzip > %s',
            escapeshellarg($host),
            escapeshellarg($user),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backupFile)
        );

        Log::info('Starting MySQL backup', [
            'backup_id' => $this->backupId,
            'database' => $database,
            'timestamp' => $this->timestamp,
        ]);

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($backupFile)) {
            $fileSize = filesize($backupFile);
            
            $progress->update([
                'status' => 'completed',
                'progress' => 100,
                'message' => 'Backup completed successfully!',
                'filename' => basename($backupFile),
                'file_size' => $fileSize,
                'completed_at' => now(),
            ]);

            Log::info('MySQL backup completed successfully', [
                'backup_id' => $this->backupId,
                'file' => basename($backupFile),
                'size' => $fileSize,
            ]);
        } else {
            $errorMessage = implode("\n", $output);
            
            $progress->update([
                'status' => 'failed',
                'progress' => 0,
                'message' => 'Backup failed: ' . ($errorMessage ?: 'Unknown error'),
                'completed_at' => now(),
            ]);

            Log::error('MySQL backup failed', [
                'backup_id' => $this->backupId,
                'error' => $errorMessage,
                'return_code' => $returnCode,
            ]);
            
            throw new \Exception('Backup failed: ' . $errorMessage);
        }
    }
}
