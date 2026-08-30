<?php
/**
 * ============================================================
 * DASHBOARD TOPBAR + BIS AI CHATBOT
 * ============================================================
 */
?>

<!-- ============================================================
     FLOATING CHAT WIDGET
     ============================================================ -->
<div class="cw-wrap" id="cwWrap">

    <!-- Chat Toggle -->
    <button
        class="cw-toggle"
        id="cwToggle"
        onclick="cwOpen()"
        aria-label="Open chat">

        <span class="cw-toggle-icon" id="cwIcon">
            <i class="fas fa-comment-dots"></i>
        </span>

        <span class="cw-unread" id="cwUnread">1</span>
    </button>


    <!-- ========================================================
         CHAT PANEL
         ======================================================== -->
    <div class="cw-panel" id="cwPanel">

        <!-- Header -->
        <div class="cw-header">

            <div class="cw-header-left">

                <div class="cw-header-avatar">
                    <i class="fas fa-robot"></i>
                    <span class="cw-header-dot"></span>
                </div>

                <div class="cw-header-text">
                    <span class="cw-header-name">
                        BIS Assistant
                    </span>

                    <span class="cw-header-sub">
                        Bacolod Barangay · Online
                    </span>
                </div>

            </div>


            <div class="cw-header-actions">

                <button
                    type="button"
                    class="cw-hbtn"
                    title="New conversation"
                    onclick="cwNewChat()">

                    <i class="fas fa-plus"></i>

                </button>

                <button
                    type="button"
                    class="cw-hbtn"
                    title="Recent conversations"
                    onclick="cwToggleHistory()">

                    <i class="fas fa-history"></i>

                </button>

                

                <button
                    type="button"
                    class="cw-hbtn"
                    title="Close"
                    onclick="cwClose()">

                    <i class="fas fa-times"></i>

                </button>

            </div>

        </div>



        <div class="cw-history-panel" id="cwHistoryPanel" hidden>
            <div class="cw-history-title">
                <span>Recent conversations</span>
                <button type="button" class="cw-history-close" onclick="cwToggleHistory()">×</button>
            </div>
            <div class="cw-history-list" id="cwHistoryList">
                <div class="cw-history-empty">No saved conversations yet.</div>
            </div>
        </div>

        <!-- Date Divider -->
        <div class="cw-date-divider">
            <span>Today</span>
        </div>


        <!-- ====================================================
             CHAT MESSAGES
             ==================================================== -->
        <div
            class="cw-messages"
            id="cwMessages">

            <!-- Initial AI Message -->
            <div class="cw-row cw-row--bot">

                <div class="cw-avatar">
                    <i class="fas fa-robot"></i>
                </div>

                <div class="cw-body">

                    <div class="cw-bubble cw-ai-response">

                        <p class="cw-ai-paragraph">
                            Hello! I'm the
                            <strong>BIS Assistant</strong>
                            👋
                        </p>

                        <p class="cw-ai-paragraph">
                            How can I help you today?
                        </p>

                    </div>

                    <span class="cw-ts">
                        Just now
                    </span>

                </div>

            </div>


            <!-- =================================================
                 QUICK QUESTIONS
                 ================================================= -->
            <div
                class="cw-chips"
                id="cwChips">

                <button
                    type="button"
                    class="cw-chip"
                    onclick="cwQuick('How do I request a barangay clearance?')">

                    <i class="fas fa-file-alt"></i>
                    Request clearance

                </button>


                <button
                    type="button"
                    class="cw-chip"
                    onclick="cwQuick('How do I create an account?')">

                    <i class="fas fa-user-plus"></i>
                    Create account

                </button>


                <button
                    type="button"
                    class="cw-chip"
                    onclick="cwQuick('How do I file a blotter report?')">

                    <i class="fas fa-book"></i>
                    File blotter

                </button>


                <button
                    type="button"
                    class="cw-chip"
                    onclick="cwQuick('What documents can I request?')">

                    <i class="fas fa-file-contract"></i>
                    Documents

                </button>


                <button
                    type="button"
                    class="cw-chip"
                    onclick="cwQuick('What are the office hours?')">

                    <i class="fas fa-clock"></i>
                    Office hours

                </button>


                <button
                    type="button"
                    class="cw-chip"
                    onclick="cwQuick('How do I reset my password?')">

                    <i class="fas fa-key"></i>
                    Reset password

                </button>

            </div>

        </div>


        <!-- ====================================================
             CHAT INPUT
             ==================================================== -->
        <div class="cw-footer">

            <div class="cw-input-wrap">

                <input
                    type="text"
                    id="cwInput"
                    class="cw-input"
                    placeholder="Type a message..."
                    autocomplete="off">

                <button
                    type="button"
                    class="cw-send"
                    onclick="cwSend()">

                    <i class="fas fa-paper-plane"></i>

                </button>

            </div>

            <p class="cw-powered">
                Powered by
                <strong>Bacolod BIS</strong>
            </p>

        </div>

    </div>

</div>


<!-- ============================================================
     CHATBOT CSS
     ============================================================ -->
<style>

    /* ========================================================
       AI RESPONSE
       ======================================================== */

    .cw-ai-response {
        width: 100%;
        box-sizing: border-box;

        /*
         * IMPORTANT:
         * This allows the text itself to be justified.
         */
        text-align: justify !important;

        line-height: 1.65;

        white-space: normal;

        word-break: normal;
        overflow-wrap: break-word;
    }


    /* Normal paragraphs */

    .cw-ai-response .cw-ai-paragraph {

        display: block;

        margin: 0 0 10px 0;
        padding: 0;

        text-align: justify !important;

        line-height: 1.65;
    }


    /* Remove margin from last paragraph */

    .cw-ai-response .cw-ai-paragraph:last-child {
        margin-bottom: 0;
    }


    /* ========================================================
       NUMBERED STEPS
       ======================================================== */

    .cw-ai-response .cw-ai-steps {

        display: block;

        margin-top: 10px;
        margin-bottom: 12px;

        padding-left: 30px;

        text-align: left !important;
    }


    .cw-ai-response .cw-ai-steps li {

        margin-bottom: 9px;

        padding-left: 5px;

        line-height: 1.65;

        /*
         * Text of each step is justified.
         */
        text-align: justify !important;
    }


    .cw-ai-response .cw-ai-steps li:last-child {
        margin-bottom: 0;
    }


    /* ========================================================
       BULLET POINTS
       ======================================================== */

    .cw-ai-response .cw-ai-bullet {

        display: block;

        margin: 6px 0;

        padding-left: 5px;

        line-height: 1.65;

        text-align: justify !important;
    }


    /* ========================================================
       EMPTY SPACE BETWEEN PARAGRAPHS
       ======================================================== */

    .cw-ai-response .cw-space {

        display: block;

        height: 7px;
    }


    /* ========================================================
       BOLD
       ======================================================== */

    .cw-ai-response strong {
        font-weight: 700;
    }


    /* ========================================================
       ITALIC
       ======================================================== */

    .cw-ai-response em {
        font-style: italic;
    }


    /* ========================================================
       LINK
       ======================================================== */

    .cw-ai-response a {
        text-decoration: underline;
    }


    /* ========================================================
       CHAT BUBBLE
       ======================================================== */

    .cw-bubble.cw-ai-response {

        max-width: 100%;

        box-sizing: border-box;

        overflow-wrap: anywhere;
    }


    /* ========================================================
       TYPING
       ======================================================== */

    .cw-typing-row {
        margin-bottom: 5px;
    }


    /* ========================================================
       PERSISTENT CHAT HISTORY
       ======================================================== */

    .cw-history-panel {
        position: absolute;
        top: 64px;
        left: 0;
        right: 0;
        z-index: 20;
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,.08);
        box-shadow: 0 8px 24px rgba(0,0,0,.10);
        max-height: 330px;
        overflow: hidden;
    }

    .cw-history-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 1px solid rgba(0,0,0,.06);
    }

    .cw-history-close {
        border: 0;
        background: transparent;
        font-size: 20px;
        cursor: pointer;
        line-height: 1;
    }

    .cw-history-list {
        max-height: 275px;
        overflow-y: auto;
        padding: 6px;
    }

    .cw-history-item {
        width: 100%;
        border: 0;
        background: transparent;
        text-align: left;
        padding: 10px 11px;
        border-radius: 8px;
        cursor: pointer;
        margin-bottom: 2px;
    }

    .cw-history-item:hover,
    .cw-history-item.active {
        background: rgba(91,111,214,.08);
    }

    .cw-history-item-title {
        display: block;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cw-history-item-date {
        display: block;
        margin-top: 3px;
        font-size: 10px;
        opacity: .60;
    }

    .cw-history-empty {
        padding: 18px 10px;
        text-align: center;
        font-size: 12px;
        opacity: .65;
    }

</style>


<!-- ============================================================
     CHATBOT JAVASCRIPT
     ============================================================ -->
<script>

(function () {

    'use strict';


    /* ========================================================
       CONFIGURATION
       ======================================================== */

    const CHAT_ENDPOINT = '/api/chatbot/chat';
    const CHAT_HISTORY_ENDPOINT = '/api/chatbot/history';
    const CHAT_CONVERSATION_ENDPOINT = '/api/chatbot/conversation';

    let conversationId = null;
    let chatHistoryLoaded = false;


    /* ========================================================
       CURRENT TIME
       ======================================================== */

    function now() {

        const d = new Date();

        return (
            d.getHours().toString().padStart(2, '0') +
            ':' +
            d.getMinutes().toString().padStart(2, '0')
        );
    }


    /* ========================================================
       HTML ESCAPE
       ======================================================== */

    function escapeHtml(value) {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    /* ========================================================
       FORMAT INLINE MARKDOWN
       ======================================================== */

    function formatInline(text) {

        /*
         * Escape HTML first.
         */
        let result = escapeHtml(text);


        /*
         * Bold:
         *
         * **Barangay Clearance**
         */
        result = result.replace(
            /\*\*(.*?)\*\*/g,
            '<strong>$1</strong>'
        );


        /*
         * Italic:
         *
         * *example*
         */
        result = result.replace(
            /(^|[^*])\*([^*]+)\*(?!\*)/g,
            '$1<em>$2</em>'
        );


        /*
         * Markdown inline code
         */
        result = result.replace(
            /`([^`]+)`/g,
            '<code>$1</code>'
        );


        return result;
    }


    /* ========================================================
       FORMAT AI RESPONSE
       ======================================================== */

    function formatAIResponse(text) {

        if (
            text === null ||
            text === undefined
        ) {
            return '';
        }


        /*
         * Convert everything to string.
         */
        text = String(text);


        /*
         * Normalize escaped newlines.
         *
         * This handles:
         *
         * \n
         * \\n
         * \r\n
         */
        text = text
            .replace(/\\r\\n/g, '\n')
            .replace(/\\n/g, '\n')
            .replace(/\r\n/g, '\n')
            .replace(/\r/g, '\n');


        /*
         * ====================================================
         * IMPORTANT FIX
         * ====================================================
         *
         * OpenRouter may return:
         *
         * "1. Login 2. Open My Clearances 3. Click..."
         *
         * instead of:
         *
         * 1. Login
         * 2. Open My Clearances
         * 3. Click...
         *
         * So we FORCE a newline before numbered items.
         */
        text = text.replace(
            /\s+(\d{1,2})\.\s+/g,
            '\n$1. '
        );


        /*
         * If "1." is immediately after a colon:
         *
         * "follow these steps: 1. Login"
         *
         * turn it into:
         *
         * "follow these steps:
         *
         * 1. Login"
         */
        text = text.replace(
            /:\s*(\d{1,2})\.\s+/g,
            ':\n$1. '
        );


        /*
         * Normalize excessive spaces.
         */
        text = text.replace(
            /[ \t]+/g,
            ' '
        );


        /*
         * Split into lines.
         */
        const lines = text.split('\n');


        let html = '';

        let inNumberedList = false;

        let inBulletList = false;


        /*
         * Close numbered list.
         */
        function closeNumberedList() {

            if (inNumberedList) {

                html += '</ol>';

                inNumberedList = false;
            }
        }


        /*
         * Close bullet list.
         */
        function closeBulletList() {

            if (inBulletList) {

                html += '</ul>';

                inBulletList = false;
            }
        }


        /*
         * Process every line.
         */
        lines.forEach(function (rawLine) {

            const line = rawLine.trim();


            /*
             * Empty line
             */
            if (!line) {

                closeNumberedList();
                closeBulletList();

                html += '<div class="cw-space"></div>';

                return;
            }


            /*
             * =================================================
             * NUMBERED STEP
             * =================================================
             *
             * Matches:
             *
             * 1. Login
             * 2. Open My Clearances
             */
            const numberedMatch =
                line.match(/^(\d{1,2})\.\s+(.*)$/);


            if (numberedMatch) {

                closeBulletList();


                if (!inNumberedList) {

                    html +=
                        '<ol class="cw-ai-steps">';

                    inNumberedList = true;
                }


                html +=
                    '<li>' +
                    formatInline(numberedMatch[2]) +
                    '</li>';

                return;
            }


            /*
             * =================================================
             * BULLET
             * =================================================
             */
            const bulletMatch =
                line.match(/^(?:[-•*])\s+(.*)$/);


            if (bulletMatch) {

                closeNumberedList();


                if (!inBulletList) {

                    html +=
                        '<ul class="cw-ai-bullets">';

                    inBulletList = true;
                }


                html +=
                    '<li>' +
                    formatInline(bulletMatch[1]) +
                    '</li>';

                return;
            }


            /*
             * Normal paragraph
             */
            closeNumberedList();
            closeBulletList();


            html +=
                '<p class="cw-ai-paragraph">' +
                formatInline(line) +
                '</p>';

        });


        /*
         * Close lists if still open.
         */
        closeNumberedList();
        closeBulletList();


        return html;
    }


    /* ========================================================
       ADD MESSAGE
       ======================================================== */

    function addMsg(text, isUser) {

        const wrap =
            document.getElementById('cwMessages');

        if (!wrap) {
            return;
        }


        /*
         * Remove quick buttons after first message.
         */
        const chips =
            document.getElementById('cwChips');

        if (chips) {
            chips.remove();
        }


        /*
         * Create message row.
         */
        const row =
            document.createElement('div');


        row.className =
            'cw-row ' +
            (
                isUser
                    ? 'cw-row--user'
                    : 'cw-row--bot'
            );


        /*
         * ====================================================
         * USER MESSAGE
         * ====================================================
         */

        if (isUser) {

            row.innerHTML = `

                <div class="cw-body">

                    <div class="cw-bubble">
                        ${escapeHtml(text)}
                    </div>

                    <span class="cw-ts">
                        ${now()}
                    </span>

                </div>

            `;

        }


        /*
         * ====================================================
         * AI MESSAGE
         * ====================================================
         */

        else {

            const formatted =
                formatAIResponse(text);


            row.innerHTML = `

                <div class="cw-avatar">
                    <i class="fas fa-robot"></i>
                </div>

                <div class="cw-body">

                    <div class="cw-bubble cw-ai-response">
                        ${formatted}
                    </div>

                    <span class="cw-ts">
                        ${now()}
                    </span>

                </div>

            `;
        }


        /*
         * Add to chat.
         */
        wrap.appendChild(row);


        /*
         * Scroll to bottom.
         */
        wrap.scrollTop =
            wrap.scrollHeight;
    }


    /* ========================================================
       TYPING INDICATOR
       ======================================================== */

    function typing() {

        const wrap =
            document.getElementById('cwMessages');

        if (!wrap) {
            return;
        }


        /*
         * Remove existing typing indicator.
         */
        const existing =
            document.getElementById('cwTyping');

        if (existing) {
            existing.remove();
        }


        const t =
            document.createElement('div');


        t.className =
            'cw-row cw-row--bot cw-typing-row';


        t.id =
            'cwTyping';


        t.innerHTML = `

            <div class="cw-avatar">
                <i class="fas fa-robot"></i>
            </div>

            <div class="cw-body">

                <div class="cw-bubble cw-typing">

                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>

        `;


        wrap.appendChild(t);


        wrap.scrollTop =
            wrap.scrollHeight;
    }


    /* ========================================================
       REMOVE TYPING
       ======================================================== */

    function removeTyping() {

        const t =
            document.getElementById('cwTyping');

        if (t) {
            t.remove();
        }
    }


    /* ========================================================
       SEND MESSAGE TO OPENROUTER RAG
       ======================================================== */


    /* ========================================================
       LOAD PERSISTENT CHAT HISTORY
       ======================================================== */

    async function loadChatHistory() {

        if (chatHistoryLoaded) {
            return;
        }

        chatHistoryLoaded = true;

        try {
            const response = await fetch(
                CHAT_HISTORY_ENDPOINT,
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (!data || !data.success || !data.authenticated) {
                return;
            }

            renderConversationList(data.conversations || []);

            if (data.active_conversation_id) {
                conversationId = Number(data.active_conversation_id);
                renderStoredMessages(data.messages || []);
            }

        } catch (error) {
            console.warn('Could not load BIS chat history:', error);
        }
    }


    function renderConversationList(conversations) {

        document.__cwConversations = conversations || [];

        const list = document.getElementById('cwHistoryList');

        if (!list) {
            return;
        }

        list.innerHTML = '';

        if (!conversations.length) {

            list.innerHTML =
                '<div class="cw-history-empty">' +
                'No saved conversations yet.' +
                '</div>';

            return;
        }

        conversations.forEach(function (conversation) {

            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'cw-history-item';

            if (
                conversationId !== null &&
                Number(conversation.id) === Number(conversationId)
            ) {
                button.classList.add('active');
            }

            const title = document.createElement('span');
            title.className = 'cw-history-item-title';
            title.textContent =
                conversation.title || 'Conversation';

            const date = document.createElement('span');
            date.className = 'cw-history-item-date';

            date.textContent =
                conversation.updated_at ||
                conversation.created_at ||
                '';

            button.appendChild(title);
            button.appendChild(date);

            button.addEventListener('click', function () {
                loadConversation(Number(conversation.id));
            });

            list.appendChild(button);
        });
    }


    async function loadConversation(id) {

        try {

            const response = await fetch(
                CHAT_CONVERSATION_ENDPOINT + '/' + encodeURIComponent(id),
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (!data || !data.success) {
                return;
            }

            conversationId = Number(id);

            renderStoredMessages(data.messages || []);

            const panel =
                document.getElementById('cwHistoryPanel');

            if (panel) {
                panel.hidden = true;
            }

            renderConversationList(
                document.__cwConversations || []
            );

        } catch (error) {
            console.error('Could not load BIS conversation:', error);
        }
    }


    function renderStoredMessages(messages) {

        const wrap =
            document.getElementById('cwMessages');

        if (!wrap) {
            return;
        }

        /*
         * Clear the temporary greeting/chips and rebuild the selected
         * conversation from the database.
         */
        wrap.innerHTML = '';

        if (!messages.length) {
            addMsg(
                "Hello! I'm the BIS Assistant 👋\n\nHow can I help you today?",
                false
            );
            return;
        }

        messages.forEach(function (item) {

            addMsg(
                item.message || '',
                item.sender === 'user'
            );

        });

        wrap.scrollTop = wrap.scrollHeight;
    }


    window.cwToggleHistory = function () {

        const panel =
            document.getElementById('cwHistoryPanel');

        if (!panel) {
            return;
        }

        panel.hidden = !panel.hidden;

        if (!panel.hidden) {
            loadChatHistory();
        }
    };


    window.cwNewChat = function () {

        conversationId = null;

        const panel =
            document.getElementById('cwHistoryPanel');

        if (panel) {
            panel.hidden = true;
        }

        const wrap =
            document.getElementById('cwMessages');

        if (wrap) {
            wrap.innerHTML = '';

            addMsg(
                "Hello! I'm the BIS Assistant 👋\n\nHow can I help you today?",
                false
            );

            /*
             * Recreate quick-topic chips for the new conversation.
             */
            const chips = document.createElement('div');

            chips.className = 'cw-chips';
            chips.id = 'cwChips';

            chips.innerHTML = `
                <button type="button" class="cw-chip" onclick="cwQuick('How do I request a barangay clearance?')">
                    <i class="fas fa-file-alt"></i> Request clearance
                </button>
                <button type="button" class="cw-chip" onclick="cwQuick('How do I create an account?')">
                    <i class="fas fa-user-plus"></i> Create account
                </button>
                <button type="button" class="cw-chip" onclick="cwQuick('How do I file a blotter report?')">
                    <i class="fas fa-book"></i> File blotter
                </button>
                <button type="button" class="cw-chip" onclick="cwQuick('What documents can I request?')">
                    <i class="fas fa-file-contract"></i> Documents
                </button>
                <button type="button" class="cw-chip" onclick="cwQuick('What are the office hours?')">
                    <i class="fas fa-clock"></i> Office hours
                </button>
                <button type="button" class="cw-chip" onclick="cwQuick('How do I reset my password?')">
                    <i class="fas fa-key"></i> Reset password
                </button>
            `;

            wrap.appendChild(chips);
        }

        const input =
            document.getElementById('cwInput');

        if (input) {
            input.focus();
        }
    };


    document.addEventListener('DOMContentLoaded', function () {
        loadChatHistory();
    });


    window.cwSend = async function () {

        const inp =
            document.getElementById('cwInput');


        if (!inp) {
            return;
        }


        const msg =
            inp.value.trim();


        /*
         * Don't send empty message.
         */
        if (!msg) {
            return;
        }


        /*
         * Display user message.
         */
        addMsg(msg, true);


        /*
         * Clear input.
         */
        inp.value = '';


        /*
         * Hide unread badge.
         */
        const unread =
            document.getElementById('cwUnread');

        if (unread) {
            unread.style.display = 'none';
        }


        /*
         * Show typing.
         */
        typing();


        try {

            /*
             * =================================================
             * SEND TO YOUR CODEIGNITER API
             * =================================================
             */
            const response =
                await fetch(
                    CHAT_ENDPOINT,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/x-www-form-urlencoded; charset=UTF-8',

                            'Accept':
                                'application/json'
                        },

                        credentials:
                            'same-origin',

                        body:
                            'message=' +
                            encodeURIComponent(msg) +
                            '&conversation_id=' +
                            encodeURIComponent(
                                conversationId === null ? '' : conversationId
                            )
                    }
                );


            /*
             * Check HTTP status.
             */
            if (!response.ok) {

                throw new Error(
                    'HTTP ' +
                    response.status
                );
            }


            /*
             * Parse JSON.
             */
            const data =
                await response.json();


            /*
             * Remove typing.
             */
            removeTyping();


            /*
             * =================================================
             * SUCCESSFUL AI RESPONSE
             * =================================================
             */
            if (
                data &&
                data.success &&
                data.response
            ) {

                if (
                    data.conversation_id !== undefined &&
                    data.conversation_id !== null &&
                    Number(data.conversation_id) > 0
                ) {
                    conversationId = Number(data.conversation_id);
                }

                addMsg(
                    data.response,
                    false
                );


                /*
                 * Console information.
                 *
                 * This is useful for confirming RAG.
                 */
                console.log(
                    'BIS AI Response:',
                    data
                );

                return;
            }


            /*
             * =================================================
             * API RETURNED ERROR
             * =================================================
             */
            addMsg(
                'Sorry, I could not generate an answer right now. Please try again.',
                false
            );


            console.error(
                'Chatbot API error:',
                data
            );

        }

        catch (error) {

            /*
             * Remove typing.
             */
            removeTyping();


            /*
             * Show connection error.
             */
            addMsg(
                'Sorry, I could not connect to the BIS AI service. Please try again.',
                false
            );


            console.error(
                'Chatbot request failed:',
                error
            );
        }

    };


    /* ========================================================
       QUICK QUESTION
       ======================================================== */

    window.cwQuick = function (msg) {

        const chips =
            document.getElementById('cwChips');

        if (chips) {
            chips.remove();
        }


        const input =
            document.getElementById('cwInput');

        if (input) {
            input.value = msg;
        }


        /*
         * Send through the exact same
         * OpenRouter/RAG API process.
         */
        window.cwSend();
    };


    /* ========================================================
       OPEN CHAT
       ======================================================== */

    window.cwOpen = function () {

        const panel =
            document.getElementById('cwPanel');

        const wrap =
            document.getElementById('cwWrap');

        const unread =
            document.getElementById('cwUnread');


        if (!panel || !wrap) {
            return;
        }


        panel.classList.toggle('cw-open');

        wrap.classList.toggle('cw-active');


        if (
            panel.classList.contains('cw-open')
        ) {

            if (unread) {
                unread.style.display = 'none';
            }


            setTimeout(function () {

                const input =
                    document.getElementById('cwInput');

                if (input) {
                    input.focus();
                }

            }, 150);
        }

    };


    /* ========================================================
       CLOSE CHAT
       ======================================================== */

    window.cwClose = function () {

        const panel =
            document.getElementById('cwPanel');

        const wrap =
            document.getElementById('cwWrap');


        if (panel) {
            panel.classList.remove('cw-open');
        }


        if (wrap) {
            wrap.classList.remove('cw-active');
        }

    };


    /* ========================================================
       ENTER KEY
       ======================================================== */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Enter' &&
                document.activeElement &&
                document.activeElement.id === 'cwInput'
            ) {

                event.preventDefault();

                window.cwSend();
            }

        }
    );


    /* ========================================================
       LEGACY ALIASES
       ======================================================== */

    window.toggleChat =
        window.cwOpen;

    window.sendQuick =
        window.cwQuick;


})();

</script>


<!-- ============================================================
     DASHBOARD TOPBAR
     ============================================================ -->

<header class="db-topbar">

    <button
        class="db-menu-toggle"
        onclick="document.getElementById('sidebar').classList.toggle('open')"
        aria-label="Toggle menu">

        <i class="fas fa-bars"></i>

    </button>


    <div class="db-topbar-title">

        <h1>
            <?= $pageTitle ?? 'Dashboard' ?>
        </h1>

        <span>
            <?= date('l, F j, Y') ?>
        </span>

    </div>


    <div class="db-topbar-right">

        <!-- Notification -->
        <button
            class="db-notif-btn"
            onclick="window.location.href='/<?= esc((string)(session()->get('role') ?? 'resident')) ?>/notifications'"
            aria-label="Notifications">

            <i class="fas fa-bell"></i>

            <span
                class="db-notif-dot"
                id="topbarNotifDot"
                style="display:none;">
            </span>

            <span
                id="topbarUnreadCount"
                class="db-notif-count"
                style="display:none;">
            </span>

        </button>


        <!-- Avatar -->
        <div
            class="db-avatar"
            onclick="window.location.href='/<?= esc((string)(session()->get('role') ?? 'resident')) ?>/<?= session()->get('role') === 'resident' ? 'profile' : 'settings' ?>'"
            style="cursor:pointer;">

            <?php

            $avatarFile =
                session()->get('avatar');

            if (
                $avatarFile &&
                file_exists(
                    FCPATH .
                    'uploads/avatars/' .
                    $avatarFile
                )
            ):

            ?>

                <img
                    src="/uploads/avatars/<?= esc($avatarFile) ?>"
                    alt="Avatar"
                    style="
                        width:100%;
                        height:100%;
                        object-fit:cover;
                        border-radius:50%;
                    ">

            <?php else: ?>

                <i class="fas fa-user"></i>

            <?php endif; ?>

        </div>


        <!-- Username -->
        <span class="db-username">

            <?= esc(
                (string)(
                    session()->get('username')
                    ?? 'User'
                )
            ) ?>

        </span>

    </div>

</header>


<!-- ============================================================
     RESIDENT NOTIFICATIONS
     ============================================================ -->

<?php if (session()->get('role') === 'resident'): ?>

<style>

    .db-notif-count {

        position: absolute;

        top: 2px;
        right: 2px;

        min-width: 17px;
        height: 17px;

        background: #c0392b;

        color: #fff;

        font-size: 10px;

        font-weight: 700;

        border-radius: 100px;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 0 4px;

        pointer-events: none;
    }

</style>


<script>

(function () {

    function pollUnread() {

        fetch(
            '/resident/notifications/poll',
            {
                credentials:
                    'same-origin'
            }
        )

        .then(function (r) {
            return r.json();
        })

        .then(function (data) {

            const count =
                data.unread || 0;


            const dot =
                document.getElementById(
                    'topbarNotifDot'
                );


            const badge =
                document.getElementById(
                    'topbarUnreadCount'
                );


            if (dot) {

                dot.style.display =
                    count > 0
                        ? ''
                        : 'none';
            }


            if (badge) {

                badge.textContent =
                    count > 9
                        ? '9+'
                        : count;


                badge.style.display =
                    count > 0
                        ? 'flex'
                        : 'none';
            }

        })

        .catch(function () {});

    }


    pollUnread();


    setInterval(
        pollUnread,
        30000
    );

})();

</script>


<!-- ============================================================
     SECRETARY / CAPTAIN NOTIFICATIONS
     ============================================================ -->

<?php elseif (
    in_array(
        session()->get('role'),
        ['secretary', 'captain']
    )
): ?>

<style>

    .db-notif-count {

        position: absolute;

        top: 2px;
        right: 2px;

        min-width: 17px;
        height: 17px;

        background: #e6a800;

        color: #fff;

        font-size: 10px;

        font-weight: 700;

        border-radius: 100px;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 0 4px;

        pointer-events: none;
    }

</style>


<script>

(function () {

    const _role =
        '<?= esc(session()->get('role')) ?>';


    function pollAdmin() {

        fetch(
            '/' +
            _role +
            '/notifications/poll',
            {
                credentials:
                    'same-origin'
            }
        )

        .then(function (r) {
            return r.json();
        })

        .then(function (data) {

            const count =
                data.unread || 0;


            const dot =
                document.getElementById(
                    'topbarNotifDot'
                );


            const badge =
                document.getElementById(
                    'topbarUnreadCount'
                );


            if (dot) {

                dot.style.display =
                    count > 0
                        ? ''
                        : 'none';
            }


            if (badge) {

                badge.textContent =
                    count > 9
                        ? '9+'
                        : count;


                badge.style.display =
                    count > 0
                        ? 'flex'
                        : 'none';
            }

        })

        .catch(function () {});

    }


    pollAdmin();


    setInterval(
        pollAdmin,
        60000
    );

})();

</script>

<?php endif; ?>