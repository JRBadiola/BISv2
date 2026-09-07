<!-- ============================================================
     BIS CHATBOT WIDGET
     ============================================================ -->

<div class="cw-wrap" id="cwWrap">

    <!-- ========================================================
         CHAT TOGGLE
         ======================================================== -->

    <button
        class="cw-toggle"
        id="cwToggle"
        type="button"
        onclick="cwOpen()"
        aria-label="Open chat">

        <span class="cw-toggle-icon" id="cwIcon">
            <i class="fas fa-comment-dots"></i>
        </span>

        <span
            class="cw-unread"
            id="cwUnread"
            style="display:none;">
            1
        </span>

    </button>


    <!-- ========================================================
         CHAT PANEL
         ======================================================== -->

    <div class="cw-panel" id="cwPanel">

        <!-- ====================================================
             HEADER
             ==================================================== -->

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


        <!-- ====================================================
             HISTORY
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
             DATE
             ==================================================== -->

        <div class="cw-date-divider">

            <span>
                Today
            </span>

        </div>


        <!-- ====================================================
             MESSAGES
             ==================================================== -->

        <div
            class="cw-messages"
            id="cwMessages">

            <!-- ==================================================
                 INITIAL AI MESSAGE
                 ================================================== -->

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


            <!-- ==================================================
                 QUICK QUESTIONS
                 ================================================== -->

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
             FOOTER
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
                    onclick="cwSend()"
                    aria-label="Send message">

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
   WIDGET
   ============================================================ */

.cw-wrap {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 99999;
    font-family: inherit;
}


/* ============================================================
   TOGGLE
   ============================================================ */

.cw-toggle {
    position: relative;

    width: 58px;
    height: 58px;

    border: none;
    border-radius: 50%;

    background: #5b6fd6;
    color: #fff;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;

    box-shadow:
        0 8px 25px rgba(0, 0, 0, .20);

    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.cw-toggle:hover {
    transform: translateY(-2px);

    box-shadow:
        0 10px 30px rgba(0, 0, 0, .25);
}

.cw-toggle:active {
    transform: scale(.96);
}

.cw-toggle-icon {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 22px;
}


/* ============================================================
   UNREAD
   ============================================================ */

.cw-unread {
    position: absolute;

    top: -3px;
    right: -3px;

    min-width: 19px;
    height: 19px;

    padding: 0 5px;

    border-radius: 50px;

    background: #e74c3c;
    color: #fff;

    font-size: 10px;
    font-weight: 700;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 2px solid #fff;
}


/* ============================================================
   PANEL
   ============================================================ */

.cw-panel {
    position: absolute;

    right: 0;
    bottom: 72px;

    width: 380px;

    max-width: calc(100vw - 30px);

    height: 570px;

    max-height: calc(100vh - 110px);

    background: #fff;

    border-radius: 18px;

    overflow: hidden;

    box-shadow:
        0 18px 55px rgba(0, 0, 0, .22);

    display: flex;
    flex-direction: column;

    opacity: 0;
    visibility: hidden;

    transform:
        translateY(14px)
        scale(.98);

    pointer-events: none;

    transition:
        opacity .2s ease,
        visibility .2s ease,
        transform .2s ease;
}

.cw-panel.cw-open {

    opacity: 1;

    visibility: visible;

    transform:
        translateY(0)
        scale(1);

    pointer-events: auto;
}


/* ============================================================
   HEADER
   ============================================================ */

.cw-header {
    min-height: 64px;

    padding: 10px 12px;

    background: #5b6fd6;

    color: #fff;

    display: flex;

    align-items: center;
    justify-content: space-between;

    flex-shrink: 0;
}

.cw-header-left {
    display: flex;

    align-items: center;

    gap: 10px;

    min-width: 0;
}

.cw-header-avatar {
    position: relative;

    width: 40px;
    height: 40px;

    border-radius: 50%;

    background:
        rgba(255, 255, 255, .18);

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;
}

.cw-header-avatar i {
    font-size: 18px;
}

.cw-header-dot {
    position: absolute;

    right: 0;
    bottom: 1px;

    width: 9px;
    height: 9px;

    border-radius: 50%;

    background: #2ecc71;

    border: 2px solid #5b6fd6;
}

.cw-header-text {
    display: flex;

    flex-direction: column;

    min-width: 0;
}

.cw-header-name {
    font-size: 14px;
    font-weight: 700;
}

.cw-header-sub {
    margin-top: 2px;

    font-size: 10px;

    opacity: .85;
}

.cw-header-actions {
    display: flex;

    align-items: center;

    gap: 3px;
}

.cw-hbtn {
    width: 32px;
    height: 32px;

    border: 0;
    border-radius: 8px;

    background: transparent;

    color: #fff;

    cursor: pointer;

    display: flex;

    align-items: center;
    justify-content: center;
}

.cw-hbtn:hover {
    background:
        rgba(255, 255, 255, .15);
}


/* ============================================================
   DATE
   ============================================================ */

.cw-date-divider {
    padding: 8px 12px;

    text-align: center;

    flex-shrink: 0;
}

.cw-date-divider span {
    display: inline-block;

    padding: 3px 9px;

    border-radius: 20px;

    background:
        rgba(0, 0, 0, .05);

    font-size: 10px;

    color: #777;
}


/* ============================================================
   MESSAGES
   ============================================================ */

.cw-messages {
    flex: 1;

    overflow-y: auto;

    padding: 14px;

    background: #f7f8fc;

    scroll-behavior: auto;
}


/* ============================================================
   IMPORTANT ALIGNMENT RULES
   ============================================================ */

/*
 * AI = LEFT
 * STAFF = LEFT
 * SYSTEM = LEFT
 */

#cwMessages .cw-row--bot,
#cwMessages .cw-row--staff,
#cwMessages .cw-row--system {

    display: flex !important;

    width: 100% !important;

    justify-content: flex-start !important;

    align-items: flex-start !important;
}


/*
 * RESIDENT = RIGHT
 */

#cwMessages .cw-row--user {

    display: flex !important;

    width: 100% !important;

    justify-content: flex-end !important;

    align-items: flex-start !important;
}


/*
 * User body goes all the way to the right.
 */

#cwMessages .cw-row--user .cw-body {

    margin-left: auto !important;

    margin-right: 0 !important;

    max-width: 78%;
}


/*
 * User timestamp = RIGHT
 */

#cwMessages .cw-row--user .cw-ts {

    text-align: right !important;
}


/* ============================================================
   AVATAR
   ============================================================ */

.cw-avatar {

    width: 30px;
    height: 30px;

    border-radius: 50%;

    background: #5b6fd6;

    color: #fff;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    margin-right: 8px;

    font-size: 12px;
}


/* ============================================================
   BODY
   ============================================================ */

.cw-body {
    max-width: 82%;
}


/* ============================================================
   BUBBLE
   ============================================================ */

.cw-bubble {

    padding: 10px 12px;

    border-radius: 14px;

    background: #fff;

    color: #333;

    font-size: 13px;

    box-shadow:
        0 1px 4px rgba(0, 0, 0, .06);

    word-wrap: break-word;

    overflow-wrap: anywhere;
}


/* ============================================================
   USER BUBBLE
   ============================================================ */

#cwMessages .cw-row--user .cw-bubble {

    background: #5b6fd6;

    color: #fff;

    border-bottom-right-radius: 4px;
}


/* ============================================================
   AI BUBBLE
   ============================================================ */

#cwMessages .cw-row--bot .cw-bubble {

    border-bottom-left-radius: 4px;
}


/* ============================================================
   TIMESTAMP
   ============================================================ */

.cw-ts {

    display: block;

    margin-top: 4px;

    font-size: 9px;

    color: #999;
}


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

.cw-ai-response .cw-ai-paragraph {

    display: block;

    margin:
        0 0 10px 0;

    padding: 0;

    text-align: justify !important;

    line-height: 1.65;
}

.cw-ai-response .cw-ai-paragraph:last-child {

    margin-bottom: 0;
}


/* ============================================================
   LISTS
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


/* ============================================================
   QUICK CHIPS
   ============================================================ */

.cw-chips {

    display: flex;

    flex-wrap: wrap;

    gap: 7px;

    margin-top: 10px;
}

.cw-chip {

    border:
        1px solid rgba(91, 111, 214, .25);

    background: #fff;

    color: #4f60bd;

    border-radius: 20px;

    padding: 7px 10px;

    font-size: 10px;

    cursor: pointer;
}

.cw-chip:hover {

    background:
        rgba(91, 111, 214, .08);
}


/* ============================================================
   TYPING
   ============================================================ */

.cw-typing-row {

    margin-bottom: 5px;
}

.cw-typing {

    display: flex;

    align-items: center;

    gap: 4px;
}

.cw-typing span {

    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #999;

    animation:
        cwTyping 1.2s
        infinite
        ease-in-out;
}

.cw-typing span:nth-child(2) {

    animation-delay: .15s;
}

.cw-typing span:nth-child(3) {

    animation-delay: .30s;
}

@keyframes cwTyping {

    0%,
    60%,
    100% {

        transform: translateY(0);

        opacity: .45;

    }

    30% {

        transform: translateY(-4px);

        opacity: 1;

    }

}


/* ============================================================
   HISTORY
   ============================================================ */

.cw-history-panel {

    position: absolute;

    top: 64px;

    left: 0;
    right: 0;

    z-index: 20;

    background: #fff;

    border-bottom:
        1px solid rgba(0, 0, 0, .08);

    box-shadow:
        0 8px 24px rgba(0, 0, 0, .10);

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

    border-bottom:
        1px solid rgba(0, 0, 0, .06);
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

    background:
        rgba(91, 111, 214, .08);
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

.cw-history-empty,
.cw-history-loading {

    padding: 18px 10px;

    text-align: center;

    font-size: 12px;

    opacity: .65;
}


/* ============================================================
   FOOTER
   ============================================================ */

.cw-footer {

    padding:
        10px 12px 8px;

    background: #fff;

    border-top:
        1px solid rgba(0, 0, 0, .06);

    flex-shrink: 0;
}

.cw-input-wrap {

    display: flex;

    align-items: center;

    gap: 7px;
}

.cw-input {

    flex: 1;

    min-width: 0;

    height: 40px;

    padding: 0 12px;

    border:
        1px solid #ddd;

    border-radius: 20px;

    outline: none;

    font-size: 13px;

    box-sizing: border-box;
}

.cw-input:focus {

    border-color: #5b6fd6;
}

.cw-send {

    width: 40px;
    height: 40px;

    border: 0;

    border-radius: 50%;

    background: #5b6fd6;

    color: #fff;

    cursor: pointer;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;
}

.cw-powered {

    margin:
        6px 0 0;

    text-align: center;

    font-size: 9px;

    color: #aaa;
}


/* ============================================================
   MOBILE
   ============================================================ */

@media (max-width: 520px) {

    .cw-wrap {

        right: 15px;

        bottom: 15px;
    }

    .cw-panel {

        right: -5px;

        width:
            calc(100vw - 30px);

        height:
            min(570px, calc(100vh - 90px));
    }

    .cw-toggle {

        width: 54px;

        height: 54px;
    }

}

</style>


<!-- ============================================================
     CHATBOT JAVASCRIPT
     ============================================================ -->

<script>

(function () {

    'use strict';


    /* ========================================================
       USER ROLE
       ======================================================== */

    const USER_ROLE =
        <?= json_encode(
            (string)(session()->get('role') ?? 'resident')
        ) ?>;


    /* ========================================================
       CHAT ENDPOINT
       ======================================================== */

    const CHAT_ENDPOINT = (() => {

        switch (
            String(USER_ROLE).toLowerCase()
        ) {

            case 'resident':
                return '/resident/chatbot/api/chat';

            case 'secretary':
                return '/secretary/chatbot/api/chat';

            case 'captain':
                return '/captain/chatbot/api/chat';

            default:
                return '/api/chatbot/chat';

        }

    })();


    /* ========================================================
       ENDPOINTS
       ======================================================== */

    const CHAT_HISTORY_ENDPOINT =
        '/api/chatbot/history';

    const CHAT_CONVERSATION_ENDPOINT =
        '/api/chatbot/conversation';

    const SUPPORT_REQUEST_ENDPOINT =
        '/resident/chatbot/api/request-human';

    const SUPPORT_STATUS_ENDPOINT =
        '/resident/chatbot/api/support-status';


    /* ========================================================
       STATE
       ======================================================== */

    let conversationId = null;

    let supportMode = 'ai';

    let assignedStaffId = null;

    let historyLoading = false;

    let chatSending = false;

    let supportPollingStarted = false;


    /* ========================================================
       TIME
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
       ESCAPE HTML
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
       INLINE FORMAT
       ======================================================== */

    function formatInline(text) {

        let value =
            String(text ?? '');


        value = value.replace(
            /<br\s*\/?>/gi,
            '\n'
        );


        value = value.replace(
            /<strong>(.*?)<\/strong>/gi,
            '**$1**'
        );


        value = value.replace(
            /<b>(.*?)<\/b>/gi,
            '**$1**'
        );


        value = value.replace(
            /<em>(.*?)<\/em>/gi,
            '*$1*'
        );


        value = value.replace(
            /<i>(.*?)<\/i>/gi,
            '*$1*'
        );


        value = value.replace(
            /<[^>]*>/g,
            ''
        );


        let result =
            escapeHtml(value);


        result = result.replace(
            /\*\*(.*?)\*\*/g,
            '<strong>$1</strong>'
        );


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


        text =
            String(text);


        text = text
            .replace(/\\r\\n/g, '\n')
            .replace(/\\n/g, '\n')
            .replace(/\r\n/g, '\n')
            .replace(/\r/g, '\n');


        text = text.replace(
            /<br\s*\/?>/gi,
            '\n'
        );


        const lines =
            text.split('\n');


        let html = '';

        let inNumberedList = false;

        let inBulletList = false;


        function closeNumberedList() {

            if (inNumberedList) {

                html += '</ol>';

                inNumberedList = false;

            }

        }


        function closeBulletList() {

            if (inBulletList) {

                html += '</ul>';

                inBulletList = false;

            }

        }


        lines.forEach(
            function (rawLine) {

                const line =
                    rawLine.trim();


                if (!line) {

                    closeNumberedList();

                    closeBulletList();

                    html +=
                        '<div class="cw-space"></div>';

                    return;

                }


                const numbered =
                    line.match(
                        /^(\d{1,2})[.)]\s+(.*)$/
                    );


                if (numbered) {

                    closeBulletList();


                    if (!inNumberedList) {

                        html +=
                            '<ol class="cw-ai-steps">';

                        inNumberedList =
                            true;

                    }


                    html +=
                        '<li>' +
                        formatInline(
                            numbered[2]
                        ) +
                        '</li>';

                    return;

                }


                const bullet =
                    line.match(
                        /^[-•*]\s+(.*)$/
                    );


                if (bullet) {

                    closeNumberedList();


                    if (!inBulletList) {

                        html +=
                            '<ul class="cw-ai-bullets">';

                        inBulletList =
                            true;

                    }


                    html +=
                        '<li>' +
                        formatInline(
                            bullet[1]
                        ) +
                        '</li>';

                    return;

                }


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
       CHECK BOTTOM
       ======================================================== */

    function isNearBottom(
        wrap,
        threshold = 100
    ) {

        if (!wrap) {

            return true;

        }


        return (
            wrap.scrollHeight -
            wrap.scrollTop -
            wrap.clientHeight
        ) <= threshold;

    }


    /* ========================================================
       ADD MESSAGE
       ======================================================== */

    function addMsg(
        text,
        isUser = false,
        senderType = null,
        autoScroll = true
    ) {

        const wrap =
            document.getElementById(
                'cwMessages'
            );


        if (!wrap) {

            return;

        }


        const chips =
            document.getElementById(
                'cwChips'
            );


        if (chips) {

            chips.remove();

        }


        /*
         * Normalize sender type.
         */

        let type =
            String(
                senderType || ''
            ).toLowerCase();


        /*
         * VERY IMPORTANT:
         *
         * If isUser is true, ALWAYS classify
         * as USER regardless of senderType.
         */

        if (isUser === true) {

            type = 'user';

        }


        /*
         * If no sender was supplied:
         *
         * false = bot
         */

        if (!type) {

            type = 'bot';

        }


        /*
         * Remember scroll state BEFORE
         * adding the message.
         */

        const shouldScroll =
            autoScroll &&
            isNearBottom(wrap);


        const row =
            document.createElement(
                'div'
            );


        /* =====================================================
           EXPLICIT ROW CLASS
           ===================================================== */

        if (type === 'user') {

            /*
             * RESIDENT = RIGHT
             */

            row.className =
                'cw-row cw-row--user';

        }

        else if (type === 'staff') {

            /*
             * STAFF = LEFT
             */

            row.className =
                'cw-row cw-row--bot cw-row--staff';

        }

        else if (type === 'system') {

            /*
             * SYSTEM = LEFT
             */

            row.className =
                'cw-row cw-row--bot cw-row--system';

        }

        else {

            /*
             * AI = LEFT
             */

            row.className =
                'cw-row cw-row--bot';

        }


        /* =====================================================
           USER MESSAGE
           ===================================================== */

        if (type === 'user') {

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


        /* =====================================================
           STAFF MESSAGE
           ===================================================== */

        else if (type === 'staff') {

            row.innerHTML = `

                <div class="cw-avatar">

                    <i class="fas fa-user-tie"></i>

                </div>


                <div class="cw-body">

                    <div
                        class="cw-bubble cw-ai-response"
                        style="
                            background:#e8f7f1;
                            color:#184b3a;
                        ">

                        <div
                            style="
                                font-size:10px;
                                font-weight:700;
                                color:#168b69;
                                margin-bottom:4px;
                            ">

                            Barangay Staff

                        </div>

                        ${formatAIResponse(text)}

                    </div>


                    <span class="cw-ts">

                        ${now()}

                    </span>

                </div>

            `;

        }


        /* =====================================================
           SYSTEM MESSAGE
           ===================================================== */

        else if (type === 'system') {

            row.innerHTML = `

                <div class="cw-avatar">

                    <i class="fas fa-info-circle"></i>

                </div>


                <div class="cw-body">

                    <div
                        class="cw-bubble cw-ai-response"
                        style="
                            background:#fff8e5;
                            color:#685000;
                        ">

                        ${formatAIResponse(text)}

                    </div>


                    <span class="cw-ts">

                        ${now()}

                    </span>

                </div>

            `;

        }


        /* =====================================================
           AI MESSAGE
           ===================================================== */

        else {

            row.innerHTML = `

                <div class="cw-avatar">

                    <i class="fas fa-robot"></i>

                </div>


                <div class="cw-body">

                    <div class="cw-bubble cw-ai-response">

                        ${formatAIResponse(text)}

                    </div>


                    <span class="cw-ts">

                        ${now()}

                    </span>

                </div>

            `;

        }


        /*
         * Add message.
         */

        wrap.appendChild(row);


        /*
         * Only scroll if appropriate.
         */

        if (shouldScroll) {

            wrap.scrollTop =
                wrap.scrollHeight;

        }

    }


    /* ========================================================
       TYPING
       ======================================================== */

    function typing() {

        const wrap =
            document.getElementById(
                'cwMessages'
            );


        if (!wrap) {

            return;

        }


        const old =
            document.getElementById(
                'cwTyping'
            );


        if (old) {

            old.remove();

        }


        const shouldScroll =
            isNearBottom(wrap);


        const t =
            document.createElement(
                'div'
            );


        t.id =
            'cwTyping';


        t.className =
            'cw-row cw-row--bot cw-typing-row';


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


        if (shouldScroll) {

            wrap.scrollTop =
                wrap.scrollHeight;

        }

    }


    /* ========================================================
       REMOVE TYPING
       ======================================================== */

    function removeTyping() {

        const typingElement =
            document.getElementById(
                'cwTyping'
            );


        if (typingElement) {

            typingElement.remove();

        }

    }


    /* ========================================================
       SERVER ERROR
       ======================================================== */

    function showServerError(
        data,
        httpStatus
    ) {

        console.error(
            'BIS Chatbot Error:',
            {
                status:
                    httpStatus,

                response:
                    data
            }
        );


        const message =
            data?.response ||
            data?.message ||
            data?.error ||
            'Sorry, I could not process your request. Please try again.';


        addMsg(
            message,
            false,
            'system'
        );

    }


    /* ========================================================
       FETCH HISTORY
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

                    cache:
                        'no-store'
                }
            );


        if (!response.ok) {

            throw new Error(
                'History HTTP ' +
                response.status
            );

        }


        return await response.json();

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


        historyLoading =
            true;


        try {

            const data =
                await fetchChatHistory();


            if (
                !data ||
                data.success !== true
            ) {

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


            if (
                loadActiveConversation &&
                data.active_conversation_id &&
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


                await refreshSupportStatus();

            }


            return data;

        }

        catch (error) {

            console.error(
                'Chat history error:',
                error
            );

            return null;

        }

        finally {

            historyLoading =
                false;

        }

    }


    /* ========================================================
       REFRESH CONVERSATION LIST
       ======================================================== */

    async function refreshConversationList() {

        try {

            const data =
                await fetchChatHistory();


            if (
                !data ||
                data.success !== true
            ) {

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
                'Conversation list error:',
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

        const list =
            document.getElementById(
                'cwHistoryList'
            );


        if (!list) {

            return;

        }


        list.innerHTML =
            '';


        if (
            !Array.isArray(conversations) ||
            conversations.length === 0
        ) {

            list.innerHTML = `

                <div class="cw-history-empty">

                    No saved conversations yet.

                </div>

            `;

            return;

        }


        conversations.forEach(
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
                    Number(
                        conversation.id
                    ) ===
                    Number(
                        conversationId
                    )
                ) {

                    button.classList.add(
                        'active'
                    );

                }


                button.innerHTML = `

                    <span
                        class="cw-history-item-title">

                        ${escapeHtml(
                            conversation.title ||
                            'Conversation'
                        )}

                    </span>


                    <span
                        class="cw-history-item-date">

                        ${escapeHtml(
                            formatConversationDate(
                                conversation.updated_at ||
                                conversation.created_at ||
                                ''
                            )
                        )}

                    </span>

                `;


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
       DATE FORMAT
       ======================================================== */

    function formatConversationDate(
        value
    ) {

        if (!value) {

            return '';

        }


        const date =
            new Date(
                String(value)
                    .replace(
                        ' ',
                        'T'
                    )
            );


        if (
            isNaN(
                date.getTime()
            )
        ) {

            return String(value);

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
       LOAD CONVERSATION
       ======================================================== */

    async function loadConversation(
        id
    ) {

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

                        cache:
                            'no-store'
                    }
                );


            const data =
                await response.json();


            if (
                !response.ok ||
                data.success !== true
            ) {

                console.error(
                    'Conversation loading failed:',
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


            await refreshSupportStatus();


            const historyPanel =
                document.getElementById(
                    'cwHistoryPanel'
                );


            if (historyPanel) {

                historyPanel.hidden =
                    true;

            }


            renderConversationList(
                document.__cwConversations ||
                []
            );

        }

        catch (error) {

            console.error(
                'Load conversation error:',
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


        /*
         * SAVE CURRENT SCROLL POSITION.
         */

        const previousScrollTop =
            wrap.scrollTop;


        const wasNearBottom =
            isNearBottom(wrap);


        /*
         * CLEAR DISPLAY.
         */

        wrap.innerHTML =
            '';


        /*
         * EMPTY CONVERSATION.
         */

        if (
            !Array.isArray(messages) ||
            messages.length === 0
        ) {

            addMsg(
                "Hello! I'm the BIS Assistant 👋\n\nHow can I help you today?",
                false,
                'bot',
                false
            );


            wrap.scrollTop =
                wrap.scrollHeight;


            return;

        }


        /* =====================================================
           RENDER EACH STORED MESSAGE
           ===================================================== */

        messages.forEach(
            function (item) {

                /*
                 * Support different field names in case
                 * your controller returns sender_type,
                 * sender, role, etc.
                 */

                const sender =
                    String(
                        item.sender ||
                        item.sender_type ||
                        item.role ||
                        ''
                    ).toLowerCase();


                const message =
                    item.message ||
                    item.content ||
                    item.text ||
                    '';


                /* ---------------------------------------------
                   RESIDENT
                   --------------------------------------------- */

                if (
                    sender === 'user' ||
                    sender === 'resident' ||
                    sender === 'client'
                ) {

                    addMsg(
                        message,
                        true,
                        'user',
                        false
                    );

                }


                /* ---------------------------------------------
                   STAFF
                   --------------------------------------------- */

                else if (
                    sender === 'staff' ||
                    sender === 'secretary' ||
                    sender === 'captain'
                ) {

                    addMsg(
                        message,
                        false,
                        'staff',
                        false
                    );

                }


                /* ---------------------------------------------
                   SYSTEM
                   --------------------------------------------- */

                else if (
                    sender === 'system'
                ) {

                    addMsg(
                        message,
                        false,
                        'system',
                        false
                    );

                }


                /* ---------------------------------------------
                   AI
                   --------------------------------------------- */

                else {

                    addMsg(
                        message,
                        false,
                        'bot',
                        false
                    );

                }

            }
        );


        /*
         * IMPORTANT:
         *
         * If resident was reading old messages,
         * restore their position.
         */

        if (wasNearBottom) {

            wrap.scrollTop =
                wrap.scrollHeight;

        }

        else {

            const maxScrollTop =
                Math.max(
                    0,
                    wrap.scrollHeight -
                    wrap.clientHeight
                );


            wrap.scrollTop =
                Math.min(
                    previousScrollTop,
                    maxScrollTop
                );

        }

    }


    /* ========================================================
       SUPPORT STATUS
       ======================================================== */

    async function refreshSupportStatus() {

        if (
            String(USER_ROLE).toLowerCase() !==
            'resident'
        ) {

            return null;

        }


        if (
            !conversationId ||
            Number(conversationId) <= 0
        ) {

            return null;

        }


        try {

            const response =
                await fetch(
                    SUPPORT_STATUS_ENDPOINT +
                    '?conversation_id=' +
                    encodeURIComponent(
                        conversationId
                    ) +
                    '&_=' +
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

                        cache:
                            'no-store'
                    }
                );


            if (!response.ok) {

                return null;

            }


            const data =
                await response.json();


            if (!data) {

                return null;

            }


            if (data.support_mode) {

                supportMode =
                    data.support_mode;

            }


            assignedStaffId =
                data.assigned_staff_id ||
                null;


            /*
             * IMPORTANT:
             *
             * Stored messages are rendered through
             * renderStoredMessages(), which preserves
             * scroll position.
             */

            if (
                Array.isArray(
                    data.messages
                ) &&
                data.messages.length > 0
            ) {

                renderStoredMessages(
                    data.messages
                );

            }


            updateSupportUI();


            return data;

        }

        catch (error) {

            console.error(
                'Support status error:',
                error
            );

            return null;

        }

    }


    /* ========================================================
       SUPPORT UI
       ======================================================== */

    function updateSupportUI() {

        const input =
            document.getElementById(
                'cwInput'
            );


        if (!input) {

            return;

        }


        if (
            supportMode ===
            'waiting_human'
        ) {

            input.placeholder =
                'Message the support queue...';

        }

        else if (
            supportMode ===
            'human'
        ) {

            input.placeholder =
                'Message Barangay Staff...';

        }

        else {

            input.placeholder =
                'Type a message...';

        }

    }


    /* ========================================================
       REQUEST HUMAN SUPPORT
       ======================================================== */

    async function requestHumanSupport() {

        if (
            String(USER_ROLE).toLowerCase() !==
            'resident'
        ) {

            return;

        }


        if (
            !conversationId ||
            Number(conversationId) <= 0
        ) {

            return;

        }


        if (
            supportMode === 'human' ||
            supportMode === 'waiting_human'
        ) {

            return;

        }


        try {

            const response =
                await fetch(
                    SUPPORT_REQUEST_ENDPOINT,
                    {
                        method: 'POST',

                        credentials:
                            'same-origin',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest'

                        },

                        body:
                            JSON.stringify({

                                conversation_id:
                                    conversationId

                            })
                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                showServerError(
                    data,
                    response.status
                );

                return;

            }


            supportMode =
                data.support_mode ||
                'waiting_human';


            updateSupportUI();


            addMsg(
                data.message ||
                'Your request has been sent to the Barangay support staff. Please wait for a staff member to assist you.',
                false,
                'system'
            );


            startSupportPolling();

        }

        catch (error) {

            console.error(
                'Human support error:',
                error
            );


            addMsg(
                'Unable to connect you to Barangay support at this time. Please try again.',
                false,
                'system'
            );

        }

    }


    /* ========================================================
       SUPPORT POLLING
       ======================================================== */

    function startSupportPolling() {

        if (
            String(USER_ROLE).toLowerCase() !==
            'resident'
        ) {

            return;

        }


        if (supportPollingStarted) {

            return;

        }


        supportPollingStarted =
            true;


        setInterval(
            async function () {

                if (
                    conversationId &&
                    Number(conversationId) > 0
                ) {

                    /*
                     * This can continue polling.
                     *
                     * renderStoredMessages() will NOT
                     * force the user to the bottom.
                     */

                    await refreshSupportStatus();

                }

            },
            2500
        );

    }


    /* ========================================================
       HISTORY TOGGLE
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
       NEW CHAT
       ======================================================== */

    window.cwNewChat =
        function () {

            conversationId =
                null;

            supportMode =
                'ai';

            assignedStaffId =
                null;


            const panel =
                document.getElementById(
                    'cwHistoryPanel'
                );


            if (panel) {

                panel.hidden =
                    true;

            }


            const wrap =
                document.getElementById(
                    'cwMessages'
                );


            if (!wrap) {

                return;

            }


            wrap.innerHTML =
                '';


            /*
             * Initial AI message.
             */

            addMsg(
                "Hello! I'm the BIS Assistant 👋\n\nHow can I help you today?",
                false,
                'bot',
                false
            );


            /*
             * Quick chips.
             */

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


            wrap.scrollTop =
                wrap.scrollHeight;


            updateSupportUI();

        };


    /* ========================================================
       SEND
       ======================================================== */

    window.cwSend =
        async function () {

            if (chatSending) {

                return;

            }


            const input =
                document.getElementById(
                    'cwInput'
                );


            if (!input) {

                return;

            }


            const msg =
                input.value.trim();


            if (!msg) {

                return;

            }


            chatSending =
                true;


            /*
             * ==================================================
             * THIS IS THE IMPORTANT PART
             *
             * The resident message is explicitly passed as:
             *
             * true, 'user'
             *
             * so it CANNOT become a bot message.
             * ==================================================
             */

            addMsg(
                msg,
                true,
                'user',
                true
            );


            /*
             * Explicitly put the user's newly sent message
             * at the newest position.
             */

            const messagesWrap =
                document.getElementById(
                    'cwMessages'
                );


            if (messagesWrap) {

                messagesWrap.scrollTop =
                    messagesWrap.scrollHeight;

            }


            input.value =
                '';


            const unread =
                document.getElementById(
                    'cwUnread'
                );


            if (unread) {

                unread.style.display =
                    'none';

            }


            typing();


            try {

                const body =
                    new URLSearchParams();


                body.append(
                    'message',
                    msg
                );


                body.append(
                    'conversation_id',
                    conversationId
                        ? String(conversationId)
                        : ''
                );


                body.append(
                    'support_mode',
                    supportMode
                );


                const response =
                    await fetch(
                        CHAT_ENDPOINT,
                        {
                            method: 'POST',

                            credentials:
                                'same-origin',

                            headers: {

                                'Content-Type':
                                    'application/x-www-form-urlencoded; charset=UTF-8',

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'

                            },

                            body:
                                body.toString()
                        }
                    );


                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                let data;


                if (
                    contentType
                        .toLowerCase()
                        .includes(
                            'application/json'
                        )
                ) {

                    data =
                        await response.json();

                }

                else {

                    const raw =
                        await response.text();


                    data = {

                        success:
                            false,

                        message:
                            raw ||
                            'Invalid server response.'

                    };

                }


                removeTyping();


                console.log(
                    'BIS Chatbot:',
                    data
                );


                if (!response.ok) {

                    showServerError(
                        data,
                        response.status
                    );

                    return;

                }


                if (
                    data &&
                    data.success === true
                ) {


                    /*
                     * Update conversation ID.
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
                     * Update support mode.
                     */

                    if (
                        data.support_mode
                    ) {

                        supportMode =
                            data.support_mode;

                    }


                    updateSupportUI();


                    /* ==========================================
                       HUMAN SUPPORT
                       ========================================== */

                    if (
                        data.source ===
                            'human_support' ||

                        data.source ===
                            'human_support_queue' ||

                        data.source ===
                            'human_support_handoff' ||

                        data.support_mode ===
                            'human' ||

                        data.support_mode ===
                            'waiting_human'
                    ) {

                        if (
                            data.response
                        ) {

                            addMsg(
                                data.response,
                                false,

                                data.support_mode ===
                                    'waiting_human'

                                    ? 'system'

                                    : 'staff'
                            );

                        }


                        startSupportPolling();


                        await refreshConversationList();


                        return;

                    }


                    /* ==========================================
                       AI RESPONSE
                       ========================================== */

                    if (
                        data.response
                    ) {

                        addMsg(
                            data.response,
                            false,
                            'bot',
                            true
                        );

                    }

                    else if (
                        data.message
                    ) {

                        addMsg(
                            data.message,
                            false,
                            'system',
                            true
                        );

                    }


                    await refreshConversationList();


                }

                else {

                    showServerError(
                        data,
                        response.status
                    );

                }

            }

            catch (error) {

                removeTyping();


                console.error(
                    'Chatbot request error:',
                    error
                );


                addMsg(
                    'Sorry, I could not connect to the BIS service. Please try again.',
                    false,
                    'system'
                );

            }

            finally {

                chatSending =
                    false;

            }

        };


    /* ========================================================
       QUICK MESSAGE
       ======================================================== */

    window.cwQuick =
        function (msg) {

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
       OPEN
       ======================================================== */

    window.cwOpen =
        function () {

            const panel =
                document.getElementById(
                    'cwPanel'
                );


            if (!panel) {

                return;

            }


            panel.classList.add(
                'cw-open'
            );


            const unread =
                document.getElementById(
                    'cwUnread'
                );


            if (unread) {

                unread.style.display =
                    'none';

            }


            refreshConversationList();

            refreshSupportStatus();


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

        };


    /* ========================================================
       CLOSE
       ======================================================== */

    window.cwClose =
        function () {

            const panel =
                document.getElementById(
                    'cwPanel'
                );


            if (panel) {

                panel.classList.remove(
                    'cw-open'
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
       HUMAN SUPPORT BUTTON
       ======================================================== */

    window.requestBarangayStaff =
        function () {

            requestHumanSupport();

        };


    /* ========================================================
       INITIALIZE
       ======================================================== */

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            loadChatHistory(true);

            updateSupportUI();


            if (
                String(USER_ROLE)
                    .toLowerCase() ===
                'resident'
            ) {

                startSupportPolling();

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