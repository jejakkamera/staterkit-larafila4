<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupProgress extends Model
{
    protected $fillable = [
        'backup_id',
        'status',
        'progress',
        'message',
        'filename',
        'file_size',
        'started_at',
        'completed_at',
    ];
}
