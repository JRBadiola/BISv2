<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateChatMessageSenderEnum extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('chat_messages');

        if (!is_array($fields) || !in_array('sender', $fields, true)) {
            return;
        }

        // Human-support messages are stored as sender='staff'.
        // Keep the existing AI/user values and add the staff value.
        $this->db->query(
            "ALTER TABLE chat_messages
             MODIFY COLUMN sender ENUM('user','assistant','staff') NOT NULL"
        );
    }

    public function down()
    {
        // Do not silently destroy existing staff messages during rollback.
        // Convert any staff messages back to assistant before restoring the
        // original enum definition.
        $this->db->query(
            "UPDATE chat_messages
             SET sender = 'assistant'
             WHERE sender = 'staff'"
        );

        $this->db->query(
            "ALTER TABLE chat_messages
             MODIFY COLUMN sender ENUM('user','assistant') NOT NULL"
        );
    }
}
