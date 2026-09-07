<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHumanSupportToChat extends Migration
{
    public function up()
    {
        // -------------------------------------------------------------
        // chat_conversations
        // -------------------------------------------------------------
        $conversationFields = $this->db->getFieldNames('chat_conversations');

        if (!in_array('support_mode', $conversationFields, true)) {
            $this->forge->addColumn('chat_conversations', [
                'support_mode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'ai',
                    'null'       => false,
                    'after'      => 'title',
                ],
            ]);
        }

        $conversationFields = $this->db->getFieldNames('chat_conversations');

        if (!in_array('assigned_staff_id', $conversationFields, true)) {
            $this->forge->addColumn('chat_conversations', [
                'assigned_staff_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'support_mode',
                ],
            ]);
        }

        $conversationFields = $this->db->getFieldNames('chat_conversations');

        if (!in_array('last_activity_at', $conversationFields, true)) {
            $this->forge->addColumn('chat_conversations', [
                'last_activity_at' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'default' => null,
                    'after' => 'updated_at',
                ],
            ]);
        }

        $conversationFields = $this->db->getFieldNames('chat_conversations');

        if (!in_array('closed_at', $conversationFields, true)) {
            $this->forge->addColumn('chat_conversations', [
                'closed_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'last_activity_at',
                ],
            ]);
        }

        // -------------------------------------------------------------
        // chat_messages
        // -------------------------------------------------------------
        $messageFields = $this->db->getFieldNames('chat_messages');

        if (!in_array('sender_user_id', $messageFields, true)) {
            $this->forge->addColumn('chat_messages', [
                'sender_user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'sender',
                ],
            ]);
        }
    }

    public function down()
    {
        $conversationFields = $this->db->getFieldNames('chat_conversations');

        $conversationColumns = [];

        foreach ([
            'closed_at',
            'last_activity_at',
            'assigned_staff_id',
            'support_mode',
        ] as $column) {
            if (in_array($column, $conversationFields, true)) {
                $conversationColumns[] = $column;
            }
        }

        if ($conversationColumns !== []) {
            $this->forge->dropColumn(
                'chat_conversations',
                $conversationColumns
            );
        }

        $messageFields = $this->db->getFieldNames('chat_messages');

        if (in_array('sender_user_id', $messageFields, true)) {
            $this->forge->dropColumn(
                'chat_messages',
                'sender_user_id'
            );
        }
    }
}
