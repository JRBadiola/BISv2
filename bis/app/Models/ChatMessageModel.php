<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table            = 'chat_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'conversation_id',
        'sender',
        'message',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
}