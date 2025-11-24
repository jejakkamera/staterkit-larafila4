<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;

class DatabaseBackupServer extends Server
{
    public string $serverName = 'DatabaseBackup';
    
    public string $serverVersion = '1.0.0';
    
    public string $instructions = 'Manage database backups for the Laravel application. You can create new backups, list existing backups, delete backups, and get backup information. All backups are stored securely in the storage/app/backups directory.';
    
    /**
     * @var array<class-string>
     */
    public array $tools = [
        \App\Mcp\Tools\CreateBackupTool::class,
        \App\Mcp\Tools\ListBackupsTool::class,
        \App\Mcp\Tools\DeleteBackupTool::class,
        \App\Mcp\Tools\GetBackupInfoTool::class,
    ];
}
