<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatConversationModel extends Model
{
    protected $table            = 'chat_conversations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement  = true;
    protected $returnType       = 'array';

    /*
    |--------------------------------------------------------------------------
    | Automatic Timestamps
    |--------------------------------------------------------------------------
    |
    | CodeIgniter automatically maintains:
    | created_at
    | updated_at
    |
    */
    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | Allowed Fields
    |--------------------------------------------------------------------------
    */

    protected $allowedFields = [
        'user_id',
        'title',
    ];
}