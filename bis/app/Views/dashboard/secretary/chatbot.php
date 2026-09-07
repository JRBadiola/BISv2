<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chatbot & Customer Service - Bacolod BIS</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    >

    <link rel="stylesheet" href="/style.css">

    <style>
        /* ============================================================
           CUSTOMER SERVICE
           ============================================================ */

        .customer-service-wrapper {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 20px;
            min-height: 680px;
        }

        .customer-service-card {
            background: #fff;
            border: 1px solid #e8ebf3;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
        }

        /* ------------------------------------------------------------
           LEFT CONVERSATION LIST
           ------------------------------------------------------------ */

        .customer-service-sidebar {
            display: flex;
            flex-direction: column;
            min-height: 680px;
        }

        .customer-service-sidebar-header {
            padding: 18px 18px 14px;
            border-bottom: 1px solid #edf0f6;
        }

        .customer-service-sidebar-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #222;
            margin-bottom: 14px;
        }

        .customer-service-count {
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff3cd;
            color: #946200;
            font-size: 12px;
            font-weight: 700;
        }

        .customer-service-search {
            position: relative;
        }

        .customer-service-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa2b1;
            font-size: 13px;
        }

        .customer-service-search input {
            width: 100%;
            height: 38px;
            border: 1px solid #e4e8f0;
            border-radius: 10px;
            padding: 0 12px 0 34px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
        }

        .customer-service-search input:focus {
            border-color: #5b6fd6;
        }

        .customer-service-tabs {
            display: flex;
            border-bottom: 1px solid #edf0f6;
        }

        .customer-service-tab {
            flex: 1;
            border: none;
            background: transparent;
            padding: 12px 8px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: #7b8392;
            cursor: pointer;
        }

        .customer-service-tab.active {
            color: #5b6fd6;
            border-bottom: 2px solid #5b6fd6;
        }

        .customer-service-conversation-list {
            flex: 1;
            overflow-y: auto;
        }

        .customer-service-conversation {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px;
            border-bottom: 1px solid #f0f2f6;
            cursor: pointer;
            transition: background .15s ease;
        }

        .customer-service-conversation:hover {
            background: #f8f9fc;
        }

        .customer-service-conversation.active {
            background: #f1f3ff;
        }

        .customer-service-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            flex: 0 0 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8ebff;
            color: #5b6fd6;
            font-size: 13px;
            font-weight: 700;
        }

        .customer-service-conversation-body {
            min-width: 0;
            flex: 1;
        }

        .customer-service-conversation-top {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }

        .customer-service-name {
            font-size: 13px;
            font-weight: 700;
            color: #252a33;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-service-time {
            font-size: 10px;
            color: #9aa2b1;
            white-space: nowrap;
        }

        .customer-service-preview {
            font-size: 11px;
            color: #7d8491;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-service-status {
            margin-top: 6px;
        }

        .customer-service-empty {
            padding: 45px 20px;
            text-align: center;
            color: #9aa2b1;
            font-size: 12px;
        }

        /* ------------------------------------------------------------
           RIGHT CHAT
           ------------------------------------------------------------ */

        .customer-service-chat {
            display: flex;
            flex-direction: column;
            min-height: 680px;
        }

        .customer-service-chat-header {
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 1px solid #edf0f6;
        }

        .customer-service-chat-user {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
        }

        .customer-service-chat-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e8ebff;
            color: #5b6fd6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .customer-service-chat-user-info {
            min-width: 0;
        }

        .customer-service-chat-user-name {
            font-size: 14px;
            font-weight: 700;
            color: #252a33;
        }

        .customer-service-chat-user-status {
            font-size: 11px;
            color: #16a67d;
            margin-top: 2px;
        }

        .customer-service-chat-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .customer-service-btn {
            border: none;
            border-radius: 9px;
            padding: 9px 12px;
            font-family: inherit;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: .15s ease;
        }

        .customer-service-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .customer-service-btn-primary {
            background: #5b6fd6;
            color: #fff;
        }

        .customer-service-btn-primary:hover:not(:disabled) {
            background: #4c60c7;
        }

        .customer-service-btn-success {
            background: #16c79a;
            color: #fff;
        }

        .customer-service-btn-warning {
            background: #ffc107;
            color: #4b3900;
        }

        .customer-service-btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .customer-service-btn-light {
            background: #f1f3f7;
            color: #525b6b;
        }

        .customer-service-status-bar {
            padding: 9px 18px;
            background: #fafbfe;
            border-bottom: 1px solid #edf0f6;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .customer-service-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #9aa2b1;
        }

        .customer-service-status-dot.waiting {
            background: #ffc107;
        }

        .customer-service-status-dot.human {
            background: #16c79a;
        }

        .customer-service-status-dot.ai {
            background: #5b6fd6;
        }

        .customer-service-status-dot.closed {
            background: #dc3545;
        }

        .customer-service-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f7f8fb;
        }

        .customer-service-no-conversation {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #9aa2b1;
            text-align: center;
            padding: 30px;
        }

        .customer-service-no-conversation i {
            font-size: 42px;
            margin-bottom: 12px;
            opacity: .45;
        }

        .customer-service-no-conversation-title {
            font-size: 14px;
            font-weight: 600;
            color: #60697a;
            margin-bottom: 4px;
        }

        .customer-service-no-conversation-text {
            font-size: 11px;
        }

        .customer-service-message-row {
            margin-bottom: 12px;
            display: flex;
        }

        .customer-service-message-row.resident {
            justify-content: flex-start;
        }

        .customer-service-message-row.staff,
        .customer-service-message-row.ai {
            justify-content: flex-end;
        }

        .customer-service-message-content {
            max-width: 72%;
        }

        .customer-service-message-bubble {
            padding: 10px 13px;
            border-radius: 13px;
            font-size: 12px;
            line-height: 1.55;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .customer-service-message-row.resident
        .customer-service-message-bubble {
            background: #fff;
            color: #333;
            border: 1px solid #e7eaf1;
            border-bottom-left-radius: 4px;
        }

        .customer-service-message-row.staff
        .customer-service-message-bubble {
            background: #5b6fd6;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .customer-service-message-row.ai
        .customer-service-message-bubble {
            background: #e8ebff;
            color: #343c5c;
            border-bottom-right-radius: 4px;
        }

        .customer-service-message-meta {
            margin-top: 4px;
            font-size: 9px;
            color: #a0a7b3;
        }

        .customer-service-message-row.staff
        .customer-service-message-meta,
        .customer-service-message-row.ai
        .customer-service-message-meta {
            text-align: right;
        }

        .customer-service-composer {
            padding: 13px;
            border-top: 1px solid #edf0f6;
            background: #fff;
        }

        .customer-service-composer-row {
            display: flex;
            align-items: flex-end;
            gap: 9px;
        }

        .customer-service-composer textarea {
            flex: 1;
            min-height: 42px;
            max-height: 120px;
            resize: vertical;
            border: 1px solid #e2e6ee;
            border-radius: 10px;
            padding: 10px 12px;
            font-family: inherit;
            font-size: 12px;
            outline: none;
        }

        .customer-service-composer textarea:focus {
            border-color: #5b6fd6;
        }

        .customer-service-send {
            height: 42px;
            min-width: 88px;
        }

        /* ------------------------------------------------------------
           LOG TABLE
           ------------------------------------------------------------ */

        .customer-service-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 28px 0 16px;
        }

        .customer-service-section-heading h3 {
            margin: 0;
            font-size: 17px;
            color: #252a33;
        }

        .customer-service-section-heading p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #8b93a1;
        }

        /* ------------------------------------------------------------
           RESPONSIVE
           ------------------------------------------------------------ */

        @media (max-width: 900px) {
            .customer-service-wrapper {
                grid-template-columns: 1fr;
            }

            .customer-service-sidebar,
            .customer-service-chat {
                min-height: 500px;
            }
        }

        @media (max-width: 600px) {
            .customer-service-chat-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .customer-service-chat-actions {
                justify-content: flex-start;
            }

            .customer-service-message-content {
                max-width: 88%;
            }
        }
    </style>
</head>

<body
    class="db-body"
    data-role="secretary"
>

<?php
$role = 'secretary';
$active = 'chatbot';
$pageTitle = 'Chatbot & Customer Service';

include(APPPATH . 'Views/dashboard/sidebar.php');
?>

<div class="db-main">

    <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>

    <div class="db-content">

        <!-- ============================================================
             STATISTICS
             ============================================================ -->

        <div
            class="db-stats"
            style="margin-bottom:24px;"
        >

            <div class="db-stat-card">
                <div
                    class="db-stat-icon"
                    style="background:rgba(91,111,214,0.15);color:#5b6fd6;"
                >
                    <i class="fas fa-comments"></i>
                </div>

                <div>
                    <span
                        class="db-stat-num"
                        id="customerServiceTotal"
                    >
                        0
                    </span>

                    <span class="db-stat-label">
                        Active Conversations
                    </span>
                </div>
            </div>


            <div class="db-stat-card">
                <div
                    class="db-stat-icon"
                    style="background:rgba(255,193,7,0.15);color:#ffc107;"
                >
                    <i class="fas fa-user-clock"></i>
                </div>

                <div>
                    <span
                        class="db-stat-num"
                        id="customerServiceWaiting"
                    >
                        0
                    </span>

                    <span class="db-stat-label">
                        Waiting for Staff
                    </span>
                </div>
            </div>


            <div class="db-stat-card">
                <div
                    class="db-stat-icon"
                    style="background:rgba(22,199,154,0.15);color:#16c79a;"
                >
                    <i class="fas fa-headset"></i>
                </div>

                <div>
                    <span
                        class="db-stat-num"
                        id="customerServiceHuman"
                    >
                        0
                    </span>

                    <span class="db-stat-label">
                        Human Support
                    </span>
                </div>
            </div>


            <div class="db-stat-card">
                <div
                    class="db-stat-icon"
                    style="background:rgba(91,111,214,0.15);color:#5b6fd6;"
                >
                    <i class="fas fa-robot"></i>
                </div>

                <div>
                    <span
                        class="db-stat-num"
                        id="customerServiceAI"
                    >
                        0
                    </span>

                    <span class="db-stat-label">
                        AI Active
                    </span>
                </div>
            </div>

        </div>


        <!-- ============================================================
             CUSTOMER SERVICE
             ============================================================ -->

        <div class="customer-service-wrapper">

            <!-- ========================================================
                 CONVERSATIONS
                 ======================================================== -->

            <div class="customer-service-card customer-service-sidebar">

                <div class="customer-service-sidebar-header">

                    <div class="customer-service-sidebar-title">

                        <span>
                            <i
                                class="fas fa-headset"
                                style="margin-right:6px;color:#5b6fd6;"
                            ></i>

                            Customer Service
                        </span>

                        <span
                            class="customer-service-count"
                            id="supportRequestCount"
                        >
                            0
                        </span>

                    </div>

                    <div class="customer-service-search">

                        <i class="fas fa-search"></i>

                        <input
                            type="text"
                            id="supportConversationSearch"
                            placeholder="Search resident..."
                            autocomplete="off"
                        >

                    </div>

                </div>


                <div class="customer-service-tabs">

                    <button
                        type="button"
                        class="customer-service-tab active"
                        data-support-filter="all"
                    >
                        All
                    </button>

                    <button
                        type="button"
                        class="customer-service-tab"
                        data-support-filter="waiting"
                    >
                        Waiting
                    </button>

                    <button
                        type="button"
                        class="customer-service-tab"
                        data-support-filter="human"
                    >
                        Active
                    </button>

                </div>


                <div
                    id="supportConversationList"
                    class="customer-service-conversation-list"
                >

                    <div class="customer-service-empty">
                        Loading customer conversations...
                    </div>

                </div>

            </div>


            <!-- ========================================================
                 CHAT
                 ======================================================== -->

            <div class="customer-service-card customer-service-chat">

                <div class="customer-service-chat-header">

                    <div class="customer-service-chat-user">

                        <div
                            class="customer-service-chat-avatar"
                            id="supportResidentAvatar"
                        >
                            ?
                        </div>

                        <div class="customer-service-chat-user-info">

                            <div
                                class="customer-service-chat-user-name"
                                id="supportResidentName"
                            >
                                Select a conversation
                            </div>

                            <div
                                class="customer-service-chat-user-status"
                                id="supportResidentStatus"
                            >
                                No conversation selected
                            </div>

                        </div>

                    </div>


                    <div class="customer-service-chat-actions">

                        <button
                            type="button"
                            class="customer-service-btn customer-service-btn-success"
                            id="takeOverConversation"
                            disabled
                        >
                            <i class="fas fa-user-check"></i>
                            Take Over
                        </button>

                        <button
                            type="button"
                            class="customer-service-btn customer-service-btn-warning"
                            id="returnToAI"
                            disabled
                        >
                            <i class="fas fa-robot"></i>
                            Return to AI
                        </button>

                        <button
                            type="button"
                            class="customer-service-btn customer-service-btn-danger"
                            id="closeSupportConversation"
                            disabled
                        >
                            <i class="fas fa-times"></i>
                            Close
                        </button>

                    </div>

                </div>


                <div class="customer-service-status-bar">

                    <span
                        id="supportStatusDot"
                        class="customer-service-status-dot"
                    ></span>

                    <span id="supportStatusText">
                        No conversation selected
                    </span>

                </div>


                <div
                    id="supportChatMessages"
                    class="customer-service-messages"
                >

                    <div class="customer-service-no-conversation">

                        <i class="fas fa-comments"></i>

                        <div class="customer-service-no-conversation-title">
                            No conversation selected
                        </div>

                        <div class="customer-service-no-conversation-text">
                            Select a resident conversation from the left.
                        </div>

                    </div>

                </div>


                <div class="customer-service-composer">

                    <div class="customer-service-composer-row">

                        <textarea
                            id="staffMessageInput"
                            placeholder="Type your message to the resident..."
                            disabled
                        ></textarea>

                        <button
                            type="button"
                            id="staffSendButton"
                            class="customer-service-btn customer-service-btn-primary customer-service-send"
                            disabled
                        >
                            <i class="fas fa-paper-plane"></i>
                            Send
                        </button>

                    </div>

                    <div
                        style="
                            margin-top:6px;
                            font-size:10px;
                            color:#9aa2b1;
                        "
                    >
                        Messages sent here are from the Barangay Secretary.
                    </div>

                </div>

            </div>

        </div>


        <!-- ============================================================
             CHATBOT LOGS
             ============================================================ -->

        <div class="customer-service-section-heading">

            <div>
                <h3>
                    Chatbot Conversation Logs
                </h3>

                <p>
                    Previous AI-assisted conversations.
                </p>
            </div>

            <div class="db-search-wrap">

                <i class="fas fa-search"></i>

                <input
                    type="text"
                    id="logSearch"
                    placeholder="Search conversations..."
                >

            </div>

        </div>


        <div class="db-toolbar">

            <div></div>

            <div class="db-toolbar-actions">

                <select
                    id="logTopicFilter"
                    class="db-filter-select"
                >
                    <option value="">
                        All Topics
                    </option>

                    <option value="Clearance">
                        Clearance
                    </option>

                    <option value="Census">
                        Census
                    </option>

                    <option value="Complaints">
                        Complaints
                    </option>

                    <option value="General">
                        General
                    </option>
                </select>

            </div>

        </div>


        <div class="db-table-wrap">

            <table
                class="db-table"
                id="chatbotLogsTable"
            >

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Resident</th>
                        <th>Topic</th>
                        <th>Message Preview</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php

                    /*
                     * These are your existing demo/static logs.
                     *
                     * Your current saveLog/getLogs system can still
                     * be connected here later without affecting
                     * customer service.
                     */

                    $logs = [
                        [
                            '001',
                            'Juan Dela Cruz',
                            'Clearance',
                            'How do I apply for barangay clearance?',
                            'Mar 17, 2026',
                            'Resolved'
                        ],
                        [
                            '002',
                            'Maria Santos',
                            'Census',
                            'How do I update my household information?',
                            'Mar 17, 2026',
                            'Resolved'
                        ],
                        [
                            '003',
                            'Pedro Reyes',
                            'General',
                            'What are the office hours?',
                            'Mar 16, 2026',
                            'Resolved'
                        ],
                        [
                            '004',
                            'Ana Gomez',
                            'Complaints',
                            'I want to file a noise complaint.',
                            'Mar 16, 2026',
                            'Unanswered'
                        ],
                        [
                            '005',
                            'Carlos Lim',
                            'Clearance',
                            'What documents do I need for clearance?',
                            'Mar 15, 2026',
                            'Resolved'
                        ],
                        [
                            '006',
                            'Rosa Bautista',
                            'General',
                            'Is the barangay hall open on Saturday?',
                            'Mar 15, 2026',
                            'Unanswered'
                        ],
                    ];

                    foreach ($logs as $l):
                    ?>

                        <tr>

                            <td><?= esc($l[0]) ?></td>

                            <td>

                                <div class="db-resident-name">

                                    <div class="db-avatar-sm">
                                        <?= strtoupper(
                                            mb_substr(
                                                $l[1],
                                                0,
                                                1
                                            )
                                        ) ?>
                                    </div>

                                    <?= esc($l[1]) ?>

                                </div>

                            </td>

                            <td>
                                <span class="db-tag">
                                    <?= esc($l[2]) ?>
                                </span>
                            </td>

                            <td class="db-text-muted">
                                <?= esc($l[3]) ?>
                            </td>

                            <td>
                                <?= esc($l[4]) ?>
                            </td>

                            <td>

                                <span
                                    class="db-badge
                                    <?= $l[5] === 'Resolved'
                                        ? 'db-badge--approved'
                                        : 'db-badge--pending'
                                    ?>"
                                >
                                    <?= esc($l[5]) ?>
                                </span>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="db-icon-btn db-icon-btn--view"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- ================================================================
     EXISTING SIDEBAR SCRIPT
     ================================================================ -->

<script>
document.querySelectorAll('.db-nav-item').forEach(function (item) {
    item.addEventListener('click', function () {
        var sidebar = document.getElementById('sidebar');

        if (sidebar) {
            sidebar.classList.remove('open');
        }
    });
});
</script>


<!-- ================================================================
     CUSTOMER SERVICE JAVASCRIPT
     ================================================================ -->

<script src="<?= base_url('js/customer_service.js') ?>"></script>


<script>
(function () {

    'use strict';

    /*
     * ---------------------------------------------------------------
     * CUSTOMER SERVICE PAGE CONTROLLER
     * ---------------------------------------------------------------
     */

    const state = {

        role: 'secretary',

        conversations: [],

        selectedConversationId: null,

        currentConversation: null,

        filter: 'all',

        search: ''

    };


    /*
     * ---------------------------------------------------------------
     * ELEMENTS
     * ---------------------------------------------------------------
     */

    const elements = {

        list:
            document.getElementById(
                'supportConversationList'
            ),

        requestCount:
            document.getElementById(
                'supportRequestCount'
            ),

        total:
            document.getElementById(
                'customerServiceTotal'
            ),

        waiting:
            document.getElementById(
                'customerServiceWaiting'
            ),

        human:
            document.getElementById(
                'customerServiceHuman'
            ),

        ai:
            document.getElementById(
                'customerServiceAI'
            ),

        search:
            document.getElementById(
                'supportConversationSearch'
            ),

        messages:
            document.getElementById(
                'supportChatMessages'
            ),

        residentName:
            document.getElementById(
                'supportResidentName'
            ),

        residentStatus:
            document.getElementById(
                'supportResidentStatus'
            ),

        residentAvatar:
            document.getElementById(
                'supportResidentAvatar'
            ),

        statusDot:
            document.getElementById(
                'supportStatusDot'
            ),

        statusText:
            document.getElementById(
                'supportStatusText'
            ),

        takeOver:
            document.getElementById(
                'takeOverConversation'
            ),

        returnAI:
            document.getElementById(
                'returnToAI'
            ),

        close:
            document.getElementById(
                'closeSupportConversation'
            ),

        input:
            document.getElementById(
                'staffMessageInput'
            ),

        send:
            document.getElementById(
                'staffSendButton'
            )

    };


    /*
     * ---------------------------------------------------------------
     * UTILITY
     * ---------------------------------------------------------------
     */

    function escapeHtml(value) {

        var div =
            document.createElement('div');

        div.textContent =
            value == null
                ? ''
                : String(value);

        return div.innerHTML;
    }


    function initials(name) {

        name =
            String(name || 'Resident').trim();

        if (!name) {
            return '?';
        }

        var parts =
            name.split(/\s+/);

        if (parts.length === 1) {
            return parts[0]
                .substring(0, 1)
                .toUpperCase();
        }

        return (
            parts[0].substring(0, 1) +
            parts[parts.length - 1]
                .substring(0, 1)
        ).toUpperCase();
    }


    function modeLabel(mode) {

        switch (mode) {

            case 'waiting_human':
                return 'Waiting for Staff';

            case 'human':
                return 'Human Support Active';

            case 'closed':
                return 'Closed';

            case 'ai':
            default:
                return 'AI Active';

        }
    }


    function notify(message, type) {

        if (
            typeof window.showToast ===
            'function'
        ) {

            window.showToast(
                message,
                type
            );

            return;
        }

        if (
            typeof window.toast ===
            'function'
        ) {

            window.toast(
                message,
                type
            );

            return;
        }

        console.log(
            '[' + type + '] ' + message
        );
    }


    async function api(url, options) {

        options =
            options || {};

        options.credentials =
            'same-origin';

        options.headers =
            Object.assign(
                {
                    'Accept':
                        'application/json'
                },
                options.headers || {}
            );

        const response =
            await fetch(
                url,
                options
            );

        const text =
            await response.text();

        var data = {};

        try {

            data =
                text
                    ? JSON.parse(text)
                    : {};

        } catch (error) {

            throw new Error(
                'The server returned invalid JSON.'
            );

        }

        if (!response.ok) {

            throw new Error(
                data.message ||
                'Request failed (' +
                response.status +
                ').'
            );

        }

        return data;
    }


    /*
     * ---------------------------------------------------------------
     * LOAD SUPPORT QUEUE
     * ---------------------------------------------------------------
     */

    async function loadSupportConversations() {

        try {

            const data =
                await api(
                    '/secretary/chatbot/api/support-conversations'
                );

            state.conversations =
                Array.isArray(
                    data.conversations
                )
                    ? data.conversations
                    : [];

            updateStatistics();

            renderConversationList();

            /*
             * If an active conversation is already selected,
             * refresh it.
             */

            if (
                state.selectedConversationId
            ) {

                await loadSelectedConversation(
                    state.selectedConversationId,
                    true
                );

            }

        } catch (error) {

            console.error(
                'Customer service queue:',
                error
            );

            /*
             * Don't display a popup every 2.5 seconds if the
             * server/database is temporarily unavailable.
             */

        }

    }


    /*
     * ---------------------------------------------------------------
     * STATISTICS
     * ---------------------------------------------------------------
     */

    function updateStatistics() {

        var total =
            state.conversations.length;

        var waiting =
            state.conversations.filter(
                function (conversation) {

                    return (
                        conversation.support_mode ===
                        'waiting_human'
                    );

                }
            ).length;

        var human =
            state.conversations.filter(
                function (conversation) {

                    return (
                        conversation.support_mode ===
                        'human'
                    );

                }
            ).length;

        var ai =
            state.conversations.filter(
                function (conversation) {

                    return (
                        conversation.support_mode ===
                        'ai'
                    );

                }
            ).length;

        elements.total.textContent =
            total;

        elements.waiting.textContent =
            waiting;

        elements.human.textContent =
            human;

        elements.ai.textContent =
            ai;

        elements.requestCount.textContent =
            waiting;

    }


    /*
     * ---------------------------------------------------------------
     * FILTER CONVERSATIONS
     * ---------------------------------------------------------------
     */

    function getFilteredConversations() {

        var search =
            state.search
                .trim()
                .toLowerCase();

        return state.conversations.filter(
            function (conversation) {

                var mode =
                    conversation.support_mode ||
                    'ai';

                if (
                    state.filter === 'waiting' &&
                    mode !== 'waiting_human'
                ) {
                    return false;
                }

                if (
                    state.filter === 'human' &&
                    mode !== 'human'
                ) {
                    return false;
                }

                if (!search) {
                    return true;
                }

                var searchable = (
                    conversation.username ||
                    conversation.name ||
                    conversation.email ||
                    conversation.title ||
                    ''
                ).toLowerCase();

                return searchable.indexOf(search) !== -1;

            }
        );

    }


    /*
     * ---------------------------------------------------------------
     * RENDER LEFT LIST
     * ---------------------------------------------------------------
     */

    function renderConversationList() {

        var conversations =
            getFilteredConversations();

        elements.list.innerHTML = '';

        if (!conversations.length) {

            elements.list.innerHTML =
                '<div class="customer-service-empty">' +
                    '<i class="fas fa-comments"' +
                    ' style="font-size:28px;' +
                    ' display:block;margin-bottom:10px;' +
                    ' opacity:.4;"></i>' +
                    'No customer support conversations.' +
                '</div>';

            return;
        }

        conversations.forEach(
            function (conversation) {

                var item =
                    document.createElement('div');

                item.className =
                    'customer-service-conversation';

                if (
                    Number(
                        conversation.id
                    ) ===
                    Number(
                        state.selectedConversationId
                    )
                ) {

                    item.classList.add(
                        'active'
                    );

                }

                var name =
                    conversation.username ||
                    conversation.name ||
                    conversation.email ||
                    'Resident';

                var preview =
                    conversation.last_message ||
                    conversation.message_preview ||
                    conversation.title ||
                    'No messages yet';

                var mode =
                    conversation.support_mode ||
                    'ai';

                var statusClass =
                    mode === 'waiting_human'
                        ? 'db-badge--pending'
                        : mode === 'human'
                            ? 'db-badge--approved'
                            : '';

                item.innerHTML =

                    '<div class="customer-service-avatar">' +
                        escapeHtml(
                            initials(name)
                        ) +
                    '</div>' +

                    '<div class="customer-service-conversation-body">' +

                        '<div class="customer-service-conversation-top">' +

                            '<div class="customer-service-name">' +
                                escapeHtml(name) +
                            '</div>' +

                            '<div class="customer-service-time">' +
                                escapeHtml(
                                    conversation.updated_at ||
                                    ''
                                ) +
                            '</div>' +

                        '</div>' +

                        '<div class="customer-service-preview">' +
                            escapeHtml(preview) +
                        '</div>' +

                        '<div class="customer-service-status">' +

                            '<span class="db-badge ' +
                                statusClass +
                            '">' +
                                escapeHtml(
                                    modeLabel(mode)
                                ) +
                            '</span>' +

                        '</div>' +

                    '</div>';

                item.addEventListener(
                    'click',
                    function () {

                        loadSelectedConversation(
                            conversation.id
                        );

                    }
                );

                elements.list.appendChild(
                    item
                );

            }
        );

    }


    /*
     * ---------------------------------------------------------------
     * LOAD CONVERSATION
     * ---------------------------------------------------------------
     */

    async function loadSelectedConversation(
        conversationId,
        silent
    ) {

        state.selectedConversationId =
            Number(conversationId);

        var conversation =
            state.conversations.find(
                function (item) {

                    return Number(
                        item.id
                    ) === Number(
                        conversationId
                    );

                }
            );

        if (conversation) {

            renderConversationHeader(
                conversation
            );

        }

        renderConversationList();

        try {

            const data =
                await api(
                    '/secretary/chatbot/api/support-conversation?conversation_id=' +
                    encodeURIComponent(
                        conversationId
                    )
                );

            state.currentConversation =
                data.conversation ||
                conversation ||
                null;

            if (
                state.currentConversation &&
                state.currentConversation.support_mode
            ) {

                /*
                 * Make sure queue state is also current.
                 */

                state.currentConversation
                    .support_mode =
                    state.currentConversation
                        .support_mode;

            }

            renderConversationHeader(
                state.currentConversation ||
                conversation
            );

            renderMessages(
                data.messages || []
            );

            updateActionButtons();

        } catch (error) {

            console.error(
                'Open support conversation:',
                error
            );

            if (!silent) {

                notify(
                    error.message ||
                    'Unable to open the conversation.',
                    'error'
                );

            }

        }

    }


    /*
     * ---------------------------------------------------------------
     * HEADER
     * ---------------------------------------------------------------
     */

    function renderConversationHeader(
        conversation
    ) {

        if (!conversation) {

            elements.residentName.textContent =
                'Select a conversation';

            elements.residentStatus.textContent =
                'No conversation selected';

            elements.residentAvatar.textContent =
                '?';

            elements.statusDot.className =
                'customer-service-status-dot';

            elements.statusText.textContent =
                'No conversation selected';

            return;

        }

        var name =
            conversation.username ||
            conversation.name ||
            conversation.email ||
            'Resident';

        var mode =
            conversation.support_mode ||
            'ai';

        elements.residentName.textContent =
            name;

        elements.residentAvatar.textContent =
            initials(name);

        elements.residentStatus.textContent =
            modeLabel(mode);

        elements.statusText.textContent =
            modeLabel(mode);

        elements.statusDot.className =
            'customer-service-status-dot ' +
            (
                mode === 'waiting_human'
                    ? 'waiting'
                    : mode === 'human'
                        ? 'human'
                        : mode === 'closed'
                            ? 'closed'
                            : 'ai'
            );

    }


    /*
     * ---------------------------------------------------------------
     * RENDER MESSAGES
     * ---------------------------------------------------------------
     */

    function renderMessages(
        messages
    ) {

        if (!messages.length) {

            elements.messages.innerHTML =
                '<div class="customer-service-no-conversation">' +
                    '<i class="fas fa-comment-slash"></i>' +
                    '<div class="customer-service-no-conversation-title">' +
                        'No messages yet' +
                    '</div>' +
                    '<div class="customer-service-no-conversation-text">' +
                        'There are no messages in this conversation.' +
                    '</div>' +
                '</div>';

            return;

        }

        elements.messages.innerHTML = '';

        messages.forEach(
            function (message) {

                var sender =
                    String(
                        message.sender ||
                        ''
                    ).toLowerCase();

                var type =
                    sender === 'staff'
                        ? 'staff'
                        : sender === 'user'
                            ? 'resident'
                            : 'ai';

                var text =
                    message.message ||
                    message.content ||
                    '';

                var metaLabel =
                    type === 'staff'
                        ? 'Secretary'
                        : type === 'resident'
                            ? 'Resident'
                            : 'BIS Assistant';

                var row =
                    document.createElement('div');

                row.className =
                    'customer-service-message-row ' +
                    type;

                row.innerHTML =

                    '<div class="customer-service-message-content">' +

                        '<div class="customer-service-message-bubble">' +
                            escapeHtml(text) +
                        '</div>' +

                        '<div class="customer-service-message-meta">' +
                            escapeHtml(metaLabel) +

                            (
                                message.created_at
                                    ? ' â€¢ ' +
                                      escapeHtml(
                                          message.created_at
                                      )
                                    : ''
                            ) +

                        '</div>' +

                    '</div>';

                elements.messages.appendChild(
                    row
                );

            }
        );

        elements.messages.scrollTop =
            elements.messages.scrollHeight;

    }


    /*
     * ---------------------------------------------------------------
     * ACTION BUTTONS
     * ---------------------------------------------------------------
     */

    function updateActionButtons() {

        if (
            !state.currentConversation
        ) {

            elements.takeOver.disabled =
                true;

            elements.returnAI.disabled =
                true;

            elements.close.disabled =
                true;

            elements.input.disabled =
                true;

            elements.send.disabled =
                true;

            return;

        }

        var mode =
            state.currentConversation
                .support_mode ||
            'ai';

        var assigned =
            state.currentConversation
                .assigned_staff_id;

        /*
         * Waiting:
         *
         * Secretary can take over.
         */

        elements.takeOver.disabled =
            mode !== 'waiting_human';

        /*
         * Human:
         *
         * Secretary can send,
         * return to AI,
         * or close.
         */

        elements.returnAI.disabled =
            mode !== 'human';

        elements.close.disabled =
            (
                mode !== 'human' &&
                mode !== 'waiting_human'
            );

        elements.input.disabled =
            mode !== 'human';

        elements.send.disabled =
            mode !== 'human';

        /*
         * If this conversation is assigned to this secretary,
         * display a small active state.
         */

        if (
            mode === 'human' &&
            assigned
        ) {

            elements.residentStatus.textContent =
                'You are assisting this resident';

        }

    }


    /*
     * ---------------------------------------------------------------
     * TAKE OVER
     * ---------------------------------------------------------------
     */

    async function takeOver() {

        if (
            !state.selectedConversationId
        ) {
            return;
        }

        elements.takeOver.disabled =
            true;

        try {

            const data =
                await api(
                    '/secretary/chatbot/api/take-over',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body: JSON.stringify({
                            conversation_id:
                                state.selectedConversationId
                        })
                    }
                );

            notify(
                'You are now connected to the resident.',
                'success'
            );

            await loadSupportConversations();

            await loadSelectedConversation(
                state.selectedConversationId
            );

        } catch (error) {

            elements.takeOver.disabled =
                false;

            notify(
                error.message ||
                'Unable to take over the conversation.',
                'error'
            );

        }

    }


    /*
     * ---------------------------------------------------------------
     * SEND STAFF MESSAGE
     * ---------------------------------------------------------------
     */

    async function sendStaffMessage() {

        if (
            !state.selectedConversationId
        ) {
            return;
        }

        var message =
            elements.input.value.trim();

        if (!message) {
            return;
        }

        if (
            !state.currentConversation ||
            state.currentConversation.support_mode !==
            'human'
        ) {

            notify(
                'Take over the conversation before sending a message.',
                'warning'
            );

            return;

        }

        elements.send.disabled =
            true;

        try {

            await api(
                '/secretary/chatbot/api/staff-message',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({
                        conversation_id:
                            state.selectedConversationId,

                        message:
                            message
                    })
                }
            );

            elements.input.value =
                '';

            await loadSelectedConversation(
                state.selectedConversationId,
                true
            );

        } catch (error) {

            notify(
                error.message ||
                'Unable to send the message.',
                'error'
            );

        } finally {

            elements.send.disabled =
                (
                    !state.currentConversation ||
                    state.currentConversation.support_mode !==
                    'human'
                );

        }

    }


    /*
     * ---------------------------------------------------------------
     * RETURN TO AI
     * ---------------------------------------------------------------
     */

    async function returnToAI() {

        if (
            !state.selectedConversationId
        ) {
            return;
        }

        elements.returnAI.disabled =
            true;

        try {

            await api(
                '/secretary/chatbot/api/return-to-ai',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({
                        conversation_id:
                            state.selectedConversationId
                    })
                }
            );

            notify(
                'Conversation returned to the AI assistant.',
                'success'
            );

            await loadSupportConversations();

            await loadSelectedConversation(
                state.selectedConversationId
            );

        } catch (error) {

            notify(
                error.message ||
                'Unable to return the conversation to AI.',
                'error'
            );

        }

    }


    /*
     * ---------------------------------------------------------------
     * CLOSE SUPPORT
     * ---------------------------------------------------------------
     */

    async function closeSupport() {

        if (
            !state.selectedConversationId
        ) {
            return;
        }

        if (
            !window.confirm(
                'Close this customer support conversation?'
            )
        ) {
            return;
        }

        elements.close.disabled =
            true;

        try {

            await api(
                '/secretary/chatbot/api/close-support',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({
                        conversation_id:
                            state.selectedConversationId
                    })
                }
            );

            notify(
                'Support conversation closed.',
                'success'
            );

            state.selectedConversationId =
                null;

            state.currentConversation =
                null;

            renderConversationHeader(
                null
            );

            renderMessages([]);

            updateActionButtons();

            await loadSupportConversations();

        } catch (error) {

            notify(
                error.message ||
                'Unable to close the conversation.',
                'error'
            );

        }

    }


    /*
     * ---------------------------------------------------------------
     * TABS
     * ---------------------------------------------------------------
     */

    document
        .querySelectorAll(
            '[data-support-filter]'
        )
        .forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        document
                            .querySelectorAll(
                                '[data-support-filter]'
                            )
                            .forEach(
                                function (tab) {

                                    tab.classList.remove(
                                        'active'
                                    );

                                }
                            );

                        button.classList.add(
                            'active'
                        );

                        state.filter =
                            button.dataset.supportFilter ||
                            'all';

                        renderConversationList();

                    }
                );

            }
        );


    /*
     * ---------------------------------------------------------------
     * SEARCH
     * ---------------------------------------------------------------
     */

    if (elements.search) {

        elements.search.addEventListener(
            'input',
            function () {

                state.search =
                    elements.search.value;

                renderConversationList();

            }
        );

    }


    /*
     * ---------------------------------------------------------------
     * BUTTON EVENTS
     * ---------------------------------------------------------------
     */

    elements.takeOver.addEventListener(
        'click',
        takeOver
    );

    elements.returnAI.addEventListener(
        'click',
        returnToAI
    );

    elements.close.addEventListener(
        'click',
        closeSupport
    );

    elements.send.addEventListener(
        'click',
        sendStaffMessage
    );


    /*
     * ---------------------------------------------------------------
     * ENTER TO SEND
     * ---------------------------------------------------------------
     */

    elements.input.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Enter' &&
                !event.shiftKey
            ) {

                event.preventDefault();

                sendStaffMessage();

            }

        }
    );


    /*
     * ---------------------------------------------------------------
     * EXISTING CHATBOT CUSTOMER-SERVICE INTEGRATION
     *
     * This lets your existing resident/chatbot JS check
     * whether the conversation is under human support.
     *
     * ---------------------------------------------------------------
     */

    window.BISCustomerService = {

        /*
         * Check current support mode.
         */

        getMode: function () {

            return (
                state.currentConversation &&
                state.currentConversation.support_mode
            ) || 'ai';

        },

        /*
         * True when the chat should not call OpenRouter.
         */

        isHumanSupport: function () {

            return (
                this.getMode() === 'human' ||
                this.getMode() === 'waiting_human'
            );

        },

        /*
         * Current conversation ID.
         */

        getConversationId: function () {

            return state.selectedConversationId;

        }

    };


    /*
     * ---------------------------------------------------------------
     * INITIAL LOAD
     * ---------------------------------------------------------------
     */

    loadSupportConversations();


    /*
     * ---------------------------------------------------------------
     * LIVE POLLING
     *
     * Every 2.5 seconds:
     *
     * 1. Refresh waiting/active conversations.
     * 2. Refresh selected conversation.
     *
     * This gives a live-chat experience without requiring
     * WebSocket infrastructure.
     * ---------------------------------------------------------------
     */

    setInterval(
        function () {

            loadSupportConversations();

        },
        2500
    );


})();
</script>

</body>
</html>

