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

                <!-- New Conversation -->
                <button
                    type="button"
                    class="cw-hbtn"
                    title="New conversation"
                    onclick="cwNewChat()">

                    <i class="fas fa-plus"></i>

                </button>


                <!-- Recent Conversations -->
                <button
                    type="button"
                    class="cw-hbtn"
                    title="Recent conversations"
                    onclick="cwToggleHistory()">

                    <i class="fas fa-history"></i>

                </button>


                <!-- Close -->
                <button
                    type="button"
                    class="cw-hbtn"
                    title="Close"
                    onclick="cwClose()">

                    <i class="fas fa-times"></i>

                </button>

            </div>

        </div>


        <!-- ====================================================
             RECENT CONVERSATIONS
             ==================================================== -->

        <div
            class="cw-history-panel"
            id="cwHistoryPanel"
            hidden>

            <div class="cw-history-title">

                <span>
                    Recent conversations
                </span>

                <button
                    type="button"
                    class="cw-history-close"
                    onclick="cwToggleHistory()">

                    ×

                </button>

            </div>


            <div
                class="cw-history-list"
                id="cwHistoryList">

                <div class="cw-history-empty">
                    No saved conversations yet.
                </div>

            </div>

        </div>


        <!-- ====================================================
             DATE DIVIDER
             ==================================================== -->

        <div class="cw-date-divider">

            <span>
                Today
            </span>

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

/* ============================================================
   AI RESPONSE
   ============================================================ */

.cw-ai-response {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;

    text-align: justify !important;

    line-height: 1.65;

    white-space: normal;

    word-break: normal;

    overflow-wrap: anywhere;
}


/* ============================================================
   NORMAL PARAGRAPHS
   ============================================================ */

.cw-ai-response .cw-ai-paragraph {
    display: block;

    margin: 0 0 10px 0;

    padding: 0;

    text-align: justify !important;

    line-height: 1.65;
}


.cw-ai-response .cw-ai-paragraph:last-child {
    margin-bottom: 0;
}


/* ============================================================
   NUMBERED STEPS
   ============================================================ */

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

    text-align: justify !important;
}


.cw-ai-response .cw-ai-steps li:last-child {
    margin-bottom: 0;
}


/* ============================================================
   BULLET POINTS
   ============================================================ */

.cw-ai-response .cw-ai-bullets {
    display: block;

    margin-top: 8px;

    margin-bottom: 12px;

    padding-left: 30px;

    text-align: left !important;
}


.cw-ai-response .cw-ai-bullets li {
    margin-bottom: 8px;

    padding-left: 5px;

    line-height: 1.65;

    text-align: justify !important;
}


.cw-ai-response .cw-ai-bullets li:last-child {
    margin-bottom: 0;
}


/* ============================================================
   EMPTY SPACE
   ============================================================ */

.cw-ai-response .cw-space {
    display: block;

    height: 7px;
}


/* ============================================================
   BOLD
   ============================================================ */

.cw-ai-response strong {
    font-weight: 700;
}


/* ============================================================
   ITALIC
   ============================================================ */

.cw-ai-response em {
    font-style: italic;
}


/* ============================================================
   INLINE CODE
   ============================================================ */

.cw-ai-response code {
    background: rgba(0, 0, 0, .06);

    color: inherit;

    padding: 2px 5px;

    border-radius: 4px;

    font-size: .9em;

    font-family: monospace;
}


/* ============================================================
   LINKS
   ============================================================ */

.cw-ai-response a {
    text-decoration: underline;
}


/* ============================================================
   CHAT BUBBLE
   ============================================================ */

.cw-bubble.cw-ai-response {
    max-width: 100%;

    box-sizing: border-box;

    overflow-wrap: anywhere;
}


/* ============================================================
   TYPING
   ============================================================ */

.cw-typing-row {
    margin-bottom: 5px;
}


/* ============================================================
   PERSISTENT CHAT HISTORY
   ============================================================ */

.cw-history-panel {
    position: absolute;

    top: 64px;

    left: 0;

    right: 0;

    z-index: 20;

    background: #fff;

    border-bottom: 1px solid rgba(0, 0, 0, .08);

    box-shadow: 0 8px 24px rgba(0, 0, 0, .10);

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

    border-bottom: 1px solid rgba(0, 0, 0, .06);
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

    transition:
        background .15s ease,
        transform .10s ease;
}


.cw-history-item:hover,
.cw-history-item.active {
    background: rgba(91, 111, 214, .08);
}


.cw-history-item:active {
    transform: scale(.99);
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


/* ============================================================
   HISTORY LOADING
   ============================================================ */

.cw-history-loading {
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

    const CHAT_ENDPOINT =
        '/api/chatbot/chat';

    const CHAT_HISTORY_ENDPOINT =
        '/api/chatbot/history';

    const CHAT_CONVERSATION_ENDPOINT =
        '/api/chatbot/conversation';


    /* ========================================================
       CURRENT CONVERSATION
       ======================================================== */

    let conversationId = null;


    /* ========================================================
       REQUEST FLAGS
       ======================================================== */

    let historyLoading = false;

    let chatSending = false;


    /* ========================================================
       CURRENT TIME
       ======================================================== */

    function now() {

        const d = new Date();

        return (
            d.getHours()
                .toString()
                .padStart(2, '0')
            +
            ':'
            +
            d.getMinutes()
                .toString()
                .padStart(2, '0')
        );

    }


    /* ========================================================
       HTML ESCAPE
       ======================================================== */

    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /* ========================================================
       FORMAT INLINE CONTENT
       ======================================================== */

    function formatInline(text) {

        let value = String(text ?? '');


        /*
         * Convert HTML line breaks.
         */

        value = value.replace(
            /<br\s*\/?>/gi,
            '\n'
        );


        /*
         * Convert HTML bold to Markdown.
         */

        value = value.replace(
            /<strong>(.*?)<\/strong>/gi,
            '**$1**'
        );

        value = value.replace(
            /<b>(.*?)<\/b>/gi,
            '**$1**'
        );


        /*
         * Convert HTML italic to Markdown.
         */

        value = value.replace(
            /<em>(.*?)<\/em>/gi,
            '*$1*'
        );

        value = value.replace(
            /<i>(.*?)<\/i>/gi,
            '*$1*'
        );


        /*
         * Remove any remaining HTML.
         *
         * This protects the chatbot from
         * arbitrary HTML returned by AI.
         */

        value = value.replace(
            /<[^>]*>/g,
            ''
        );


        /*
         * Escape HTML.
         */

        let result =
            escapeHtml(value);


        /*
         * Bold.
         */

        result = result.replace(
            /\*\*(.*?)\*\*/g,
            '<strong>$1</strong>'
        );


        /*
         * Italic.
         */

        result = result.replace(
            /(^|[^*])\*([^*]+)\*(?!\*)/g,
            '$1<em>$2</em>'
        );


        /*
         * Inline code.
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


        text = String(text);


        /*
         * Normalize escaped newlines.
         */

        text = text
            .replace(/\\r\\n/g, '\n')
            .replace(/\\n/g, '\n')
            .replace(/\r\n/g, '\n')
            .replace(/\r/g, '\n');


        /*
         * Convert HTML <br> to newline.
         */

        text = text.replace(
            /<br\s*\/?>/gi,
            '\n'
        );


        /*
         * Force newline before numbered
         * steps when AI returns them inline.
         *
         * Example:
         *
         * Please do the following: 1. Register
         * 2. Login
         *
         * becomes:
         *
         * Please do the following:
         * 1. Register
         * 2. Login
         */

        text = text.replace(
            /(\s)(\d{1,2})\.\s+/g,
            '\n$2. '
        );


        /*
         * Number immediately after colon.
         */

        text = text.replace(
            /:\s*(\d{1,2})\.\s+/g,
            ':\n$1. '
        );


        /*
         * Normalize spaces while preserving
         * line breaks.
         */

        text = text.replace(
            /[ \t]+/g,
            ' '
        );


        const lines =
            text.split('\n');


        let html = '';

        let inNumberedList = false;

        let inBulletList = false;


        /* ====================================================
           CLOSE NUMBERED LIST
           ==================================================== */

        function closeNumberedList() {

            if (inNumberedList) {

                html += '</ol>';

                inNumberedList = false;

            }

        }


        /* ====================================================
           CLOSE BULLET LIST
           ==================================================== */

        function closeBulletList() {

            if (inBulletList) {

                html += '</ul>';

                inBulletList = false;

            }

        }


        /* ====================================================
           PROCESS LINES
           ==================================================== */

        lines.forEach(
            function (rawLine) {

                const line =
                    rawLine.trim();


                /*
                 * Empty line.
                 */

                if (!line) {

                    closeNumberedList();

                    closeBulletList();

                    html +=
                        '<div class="cw-space"></div>';

                    return;

                }


                /*
                 * Numbered list.
                 *
                 * Supports:
                 *
                 * 1. Text
                 * 2. Text
                 * 3. Text
                 */

                const numberedMatch =
                    line.match(
                        /^(\d{1,2})[.)]\s+(.*)$/
                    );


                if (numberedMatch) {

                    closeBulletList();


                    if (!inNumberedList) {

                        html +=
                            '<ol class="cw-ai-steps">';

                        inNumberedList = true;

                    }


                    html +=
                        '<li>' +
                        formatInline(
                            numberedMatch[2]
                        ) +
                        '</li>';

                    return;

                }


                /*
                 * Bullet list.
                 *
                 * Supports:
                 *
                 * - Text
                 * • Text
                 * * Text
                 */

                const bulletMatch =
                    line.match(
                        /^[-•*]\s+(.*)$/
                    );


                if (bulletMatch) {

                    closeNumberedList();


                    if (!inBulletList) {

                        html +=
                            '<ul class="cw-ai-bullets">';

                        inBulletList = true;

                    }


                    html +=
                        '<li>' +
                        formatInline(
                            bulletMatch[1]
                        ) +
                        '</li>';

                    return;

                }


                /*
                 * Normal paragraph.
                 */

                closeNumberedList();

                closeBulletList();


                html +=
                    '<p class="cw-ai-paragraph">' +
                    formatInline(line) +
                    '</p>';

            }
        );


        closeNumberedList();

        closeBulletList();


        return html;

    }


    /* ========================================================
       ADD MESSAGE
       ======================================================== */

    function addMsg(text, isUser) {

        const wrap =
            document.getElementById(
                'cwMessages'
            );


        if (!wrap) {
            return;
        }


        /*
         * Remove quick questions after
         * the first actual message.
         */

        const chips =
            document.getElementById(
                'cwChips'
            );


        if (chips) {
            chips.remove();
        }


        const row =
            document.createElement('div');


        row.className =
            'cw-row ' +
            (
                isUser
                    ? 'cw-row--user'
                    : 'cw-row--bot'
            );


        /* ====================================================
           USER MESSAGE
           ==================================================== */

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


        /* ====================================================
           AI MESSAGE
           ==================================================== */

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
            document.getElementById(
                'cwMessages'
            );


        if (!wrap) {
            return;
        }


        const existing =
            document.getElementById(
                'cwTyping'
            );


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
            document.getElementById(
                'cwTyping'
            );


        if (t) {
            t.remove();
        }

    }


    /* ========================================================
       FETCH CHAT HISTORY
       ======================================================== */

    async function fetchChatHistory() {

        const response =
            await fetch(
                CHAT_HISTORY_ENDPOINT +
                '?_=' +
                Date.now(),
                {
                    method: 'GET',

                    credentials:
                        'same-origin',

                    headers: {
                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'
                    },

                    cache: 'no-store'
                }
            );


        if (!response.ok) {

            throw new Error(
                'History request failed: HTTP ' +
                response.status
            );

        }


        const contentType =
            response.headers.get(
                'content-type'
            ) || '';


        if (
            !contentType
                .toLowerCase()
                .includes('application/json')
        ) {

            throw new Error(
                'History endpoint did not return JSON. Content-Type: ' +
                contentType
            );

        }


        const data =
            await response.json();


        console.log(
            'BIS Chat History:',
            data
        );


        return data;

    }


    /* ========================================================
       LOAD CHAT HISTORY
       ======================================================== */

    async function loadChatHistory(
        loadActiveConversation = true
    ) {

        if (historyLoading) {
            return null;
        }


        historyLoading = true;


        try {

            const data =
                await fetchChatHistory();


            if (
                !data ||
                data.success !== true
            ) {

                console.warn(
                    'BIS chat history unavailable:',
                    data
                );

                return null;

            }


            document.__cwConversations =
                Array.isArray(
                    data.conversations
                )
                    ? data.conversations
                    : [];


            renderConversationList(
                document.__cwConversations
            );


            /*
             * Restore active conversation
             * during initial loading.
             */

            if (
                loadActiveConversation &&
                data.active_conversation_id !==
                    undefined &&
                data.active_conversation_id !==
                    null &&
                Number(
                    data.active_conversation_id
                ) > 0
            ) {

                conversationId =
                    Number(
                        data.active_conversation_id
                    );


                if (
                    Array.isArray(
                        data.messages
                    )
                ) {

                    renderStoredMessages(
                        data.messages
                    );

                }


                renderConversationList(
                    document.__cwConversations
                );

            }


            return data;

        }
        catch (error) {

            console.error(
                'Could not load BIS chat history:',
                error
            );

            return null;

        }
        finally {

            historyLoading = false;

        }

    }


    /* ========================================================
       REFRESH CONVERSATION LIST ONLY
       ======================================================== */

    async function refreshConversationList() {

        try {

            const data =
                await fetchChatHistory();


            if (
                !data ||
                data.success !== true
            ) {

                console.warn(
                    'Could not refresh conversation list:',
                    data
                );

                return;

            }


            document.__cwConversations =
                Array.isArray(
                    data.conversations
                )
                    ? data.conversations
                    : [];


            renderConversationList(
                document.__cwConversations
            );

        }
        catch (error) {

            console.error(
                'Could not refresh BIS conversation list:',
                error
            );

        }

    }


    /* ========================================================
       RENDER CONVERSATION LIST
       ======================================================== */

    function renderConversationList(
        conversations
    ) {

        document.__cwConversations =
            Array.isArray(conversations)
                ? conversations
                : [];


        const list =
            document.getElementById(
                'cwHistoryList'
            );


        if (!list) {
            return;
        }


        list.innerHTML = '';


        if (
            document.__cwConversations.length === 0
        ) {

            list.innerHTML = `
                <div class="cw-history-empty">
                    No saved conversations yet.
                </div>
            `;

            return;

        }


        document.__cwConversations.forEach(
            function (conversation) {

                const button =
                    document.createElement(
                        'button'
                    );


                button.type =
                    'button';


                button.className =
                    'cw-history-item';


                if (
                    conversationId !== null &&
                    Number(conversation.id) ===
                    Number(conversationId)
                ) {

                    button.classList.add(
                        'active'
                    );

                }


                const title =
                    document.createElement(
                        'span'
                    );


                title.className =
                    'cw-history-item-title';


                title.textContent =
                    conversation.title ||
                    'Conversation';


                const date =
                    document.createElement(
                        'span'
                    );


                date.className =
                    'cw-history-item-date';


                date.textContent =
                    formatConversationDate(
                        conversation.updated_at ||
                        conversation.created_at ||
                        ''
                    );


                button.appendChild(
                    title
                );


                button.appendChild(
                    date
                );


                button.addEventListener(
                    'click',
                    function () {

                        loadConversation(
                            Number(
                                conversation.id
                            )
                        );

                    }
                );


                list.appendChild(
                    button
                );

            }
        );

    }


    /* ========================================================
       FORMAT CONVERSATION DATE
       ======================================================== */

    function formatConversationDate(
        value
    ) {

        if (!value) {
            return '';
        }


        const normalized =
            String(value).replace(
                ' ',
                'T'
            );


        const date =
            new Date(
                normalized
            );


        if (
            isNaN(
                date.getTime()
            )
        ) {

            return String(value);

        }


        const today =
            new Date();


        if (
            date.toDateString() ===
            today.toDateString()
        ) {

            return (
                date.getHours()
                    .toString()
                    .padStart(2, '0')
                +
                ':'
                +
                date.getMinutes()
                    .toString()
                    .padStart(2, '0')
            );

        }


        return date.toLocaleDateString(
            undefined,
            {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }
        );

    }


    /* ========================================================
       LOAD ONE CONVERSATION
       ======================================================== */

    async function loadConversation(id) {

        if (!id) {
            return;
        }


        try {

            const response =
                await fetch(
                    CHAT_CONVERSATION_ENDPOINT +
                    '/' +
                    encodeURIComponent(id) +
                    '?_=' +
                    Date.now(),
                    {
                        method: 'GET',

                        credentials:
                            'same-origin',

                        headers: {
                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest'
                        },

                        cache: 'no-store'
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Conversation request failed: HTTP ' +
                    response.status
                );

            }


            const contentType =
                response.headers.get(
                    'content-type'
                ) || '';


            if (
                !contentType
                    .toLowerCase()
                    .includes('application/json')
            ) {

                throw new Error(
                    'Conversation endpoint did not return JSON.'
                );

            }


            const data =
                await response.json();


            console.log(
                'Selected BIS Conversation:',
                data
            );


            if (
                !data ||
                data.success !== true
            ) {

                console.error(
                    'Conversation could not be loaded:',
                    data
                );

                return;

            }


            conversationId =
                Number(id);


            renderStoredMessages(
                Array.isArray(
                    data.messages
                )
                    ? data.messages
                    : []
            );


            const panel =
                document.getElementById(
                    'cwHistoryPanel'
                );


            if (panel) {
                panel.hidden = true;
            }


            renderConversationList(
                document.__cwConversations ||
                []
            );


            const input =
                document.getElementById(
                    'cwInput'
                );


            if (input) {
                input.focus();
            }

        }
        catch (error) {

            console.error(
                'Could not load BIS conversation:',
                error
            );

        }

    }


    /* ========================================================
       RENDER STORED MESSAGES
       ======================================================== */

    function renderStoredMessages(
        messages
    ) {

        const wrap =
            document.getElementById(
                'cwMessages'
            );


        if (!wrap) {
            return;
        }


        wrap.innerHTML = '';


        if (
            !Array.isArray(messages) ||
            messages.length === 0
        ) {

            addMsg(
                "Hello! I'm the BIS Assistant 👋\n\nHow can I help you today?",
                false
            );

            return;

        }


        messages.forEach(
            function (item) {

                addMsg(
                    item.message || '',
                    item.sender === 'user'
                );

            }
        );


        wrap.scrollTop =
            wrap.scrollHeight;

    }


    /* ========================================================
       TOGGLE HISTORY
       ======================================================== */

    window.cwToggleHistory =
        async function () {

            const panel =
                document.getElementById(
                    'cwHistoryPanel'
                );


            if (!panel) {
                return;
            }


            panel.hidden =
                !panel.hidden;


            if (!panel.hidden) {

                const list =
                    document.getElementById(
                        'cwHistoryList'
                    );


                if (list) {

                    list.innerHTML = `
                        <div class="cw-history-loading">
                            Loading conversations...
                        </div>
                    `;

                }


                await refreshConversationList();

            }

        };


    /* ========================================================
       NEW CONVERSATION
       ======================================================== */

    window.cwNewChat =
        function () {

            conversationId = null;


            const panel =
                document.getElementById(
                    'cwHistoryPanel'
                );


            if (panel) {
                panel.hidden = true;
            }


            const wrap =
                document.getElementById(
                    'cwMessages'
                );


            if (wrap) {

                wrap.innerHTML = '';


                addMsg(
                    "Hello! I'm the BIS Assistant 👋\n\nHow can I help you today?",
                    false
                );


                const chips =
                    document.createElement(
                        'div'
                    );


                chips.className =
                    'cw-chips';


                chips.id =
                    'cwChips';


                chips.innerHTML = `

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

                `;


                wrap.appendChild(
                    chips
                );

            }


            renderConversationList(
                document.__cwConversations ||
                []
            );


            const input =
                document.getElementById(
                    'cwInput'
                );


            if (input) {

                input.value = '';

                input.focus();

            }

        };


    /* ========================================================
       SEND MESSAGE
       ======================================================== */

    window.cwSend =
        async function () {

            if (chatSending) {
                return;
            }


            const inp =
                document.getElementById(
                    'cwInput'
                );


            if (!inp) {
                return;
            }


            const msg =
                inp.value.trim();


            if (!msg) {
                return;
            }


            chatSending = true;


            /*
             * Display user message immediately.
             */

            addMsg(
                msg,
                true
            );


            inp.value = '';


            const unread =
                document.getElementById(
                    'cwUnread'
                );


            if (unread) {
                unread.style.display = 'none';
            }


            typing();


            try {

                const body =
                    new URLSearchParams();


                body.append(
                    'message',
                    msg
                );


                if (
                    conversationId !== null &&
                    Number(conversationId) > 0
                ) {

                    body.append(
                        'conversation_id',
                        String(
                            conversationId
                        )
                    );

                }
                else {

                    body.append(
                        'conversation_id',
                        ''
                    );

                }


                const response =
                    await fetch(
                        CHAT_ENDPOINT,
                        {
                            method: 'POST',

                            headers: {

                                'Content-Type':
                                    'application/x-www-form-urlencoded; charset=UTF-8',

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'

                            },

                            credentials:
                                'same-origin',

                            body:
                                body.toString()

                        }
                    );


                if (!response.ok) {

                    throw new Error(
                        'Chat request failed: HTTP ' +
                        response.status
                    );

                }


                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                if (
                    !contentType
                        .toLowerCase()
                        .includes('application/json')
                ) {

                    throw new Error(
                        'Chat API did not return JSON.'
                    );

                }


                const data =
                    await response.json();


                removeTyping();


                console.log(
                    'BIS AI Response:',
                    data
                );


                /* =================================================
                   SUCCESS
                   ================================================= */

                if (
                    data &&
                    data.success === true &&
                    data.response
                ) {

                    /*
                     * Save returned conversation ID.
                     */

                    if (
                        data.conversation_id !==
                            undefined &&
                        data.conversation_id !==
                            null &&
                        Number(
                            data.conversation_id
                        ) > 0
                    ) {

                        conversationId =
                            Number(
                                data.conversation_id
                            );

                    }


                    /*
                     * addMsg() automatically calls
                     * formatAIResponse() for AI messages.
                     *
                     * Therefore:
                     *
                     * data.response
                     *
                     * should NOT be formatted here again.
                     */

                    addMsg(
                        data.response,
                        false
                    );


                    /*
                     * Refresh recent conversations.
                     *
                     * This does NOT reload current messages.
                     */

                    await refreshConversationList();


                    /*
                     * Highlight current conversation.
                     */

                    renderConversationList(
                        document.__cwConversations ||
                        []
                    );


                    return;

                }


                /* =================================================
                   API ERROR
                   ================================================= */

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

                removeTyping();


                addMsg(
                    'Sorry, I could not connect to the BIS AI service. Please try again.',
                    false
                );


                console.error(
                    'Chatbot request failed:',
                    error
                );

            }
            finally {

                chatSending = false;

            }

        };


    /* ========================================================
       QUICK QUESTION
       ======================================================== */

    window.cwQuick =
        function (msg) {

            const chips =
                document.getElementById(
                    'cwChips'
                );


            if (chips) {
                chips.remove();
            }


            const input =
                document.getElementById(
                    'cwInput'
                );


            if (!input) {
                return;
            }


            input.value =
                msg;


            window.cwSend();

        };


    /* ========================================================
       OPEN CHAT
       ======================================================== */

    window.cwOpen =
        function () {

            const panel =
                document.getElementById(
                    'cwPanel'
                );


            const wrap =
                document.getElementById(
                    'cwWrap'
                );


            const unread =
                document.getElementById(
                    'cwUnread'
                );


            if (
                !panel ||
                !wrap
            ) {

                return;

            }


            panel.classList.toggle(
                'cw-open'
            );


            wrap.classList.toggle(
                'cw-active'
            );


            if (
                panel.classList.contains(
                    'cw-open'
                )
            ) {

                if (unread) {

                    unread.style.display =
                        'none';

                }


                /*
                 * Refresh conversation list
                 * whenever chatbot is opened.
                 */

                refreshConversationList();


                setTimeout(
                    function () {

                        const input =
                            document.getElementById(
                                'cwInput'
                            );


                        if (input) {
                            input.focus();
                        }

                    },
                    150
                );

            }

        };


    /* ========================================================
       CLOSE CHAT
       ======================================================== */

    window.cwClose =
        function () {

            const panel =
                document.getElementById(
                    'cwPanel'
                );


            const wrap =
                document.getElementById(
                    'cwWrap'
                );


            if (panel) {

                panel.classList.remove(
                    'cw-open'
                );

            }


            if (wrap) {

                wrap.classList.remove(
                    'cw-active'
                );

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
                document.activeElement.id ===
                    'cwInput'
            ) {

                event.preventDefault();

                window.cwSend();

            }

        }
    );


    /* ========================================================
       INITIALIZE
       ======================================================== */

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            loadChatHistory(true);

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

    <!-- Mobile Menu -->
    <button
        class="db-menu-toggle"
        onclick="document.getElementById('sidebar').classList.toggle('open')"
        aria-label="Toggle menu">

        <i class="fas fa-bars"></i>

    </button>


    <!-- Page Title -->
    <div class="db-topbar-title">

        <h1>
            <?= $pageTitle ?? 'Dashboard' ?>
        </h1>

        <span>
            <?= date('l, F j, Y') ?>
        </span>

    </div>


    <!-- Right Side -->
    <div class="db-topbar-right">

        <!-- ====================================================
             NOTIFICATIONS
             ==================================================== -->

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


        <!-- ====================================================
             AVATAR
             ==================================================== -->

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


        <!-- ====================================================
             USERNAME
             ==================================================== -->

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

        .then(
            function (r) {
                return r.json();
            }
        )

        .then(
            function (data) {

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

            }
        )

        .catch(
            function () {}
        );

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

        .then(
            function (r) {
                return r.json();
            }
        )

        .then(
            function (data) {

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

            }
        )

        .catch(
            function () {}
        );

    }


    pollAdmin();


    setInterval(
        pollAdmin,
        60000
    );

})();

</script>

<?php endif; ?>