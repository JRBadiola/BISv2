<?php

$role = strtolower(
    (string) (
        $role ??
        session()->get('role') ??
        'secretary'
    )
);

$active = 'customer_service';

$pageTitle =
    $pageTitle ??
    'Customer Service';

if (
    ! in_array(
        $role,
        [
            'secretary',
            'captain'
        ],
        true
    )
) {
    return redirect()->to('/');
}

$roleLabel =
    ucfirst($role);

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= esc($pageTitle) ?> | Bacolod BIS
    </title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        /* =========================================================
           RESET
           ========================================================= */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f7fb;

            color: #1f2937;
        }


        /* =========================================================
           SIDEBAR
           ========================================================= */

        .db-sidebar {
            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            width: 250px;

            background: #ffffff;

            border-right:
                1px solid #e5e7eb;

            z-index: 1000;

            display: flex;
            flex-direction: column;

            box-shadow:
                2px 0 10px rgba(
                    15,
                    23,
                    42,
                    0.04
                );
        }

        .db-sidebar-brand {
            min-height: 78px;

            padding:
                14px 18px;

            display: flex;

            align-items: center;

            gap: 10px;

            border-bottom:
                1px solid #eef0f4;
        }

        .db-brand-logo {
            width: 42px;
            height: 42px;

            min-width: 42px;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            border-radius: 9px;
        }

        .db-brand-logo img {
            display: block;

            width: 38px !important;
            height: 38px !important;

            max-width: 38px !important;
            max-height: 38px !important;

            object-fit: contain;
        }

        .db-brand-text {
            min-width: 0;

            display: flex;
            flex-direction: column;
        }

        .db-brand-name {
            display: block;

            font-size: 15px;

            font-weight: 700;

            line-height: 1.2;

            color: #1f2937;
        }

        .db-brand-role {
            display: flex;

            align-items: center;

            gap: 5px;

            margin-top: 4px;

            font-size: 11px;

            color: #64748b;
        }

        .db-brand-role i {
            font-size: 10px;
        }

        .db-nav {
            flex: 1;

            overflow-y: auto;

            padding:
                12px 10px;
        }

        .db-nav-item {
            width: 100%;

            min-height: 41px;

            margin-bottom: 3px;

            padding:
                0 12px;

            border-radius: 8px;

            display: flex;

            align-items: center;

            gap: 10px;

            text-decoration: none;

            color: #64748b;

            font-size: 13px;

            font-weight: 500;

            transition:
                background 0.15s ease,
                color 0.15s ease;
        }

        .db-nav-item i {
            width: 18px;

            text-align: center;

            font-size: 14px;
        }

        .db-nav-item:hover {
            background: #f8fafc;

            color: #2563eb;
        }

        .db-nav-item.active {
            background: #eff6ff;

            color: #2563eb;

            font-weight: 600;
        }

        .db-logout {
            margin: 10px;

            min-height: 41px;

            padding:
                0 12px;

            border-radius: 8px;

            display: flex;

            align-items: center;

            gap: 10px;

            text-decoration: none;

            color: #64748b;

            font-size: 13px;

            transition:
                background 0.15s ease,
                color 0.15s ease;
        }

        .db-logout:hover {
            background: #fef2f2;

            color: #dc2626;
        }


        /* =========================================================
           MAIN PAGE
           ========================================================= */

        .customer-service-page {
            margin-left: 250px;

            width: calc(100% - 250px);

            min-height: 100vh;

            padding:
                24px 28px;

            background: #f5f7fb;
        }


        /* =========================================================
           PAGE HEADER
           ========================================================= */

        .cs-header {
            width: 100%;

            margin-bottom: 18px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;
        }

        .cs-title-area {
            min-width: 0;
        }

        .cs-title-area h1 {
            margin: 0;

            font-size: 24px;

            line-height: 1.25;

            font-weight: 700;

            color: #1f2937;
        }

        .cs-title-area p {
            margin:
                6px 0 0;

            font-size: 13px;

            line-height: 1.5;

            color: #64748b;
        }

        .cs-header-badge {
            flex-shrink: 0;

            height: 38px;

            padding:
                0 13px;

            border:
                1px solid #e5e7eb;

            border-radius: 9px;

            background: #ffffff;

            display: inline-flex;

            align-items: center;

            gap: 7px;

            color: #475569;

            font-size: 12px;

            font-weight: 600;
        }

        .cs-header-badge i {
            color: #2563eb;

            font-size: 13px;
        }


        /* =========================================================
           MAIN TWO-COLUMN AREA
           ========================================================= */

        .cs-layout {
            width: 100%;

            height:
                calc(100vh - 130px);

            min-height: 590px;

            display: grid;

            grid-template-columns:
                320px minmax(0, 1fr);

            gap: 16px;
        }

        .cs-panel {
            min-width: 0;

            min-height: 0;

            background: #ffffff;

            border:
                1px solid #e5e7eb;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 2px 10px rgba(
                    15,
                    23,
                    42,
                    0.04
                );
        }


        /* =========================================================
           QUEUE
           ========================================================= */

        .cs-queue-panel {
            display: flex;

            flex-direction: column;
        }

        .cs-queue-header {
            min-height: 62px;

            padding:
                0 16px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            border-bottom:
                1px solid #eef0f4;
        }

        .cs-queue-title {
            display: flex;

            align-items: center;

            gap: 9px;

            color: #1f2937;

            font-size: 14px;

            font-weight: 700;
        }

        .cs-queue-title i {
            color: #2563eb;

            font-size: 14px;
        }

        .cs-count {
            min-width: 24px;

            height: 24px;

            padding:
                0 7px;

            border-radius: 999px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            background: #eff6ff;

            color: #2563eb;

            font-size: 11px;

            font-weight: 700;
        }

        .cs-queue-status {
            min-height: 37px;

            padding:
                0 16px;

            display: flex;

            align-items: center;

            border-bottom:
                1px solid #eef0f4;

            color: #94a3b8;

            font-size: 11px;
        }

        .cs-queue-list {
            flex: 1;

            overflow-y: auto;
        }

        .cs-queue-empty {
            padding:
                50px 20px;

            text-align: center;

            color: #94a3b8;
        }

        .cs-queue-empty i {
            display: block;

            margin-bottom: 12px;

            color: #cbd5e1;

            font-size: 30px;
        }

        .cs-queue-empty p {
            margin: 0;

            font-size: 12px;

            line-height: 1.5;
        }

        .cs-conversation-item {
            width: 100%;

            padding:
                13px 15px;

            border: 0;

            border-bottom:
                1px solid #f1f5f9;

            background: #ffffff;

            text-align: left;

            cursor: pointer;

            display: block;

            transition:
                background 0.15s ease;
        }

        .cs-conversation-item:hover {
            background: #f8fafc;
        }

        .cs-conversation-item.active {
            background: #eff6ff;
        }

        .cs-conversation-top {
            width: 100%;

            display: flex;

            align-items: center;

            gap: 10px;
        }

        .cs-avatar {
            width: 38px;
            height: 38px;

            min-width: 38px;

            border-radius: 50%;

            background: #eff6ff;

            color: #2563eb;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 11px;

            font-weight: 700;
        }

        .cs-conversation-main {
            min-width: 0;

            flex: 1;
        }

        .cs-conversation-name {
            display: block;

            overflow: hidden;

            white-space: nowrap;

            text-overflow: ellipsis;

            color: #334155;

            font-size: 12px;

            font-weight: 700;
        }

        .cs-conversation-preview {
            display: block;

            margin-top: 3px;

            overflow: hidden;

            white-space: nowrap;

            text-overflow: ellipsis;

            color: #94a3b8;

            font-size: 10px;
        }

        .cs-conversation-meta {
            min-width: 55px;

            display: flex;

            flex-direction: column;

            align-items: flex-end;

            gap: 5px;
        }

        .cs-time {
            white-space: nowrap;

            color: #94a3b8;

            font-size: 9px;
        }

        .cs-mode-badge {
            padding:
                3px 6px;

            border-radius: 999px;

            white-space: nowrap;

            text-transform: uppercase;

            font-size: 8px;

            font-weight: 700;
        }

        .cs-mode-badge.waiting {
            background: #fef3c7;

            color: #92400e;
        }

        .cs-mode-badge.human {
            background: #dcfce7;

            color: #166534;
        }


        /* =========================================================
           CHAT PANEL
           ========================================================= */

        .cs-chat-panel {
            display: flex;

            flex-direction: column;
        }

        .cs-chat-header {
            min-height: 70px;

            padding:
                12px 16px;

            border-bottom:
                1px solid #eef0f4;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 12px;
        }

        .cs-resident-info {
            min-width: 0;

            display: flex;

            align-items: center;

            gap: 10px;
        }

        .cs-resident-avatar {
            width: 40px;
            height: 40px;

            min-width: 40px;

            border-radius: 50%;

            background: #eff6ff;

            color: #2563eb;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 12px;

            font-weight: 700;
        }

        .cs-resident-details {
            min-width: 0;
        }

        .cs-resident-name {
            margin: 0;

            overflow: hidden;

            white-space: nowrap;

            text-overflow: ellipsis;

            font-size: 13px;

            font-weight: 700;

            color: #334155;
        }

        .cs-resident-status {
            margin-top: 4px;

            display: flex;

            align-items: center;

            gap: 6px;

            font-size: 10px;

            color: #94a3b8;
        }

        .cs-status-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: #94a3b8;
        }

        .cs-status-dot.waiting {
            background: #f59e0b;
        }

        .cs-status-dot.human {
            background: #22c55e;
        }

        .cs-status-dot.closed {
            background: #94a3b8;
        }

        .cs-actions {
            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 6px;

            flex-wrap: wrap;
        }

        .cs-btn {
            height: 34px;

            padding:
                0 10px;

            border:
                1px solid #dbe1e8;

            border-radius: 8px;

            background: #ffffff;

            color: #475569;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 6px;

            cursor: pointer;

            font-family: inherit;

            font-size: 10px;

            font-weight: 600;

            transition:
                0.15s ease;
        }

        .cs-btn:hover:not(:disabled) {
            background: #f8fafc;

            border-color: #cbd5e1;
        }

        .cs-btn:disabled {
            opacity: 0.45;

            cursor: not-allowed;
        }

        .cs-btn.takeover {
            background: #2563eb;

            border-color: #2563eb;

            color: #ffffff;
        }

        .cs-btn.takeover:hover:not(:disabled) {
            background: #1d4ed8;

            border-color: #1d4ed8;
        }

        .cs-btn.return-ai {
            background: #f0f9ff;

            border-color: #bae6fd;

            color: #0369a1;
        }

        .cs-btn.close-support {
            background: #fef2f2;

            border-color: #fecaca;

            color: #b91c1c;
        }


        /* =========================================================
           EMPTY CHAT
           ========================================================= */

        .cs-chat-empty {
            flex: 1;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

            text-align: center;

            background: #f8fafc;

            color: #94a3b8;
        }

        .cs-chat-empty-inner i {
            margin-bottom: 12px;

            color: #cbd5e1;

            font-size: 40px;
        }

        .cs-chat-empty-inner h3 {
            margin:
                0 0 6px;

            color: #475569;

            font-size: 15px;
        }

        .cs-chat-empty-inner p {
            max-width: 380px;

            margin: 0 auto;

            font-size: 11px;

            line-height: 1.6;

            color: #94a3b8;
        }


        /* =========================================================
           MESSAGES
           ========================================================= */

        .cs-chat-messages {
            flex: 1;

            min-height: 0;

            overflow-y: auto;

            padding:
                18px;

            display: flex;

            flex-direction: column;

            gap: 13px;

            background: #f8fafc;
        }

        .cs-message-row {
            width: 100%;

            display: flex;
        }

        .cs-message-row.user {
            justify-content: flex-start;
        }

        .cs-message-row.assistant,
        .cs-message-row.staff {
            justify-content: flex-end;
        }

        .cs-message-bubble-wrap {
            max-width:
                min(
                    72%,
                    600px
                );
        }

        .cs-message-label {
            margin-bottom: 4px;

            color: #64748b;

            font-size: 9px;

            font-weight: 700;
        }

        .cs-message-row.assistant
        .cs-message-label,
        .cs-message-row.staff
        .cs-message-label {
            text-align: right;
        }

        .cs-message-bubble {
            padding:
                9px 12px;

            border-radius: 11px;

            font-size: 11px;

            line-height: 1.55;

            white-space: pre-wrap;

            word-break: break-word;
        }

        .cs-message-row.user
        .cs-message-bubble {
            background: #ffffff;

            border:
                1px solid #e5e7eb;

            border-bottom-left-radius: 3px;

            color: #475569;
        }

        .cs-message-row.assistant
        .cs-message-bubble {
            background: #e0edff;

            color: #1e3a8a;

            border-bottom-right-radius: 3px;
        }

        .cs-message-row.staff
        .cs-message-bubble {
            background: #2563eb;

            color: #ffffff;

            border-bottom-right-radius: 3px;
        }

        .cs-message-time {
            margin-top: 4px;

            color: #94a3b8;

            font-size: 8px;
        }

        .cs-message-row.assistant
        .cs-message-time,
        .cs-message-row.staff
        .cs-message-time {
            text-align: right;
        }


        /* =========================================================
           NOTICE
           ========================================================= */

        .cs-notice {
            display: none;

            margin:
                0 14px 10px;

            padding:
                8px 10px;

            border-radius: 7px;

            font-size: 10px;
        }

        .cs-notice.show {
            display: block;
        }

        .cs-notice.info {
            background: #eff6ff;

            color: #1d4ed8;
        }

        .cs-notice.success {
            background: #f0fdf4;

            color: #15803d;
        }

        .cs-notice.error {
            background: #fef2f2;

            color: #b91c1c;
        }


        /* =========================================================
           COMPOSER
           ========================================================= */

        .cs-composer {
            padding:
                10px 12px;

            border-top:
                1px solid #eef0f4;

            background: #ffffff;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                42px;

            gap: 8px;
        }

        .cs-message-input {
            width: 100%;

            min-height: 42px;

            max-height: 110px;

            resize: vertical;

            padding:
                10px 11px;

            border:
                1px solid #dbe1e8;

            border-radius: 8px;

            outline: none;

            font-family: inherit;

            font-size: 11px;

            color: #334155;

            background: #ffffff;
        }

        .cs-message-input:focus {
            border-color: #93c5fd;

            box-shadow:
                0 0 0 2px rgba(
                    37,
                    99,
                    235,
                    0.08
                );
        }

        .cs-send-btn {
            min-height: 42px;

            border: 0;

            border-radius: 8px;

            background: #2563eb;

            color: #ffffff;

            cursor: pointer;

            font-size: 13px;
        }

        .cs-send-btn:hover:not(:disabled) {
            background: #1d4ed8;
        }

        .cs-send-btn:disabled {
            opacity: 0.45;

            cursor: not-allowed;
        }


        /* =========================================================
           RESPONSIVE
           ========================================================= */

        @media (max-width: 1100px) {

            .customer-service-page {
                margin-left: 250px;

                width:
                    calc(
                        100% - 250px
                    );

                padding:
                    20px;
            }

            .cs-layout {
                grid-template-columns:
                    280px minmax(
                        0,
                        1fr
                    );
            }

        }


        @media (max-width: 850px) {

            .db-sidebar {
                position: static;

                width: 100%;

                height: auto;
            }

            .db-nav {
                max-height: 300px;
            }

            .customer-service-page {
                margin-left: 0;

                width: 100%;

                padding:
                    18px;
            }

            .cs-header {
                align-items: flex-start;

                flex-direction: column;
            }

            .cs-layout {
                height: auto;

                min-height: 0;

                grid-template-columns:
                    1fr;
            }

            .cs-queue-panel {
                min-height: 340px;
            }

            .cs-chat-panel {
                min-height: 620px;
            }

            .cs-chat-header {
                align-items: flex-start;

                flex-direction: column;
            }

            .cs-actions {
                width: 100%;

                justify-content: flex-start;
            }

        }

    </style>

</head>

<body>

<?php

echo view(
    'dashboard/sidebar',
    [
        'role'   => $role,
        'active' => $active,
    ]
);

?>

<main class="customer-service-page">

    <!-- =====================================================
         PAGE HEADER
         ====================================================== -->

    <div class="cs-header">

        <div class="cs-title-area">

            <h1>
                Customer Service
            </h1>

            <p>
                Manage resident support requests and communicate directly with clients.
            </p>

        </div>

        <div class="cs-header-badge">

            <i class="fas fa-headset"></i>

            <span>
                <?= esc($roleLabel) ?> Support Desk
            </span>

        </div>

    </div>


    <!-- =====================================================
         MAIN CUSTOMER SERVICE AREA
         ====================================================== -->

    <section class="cs-layout">


        <!-- =================================================
             SUPPORT REQUEST QUEUE
             ================================================== -->

        <div class="cs-panel cs-queue-panel">

            <div class="cs-queue-header">

                <div class="cs-queue-title">

                    <i class="fas fa-inbox"></i>

                    <span>
                        Support Requests
                    </span>

                </div>

                <span
                    id="supportRequestCount"
                    class="cs-count"
                >
                    0
                </span>

            </div>


            <div
                id="supportQueueStatus"
                class="cs-queue-status"
            >
                Loading support requests...
            </div>


            <div
                id="supportConversationList"
                class="cs-queue-list"
            >

                <div class="cs-queue-empty">

                    <i class="fas fa-spinner fa-spin"></i>

                    <p>
                        Loading support conversations...
                    </p>

                </div>

            </div>

        </div>


        <!-- =================================================
             CONVERSATION
             ================================================== -->

        <div class="cs-panel cs-chat-panel">


            <!-- CHAT HEADER -->

            <div class="cs-chat-header">

                <div class="cs-resident-info">

                    <div
                        id="supportResidentAvatar"
                        class="cs-resident-avatar"
                    >
                        ?
                    </div>

                    <div class="cs-resident-details">

                        <h2
                            id="supportResidentName"
                            class="cs-resident-name"
                        >
                            Select a conversation
                        </h2>

                        <div class="cs-resident-status">

                            <span
                                id="supportStatusDot"
                                class="cs-status-dot"
                            ></span>

                            <span
                                id="supportStatusText"
                            >
                                No conversation selected
                            </span>

                        </div>

                    </div>

                </div>


                <div class="cs-actions">

                    <button
                        id="takeOverConversation"
                        type="button"
                        class="cs-btn takeover"
                        disabled
                    >
                        <i class="fas fa-user-headset"></i>

                        Take Over
                    </button>


                    <button
                        id="returnToAI"
                        type="button"
                        class="cs-btn return-ai"
                        disabled
                    >
                        <i class="fas fa-robot"></i>

                        Return to AI
                    </button>


                    <button
                        id="closeSupportConversation"
                        type="button"
                        class="cs-btn close-support"
                        disabled
                    >
                        <i class="fas fa-check-circle"></i>

                        Close
                    </button>

                </div>

            </div>


            <!-- EMPTY STATE -->

            <div
                id="supportChatEmpty"
                class="cs-chat-empty"
            >

                <div class="cs-chat-empty-inner">

                    <i class="fas fa-comments"></i>

                    <h3>
                        No conversation selected
                    </h3>

                    <p>
                        Select a resident from the support queue to view the conversation.
                    </p>

                </div>

            </div>


            <!-- MESSAGES -->

            <div
                id="supportChatMessages"
                class="cs-chat-messages"
                style="display:none;"
            ></div>


            <!-- NOTICE -->

            <div
                id="supportNotice"
                class="cs-notice"
            ></div>


            <!-- COMPOSER -->

            <div
                id="supportComposer"
                class="cs-composer"
                style="display:none;"
            >

                <textarea
                    id="staffMessageInput"
                    class="cs-message-input"
                    rows="1"
                    placeholder="Type your message to the resident..."
                    disabled
                ></textarea>

                <button
                    id="staffSendButton"
                    type="button"
                    class="cs-send-btn"
                    title="Send message"
                    disabled
                >
                    <i class="fas fa-paper-plane"></i>
                </button>

            </div>

        </div>

    </section>

</main>


<script src="<?= base_url('js/customer_service.js') ?>"></script>

</body>
</html>