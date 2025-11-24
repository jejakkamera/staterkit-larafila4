<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Jobs\BackupDatabaseJob;
use App\Models\BackupProgress;

class DatabaseBackup extends Component
{
    public $backups = [];
    public $isBackingUp = false;
    public $backupMessage = '';
    public $backupStatus = ''; // 'success', 'error', 'info'
    public $activeBackupId = null;
    public $activeBackupProgress = null;
    public $mcpMessages = []; // Chat history

    #[On('refresh-backup-list')]
    public function refreshBackupList()
    {
        $this->loadBackups();
    }

    public function mount()
    {
        $this->loadBackups();
    }

    public function loadBackups()
    {
        $this->backups = [];
        $backupPath = storage_path('app/backups');

        if (is_dir($backupPath)) {
            $files = array_diff(scandir($backupPath), array('.', '..'));
            
            foreach ($files as $file) {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['sql', 'gz'])) {
                    $filePath = $backupPath . '/' . $file;
                    $this->backups[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'date' => filemtime($filePath),
                        'humanDate' => Carbon::createFromTimestamp(filemtime($filePath))->format('d M Y H:i:s'),
                    ];
                }
            }

            // Sort by date descending (newest first)
            usort($this->backups, function($a, $b) {
                return $b['date'] - $a['date'];
            });
        }

        // Check for active backup progress
        $this->checkActiveBackup();
    }

    public function checkActiveBackup()
    {
        if ($this->activeBackupId) {
            $progress = BackupProgress::where('backup_id', $this->activeBackupId)->first();
            if ($progress) {
                $this->activeBackupProgress = $progress;
                if ($progress->status !== 'in_progress') {
                    $this->isBackingUp = false;
                    if ($progress->status === 'completed') {
                        $this->backupMessage = $progress->message;
                        $this->backupStatus = 'success';
                        // Reload backups list
                        $this->loadBackups();
                    } else {
                        $this->backupMessage = $progress->message;
                        $this->backupStatus = 'error';
                    }
                    $this->activeBackupId = null;
                }
            }
        }
    }

    public function createBackup()
    {
        $this->isBackingUp = true;
        $this->backupMessage = 'Queuing backup task...';
        $this->backupStatus = 'info';

        try {
            // Create backups directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Generate backup ID
            $backupId = \Illuminate\Support\Str::uuid()->toString();
            $this->activeBackupId = $backupId;

            // Create initial progress record
            BackupProgress::create([
                'backup_id' => $backupId,
                'status' => 'pending',
                'progress' => 0,
                'message' => 'Backup queued and waiting to start...',
            ]);

            // Dispatch job ke queue
            BackupDatabaseJob::dispatch();

            $this->backupMessage = 'Backup task queued! Check progress below.';
            $this->backupStatus = 'info';
            
            // Initial check to load progress
            $this->checkActiveBackup();
        } catch (\Exception $e) {
            $this->backupMessage = 'Error: ' . $e->getMessage();
            $this->backupStatus = 'error';
            $this->isBackingUp = false;
        }
    }

    public function downloadBackup($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (file_exists($backupPath)) {
            return response()->download($backupPath, $filename);
        }

        $this->backupMessage = 'Backup file not found!';
        $this->backupStatus = 'error';
    }

    public function deleteBackup($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);

            if (file_exists($backupPath)) {
                unlink($backupPath);
                $this->backupMessage = 'Backup deleted successfully!';
                $this->backupStatus = 'success';
                $this->loadBackups();
            } else {
                $this->backupMessage = 'Backup file not found!';
                $this->backupStatus = 'error';
            }
        } catch (\Exception $e) {
            $this->backupMessage = 'Error deleting backup: ' . $e->getMessage();
            $this->backupStatus = 'error';
        }
    }

    #[On('poll-backup')]
    public function pollBackup()
    {
        $this->checkActiveBackup();
    }

    #[On('send-mcp-message')]
    public function handleMcpMessage($message)
    {
        if (empty($message)) return;

        try {
            // Detect user intent and perform immediate actions
            $this->performMcpAction($message);
            
            // Call OpenAI with MCP context and current state
            $response = $this->callMcpWithOpenAI($message);
            
            // Store response in chat history
            $this->mcpMessages[] = ['role' => 'assistant', 'content' => $response];
            
            // Livewire will automatically update the view since $mcpMessages changed
            // The modal will re-render with new messages via Livewire's update
            
            return $response;
        } catch (\Exception $e) {
            // Store error in chat history
            $this->mcpMessages[] = ['role' => 'error', 'content' => 'Error: ' . $e->getMessage()];
            
            throw $e;
        }
    }

    private function performMcpAction($message)
    {
        $lowerMessage = strtolower($message);
        
        // Detect list backups intent
        if (strpos($lowerMessage, 'list') !== false || 
            strpos($lowerMessage, 'show') !== false ||
            strpos($lowerMessage, 'daftar') !== false ||
            strpos($lowerMessage, 'tampilkan') !== false ||
            strpos($lowerMessage, 'cek') !== false ||
            strpos($lowerMessage, 'check') !== false) {
            $this->loadBackups();
        }
        
        // Detect create backup intent
        if (strpos($lowerMessage, 'create') !== false || 
            strpos($lowerMessage, 'buat') !== false ||
            strpos($lowerMessage, 'backup now') !== false) {
            $this->createBackup();
        }
    }

    private function callMcpWithOpenAI($userMessage)
    {
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }

        // Build context about available tools
        $toolsContext = $this->buildToolsContext();
        
        // Add current backup state to context
        $backupInfo = $this->formatBackupInfo();

        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a helpful Database Backup Manager assistant. You have access to these tools via MCP:\n\n{$toolsContext}\n\nCurrent system status:\n{$backupInfo}\n\nWhen the user asks to perform an action, confirm what you're doing and provide clear, friendly responses. Always provide actual data when available.",
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        
        return $data['choices'][0]['message']['content'] ?? 'No response from AI';
    }

    private function formatBackupInfo()
    {
        $info = "Database: " . env('DB_DATABASE', 'Not configured') . "\n";
        $info .= "Current Backups:\n";
        
        if (empty($this->backups)) {
            $info .= "- No backups available yet\n";
        } else {
            foreach ($this->backups as $backup) {
                $size = $this->formatBytes($backup['size']);
                $info .= "- {$backup['name']} ({$size}) - {$backup['humanDate']}\n";
            }
            $totalSize = array_sum(array_column($this->backups, 'size'));
            $info .= "\nTotal backup storage: " . $this->formatBytes($totalSize) . "\n";
        }
        
        $info .= "Backup status: " . ($this->isBackingUp ? "Backing up..." : "Idle") . "\n";
        
        return $info;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function buildToolsContext()
    {
        return <<<'EOT'
1. **create_backup**: Create a new database backup
   - Parameters: description (optional)
   - Example: "Create backup of database before major update"

2. **list_backups**: List all available backups
   - Parameters: limit (optional, default 10)
   - Shows: filename, size, date

3. **delete_backup**: Delete a specific backup
   - Parameters: filename (required)
   - Safety: Validates filename to prevent errors

4. **get_backup_info**: Get detailed info about a backup
   - Parameters: backup_id or filename
   - Shows: progress status, size, creation date

Common tasks:
- "Create a backup" → uses create_backup tool
- "Show all backups" → uses list_backups tool  
- "Delete the oldest backup" → needs list_backups first, then delete_backup
- "How much space do backups use?" → uses list_backups tool
EOT;
    }

    public function render()
    {
        return view('livewire.admin.database-backup');
    }
}
