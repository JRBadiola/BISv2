/**
 * ============================================================
 * BACOLOD BIS
 * CUSTOMER SERVICE / HUMAN SUPPORT
 * ============================================================
 *
 * Used by:
 *   Secretary Customer Service
 *   Captain Customer Service
 *
 * Backend routes:
 *   /secretary/chatbot/api/support-conversations
 *   /secretary/chatbot/api/support-conversation
 *   /secretary/chatbot/api/take-over
 *   /secretary/chatbot/api/staff-message
 *   /secretary/chatbot/api/return-to-ai
 *   /secretary/chatbot/api/close-support
 *
 *   /captain/chatbot/api/support-conversations
 *   /captain/chatbot/api/support-conversation
 *   /captain/chatbot/api/take-over
 *   /captain/chatbot/api/staff-message
 *   /captain/chatbot/api/return-to-ai
 *   /captain/chatbot/api/close-support
 */

(function () {

    'use strict';


    /* =========================================================
       CONFIGURATION
       ========================================================= */

    const serviceRoot =
        window.location.pathname
            .toLowerCase()
            .startsWith('/captain/')
            ? '/captain'
            : '/secretary';


    const ENDPOINTS = {

        conversations:
            serviceRoot +
            '/chatbot/api/support-conversations',

        conversation:
            serviceRoot +
            '/chatbot/api/support-conversation',

        takeOver:
            serviceRoot +
            '/chatbot/api/take-over',

        staffMessage:
            serviceRoot +
            '/chatbot/api/staff-message',

        returnToAI:
            serviceRoot +
            '/chatbot/api/return-to-ai',

        closeSupport:
            serviceRoot +
            '/chatbot/api/close-support'

    };


    /* =========================================================
       STATE
       ========================================================= */

    let selectedConversationId = null;

    let selectedConversation = null;

    let conversations = [];

    let pollingTimer = null;

    let loadingConversations = false;

    let loadingConversation = false;

    let sendingMessage = false;


    /* =========================================================
       DOM HELPERS
       ========================================================= */

    function el(id) {

        return document.getElementById(id);

    }


    function qs(selector) {

        return document.querySelector(selector);

    }


    /* =========================================================
       HTML ESCAPE
       ========================================================= */

    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /* =========================================================
       NORMALIZE MESSAGE
       =========================================================
       Prevents [object Object] from appearing if the backend
       returns latest_message as an object.
       ========================================================= */

    function getMessageText(message) {

        if (
            message === null ||
            message === undefined
        ) {

            return '';

        }


        if (typeof message === 'string') {

            return message;

        }


        if (typeof message === 'number') {

            return String(message);

        }


        if (typeof message === 'object') {

            return (
                message.message ??
                message.content ??
                message.text ??
                message.body ??
                message.latest_message ??
                message.last_message ??
                ''
            );

        }


        return String(message);

    }


    /* =========================================================
       FETCH JSON
       ========================================================= */

    async function fetchJson(
        url,
        options = {}
    ) {

        const response =
            await fetch(
                url,
                {
                    credentials:
                        'same-origin',

                    cache:
                        'no-store',

                    headers: {

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest',

                        ...(options.headers || {})

                    },

                    ...options

                }
            );


        const contentType =
            response.headers.get(
                'content-type'
            ) || '';


        let data = null;


        if (
            contentType
                .toLowerCase()
                .includes('application/json')
        ) {

            try {

                data =
                    await response.json();

            }

            catch (error) {

                console.error(
                    'Customer Service JSON parse error:',
                    error
                );

            }

        }

        else {

            const text =
                await response.text();

            data = {

                success: false,

                message:
                    text ||
                    'The server returned an invalid response.'

            };

        }


        if (!response.ok) {

            const message =
                data?.response ||
                data?.message ||
                data?.error ||
                'The request failed.';


            throw new Error(
                message +
                ' (HTTP ' +
                response.status +
                ')'
            );

        }


        return data;

    }


    /* =========================================================
       SHOW NOTIFICATION
       ========================================================= */

    function showNotice(
        message,
        type = 'info'
    ) {

        let container =
            el('customerServiceNotice');


        if (!container) {

            container =
                document.createElement(
                    'div'
                );

            container.id =
                'customerServiceNotice';

            container.style.position =
                'fixed';

            container.style.top =
                '20px';

            container.style.right =
                '20px';

            container.style.zIndex =
                '99999';

            container.style.maxWidth =
                '360px';

            document.body.appendChild(
                container
            );

        }


        const notice =
            document.createElement(
                'div'
            );


        notice.style.padding =
            '12px 16px';

        notice.style.marginBottom =
            '8px';

        notice.style.borderRadius =
            '10px';

        notice.style.background =
            type === 'error'
                ? '#fff0f1'
                : type === 'success'
                    ? '#eafaf5'
                    : '#eef2ff';

        notice.style.color =
            type === 'error'
                ? '#b42318'
                : type === 'success'
                    ? '#176b4d'
                    : '#33408f';

        notice.style.boxShadow =
            '0 4px 16px rgba(0,0,0,.12)';

        notice.style.fontSize =
            '13px';

        notice.textContent =
            message;


        container.appendChild(
            notice
        );


        setTimeout(
            function () {

                notice.remove();

            },
            4000
        );

    }


    /* =========================================================
       FORMAT DATE
       ========================================================= */

    function formatDate(value) {

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
            Number.isNaN(
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

            return date.toLocaleTimeString(
                [],
                {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );

        }


        return date.toLocaleDateString(
            [],
            {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }
        );

    }


    /* =========================================================
       GET CONVERSATION ID
       ========================================================= */

    function getConversationId(
        conversation
    ) {

        if (!conversation) {

            return null;

        }


        return Number(
            conversation.id ??
            conversation.conversation_id ??
            0
        ) || null;

    }


    /* =========================================================
       GET CONVERSATION NAME
       ========================================================= */

    function getResidentName(
        conversation
    ) {

        if (!conversation) {

            return 'Resident';

        }


        return (
            conversation.resident_name ||
            conversation.user_name ||
            conversation.name ||
            conversation.username ||
            (
                conversation.first_name &&
                conversation.last_name
                    ? conversation.first_name +
                      ' ' +
                      conversation.last_name
                    : ''
            ) ||
            'Resident'
        );

    }


    /* =========================================================
       GET SUPPORT MODE
       ========================================================= */

    function getSupportMode(
        conversation
    ) {

        const mode =
            String(
                conversation?.support_mode ||
                conversation?.supportMode ||
                'waiting_human'
            )
            .toLowerCase()
            .trim();


        if (
            [
                'ai',
                'waiting_human',
                'human',
                'closed'
            ].includes(mode)
        ) {

            return mode;

        }


        return 'waiting_human';

    }


    /* =========================================================
       RENDER QUEUE
       ========================================================= */

    function renderQueue(
        items
    ) {

        const list =
            el('supportConversationList');


        if (!list) {

            console.warn(
                'supportConversationList was not found.'
            );

            return;

        }


        list.innerHTML =
            '';


        const countElement =
            el('supportRequestCount');


        const waitingCount =
            (items || []).filter(
                function (item) {

                    return (
                        getSupportMode(item) ===
                        'waiting_human'
                    );

                }
            ).length;


        if (countElement) {

            countElement.textContent =
                waitingCount;

            countElement.style.display =
                waitingCount > 0
                    ? ''
                    : 'none';

        }


        if (
            !items ||
            items.length === 0
        ) {

            list.innerHTML = `

                <div
                    style="
                        padding:30px 18px;
                        text-align:center;
                        color:#9aa0b4;
                    ">

                    <i
                        class="fas fa-comments"
                        style="
                            display:block;
                            font-size:28px;
                            margin-bottom:10px;
                            opacity:.5;
                        ">
                    </i>

                    <div
                        style="
                            font-size:13px;
                            font-weight:600;
                        ">

                        No support requests

                    </div>

                    <div
                        style="
                            font-size:11px;
                            margin-top:4px;
                        ">

                        New resident requests will appear here.

                    </div>

                </div>

            `;

            return;

        }


        items.forEach(
            function (conversation) {

                const id =
                    getConversationId(
                        conversation
                    );


                if (!id) {

                    return;

                }


                const button =
                    document.createElement(
                        'button'
                    );


                button.type =
                    'button';


                button.className =
                    'support-conversation-item';


                if (
                    Number(
                        selectedConversationId
                    ) === Number(id)
                ) {

                    button.classList.add(
                        'active'
                    );

                }


                const name =
                    escapeHtml(
                        getResidentName(
                            conversation
                        )
                    );


                const mode =
                    getSupportMode(
                        conversation
                    );


                let modeText =
                    'Waiting for staff';


                if (mode === 'human') {

                    modeText =
                        'Staff support active';

                }

                else if (
                    mode === 'closed'
                ) {

                    modeText =
                        'Closed';

                }

                else if (
                    mode === 'ai'
                ) {

                    modeText =
                        'AI support';

                }


                /*
                 * Safely obtain latest message.
                 * This prevents [object Object].
                 */

                const latestMessage =
                    getMessageText(
                        conversation.latest_message ||
                        conversation.last_message ||
                        conversation.message ||
                        ''
                    );


                button.innerHTML = `

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                            width:100%;
                        ">

                        <div
                            style="
                                width:38px;
                                height:38px;
                                border-radius:50%;
                                background:#eef1ff;
                                color:#5b6fd6;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                flex-shrink:0;
                            ">

                            <i class="fas fa-user"></i>

                        </div>

                        <div
                            style="
                                min-width:0;
                                flex:1;
                                text-align:left;
                            ">

                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:space-between;
                                    gap:8px;
                                ">

                                <span
                                    style="
                                        font-size:13px;
                                        font-weight:700;
                                        color:#1a1d2e;
                                        white-space:nowrap;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                    ">

                                    ${name}

                                </span>

                                <span
                                    style="
                                        font-size:9px;
                                        color:#9aa0b4;
                                        white-space:nowrap;
                                    ">

                                    ${
                                        formatDate(
                                            conversation.updated_at ||
                                            conversation.last_activity_at ||
                                            conversation.created_at
                                        )
                                    }

                                </span>

                            </div>

                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:5px;
                                    margin-top:3px;
                                ">

                                <span
                                    style="
                                        width:7px;
                                        height:7px;
                                        border-radius:50%;
                                        background:${
                                            mode === 'human'
                                                ? '#16a085'
                                                : mode === 'closed'
                                                    ? '#aaa'
                                                    : mode === 'ai'
                                                        ? '#5b6fd6'
                                                        : '#f0a500'
                                        };
                                    ">
                                </span>

                                <span
                                    style="
                                        font-size:10px;
                                        color:#6d748a;
                                        white-space:nowrap;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                    ">

                                    ${escapeHtml(modeText)}

                                </span>

                            </div>

                            ${
                                latestMessage
                                    ? `
                                        <div
                                            style="
                                                font-size:10px;
                                                color:#9aa0b4;
                                                margin-top:4px;
                                                white-space:nowrap;
                                                overflow:hidden;
                                                text-overflow:ellipsis;
                                            ">

                                            ${escapeHtml(
                                                latestMessage
                                            )}

                                        </div>
                                      `
                                    : ''
                            }

                        </div>

                    </div>

                `;


                button.addEventListener(
                    'click',
                    function () {

                        selectConversation(
                            id
                        );

                    }
                );


                list.appendChild(
                    button
                );

            }
        );

    }


    /* =========================================================
       LOAD CONVERSATIONS
       ========================================================= */

    async function loadSupportConversations() {

        if (loadingConversations) {

            return;

        }


        loadingConversations =
            true;


        try {

            const data =
                await fetchJson(
                    ENDPOINTS.conversations,
                    {
                        method:
                            'GET'
                    }
                );


            if (
                !data ||
                data.success !== true
            ) {

                throw new Error(
                    data?.message ||
                    data?.response ||
                    'Could not load support conversations.'
                );

            }


            conversations =
                Array.isArray(
                    data.conversations
                )
                    ? data.conversations
                    : (
                        Array.isArray(
                            data.data
                        )
                            ? data.data
                            : []
                    );


            renderQueue(
                conversations
            );


            /*
             * Keep selected conversation updated.
             */

            if (
                selectedConversationId
            ) {

                const stillExists =
                    conversations.some(
                        function (conversation) {

                            return (
                                Number(
                                    getConversationId(
                                        conversation
                                    )
                                ) ===
                                Number(
                                    selectedConversationId
                                )
                            );

                        }
                    );


                if (stillExists) {

                    await loadConversation(
                        selectedConversationId,
                        false
                    );

                }

            }

        }

        catch (error) {

            console.error(
                'Failed to load Customer Service conversations:',
                error
            );


            showNotice(
                error.message ||
                'Unable to load support requests.',
                'error'
            );

        }

        finally {

            loadingConversations =
                false;

        }

    }


    /* =========================================================
       SELECT CONVERSATION
       ========================================================= */

    async function selectConversation(
        id
    ) {

        if (!id) {

            return;

        }


        selectedConversationId =
            Number(id);


        renderQueue(
            conversations
        );


        await loadConversation(
            selectedConversationId
        );

    }


    /* =========================================================
       LOAD SINGLE CONVERSATION
       ========================================================= */

    async function loadConversation(
        id,
        showErrors = true
    ) {

        if (!id) {

            return;

        }


        if (loadingConversation) {

            return;

        }


        loadingConversation =
            true;


        try {

            const data =
                await fetchJson(
                    ENDPOINTS.conversation +
                    '?conversation_id=' +
                    encodeURIComponent(id) +
                    '&_=' +
                    Date.now(),
                    {
                        method:
                            'GET'
                    }
                );


            if (
                !data ||
                data.success !== true
            ) {

                throw new Error(
                    data?.message ||
                    data?.response ||
                    'Could not load the conversation.'
                );

            }


            selectedConversation =
                data.conversation ||
                data;


            const messages =
                Array.isArray(
                    data.messages
                )
                    ? data.messages
                    : [];


            renderConversationHeader(
                selectedConversation
            );


            renderMessages(
                messages
            );


            updateActionButtons(
                selectedConversation
            );

        }

        catch (error) {

            console.error(
                'Failed to load support conversation:',
                error
            );


            if (showErrors) {

                showNotice(
                    error.message ||
                    'Unable to open the conversation.',
                    'error'
                );

            }

        }

        finally {

            loadingConversation =
                false;

        }

    }


    /* =========================================================
       RENDER HEADER
       ========================================================= */

    function renderConversationHeader(
        conversation
    ) {

        const name =
            getResidentName(
                conversation
            );


        const nameElement =
            el('supportResidentName');


        if (nameElement) {

            nameElement.textContent =
                name;

        }


        const avatar =
            el('supportResidentAvatar');


        if (avatar) {

            avatar.textContent =
                name
                    .trim()
                    .charAt(0)
                    .toUpperCase() ||
                'R';

        }


        const status =
            getSupportMode(
                conversation
            );


        const statusText =
            el('supportResidentStatus');


        const statusDot =
            el('supportStatusDot');


        const statusLabel =
            el('supportStatusText');


        if (statusText) {

            statusText.textContent =
                status === 'human'
                    ? 'Connected to staff'
                    : status === 'waiting_human'
                        ? 'Waiting for staff'
                        : status === 'closed'
                            ? 'Conversation closed'
                            : 'AI support';

        }


        if (statusDot) {

            statusDot.style.background =
                status === 'human'
                    ? '#16a085'
                    : status === 'waiting_human'
                        ? '#f0a500'
                        : status === 'closed'
                            ? '#aaa'
                            : '#5b6fd6';

        }


        if (statusLabel) {

            statusLabel.textContent =
                status === 'human'
                    ? 'Live'
                    : status === 'waiting_human'
                        ? 'Waiting'
                        : status === 'closed'
                            ? 'Closed'
                            : 'AI';

        }

    }


    /* =========================================================
       RENDER MESSAGES
       ========================================================= */

    function renderMessages(
        messages
    ) {

        const container =
            el('supportChatMessages');


        if (!container) {

            console.warn(
                'supportChatMessages was not found.'
            );

            return;

        }


        container.innerHTML =
            '';


        if (
            !messages ||
            messages.length === 0
        ) {

            container.innerHTML = `

                <div
                    style="
                        text-align:center;
                        padding:40px 15px;
                        color:#9aa0b4;
                        font-size:12px;
                    ">

                    No messages yet.

                </div>

            `;

            return;

        }


        messages.forEach(
            function (message) {

                const sender =
                    String(
                        message.sender ||
                        ''
                    ).toLowerCase();


                const isUser =
                    sender === 'user';


                const isStaff =
                    sender === 'staff';


                const isSystem =
                    sender === 'system';


                const row =
                    document.createElement(
                        'div'
                    );


                row.style.display =
                    'flex';

                row.style.marginBottom =
                    '12px';

                row.style.alignItems =
                    'flex-start';


                if (isUser) {

                    row.style.justifyContent =
                        'flex-start';

                }

                else {

                    row.style.justifyContent =
                        'flex-end';

                }


                const bubble =
                    document.createElement(
                        'div'
                    );


                bubble.style.maxWidth =
                    '72%';

                bubble.style.padding =
                    '10px 12px';

                bubble.style.borderRadius =
                    '13px';


                if (isUser) {

                    bubble.style.background =
                        '#fff';

                    bubble.style.color =
                        '#333';

                    bubble.style.borderBottomLeftRadius =
                        '4px';

                }

                else if (isStaff) {

                    bubble.style.background =
                        '#e8f7f1';

                    bubble.style.color =
                        '#184b3a';

                    bubble.style.borderBottomRightRadius =
                        '4px';

                }

                else if (isSystem) {

                    bubble.style.background =
                        '#fff8e5';

                    bubble.style.color =
                        '#685000';

                }

                else {

                    bubble.style.background =
                        '#eef1ff';

                    bubble.style.color =
                        '#34408f';

                    bubble.style.borderBottomRightRadius =
                        '4px';

                }


                const senderLabel =
                    document.createElement(
                        'div'
                    );


                senderLabel.style.fontSize =
                    '9px';

                senderLabel.style.fontWeight =
                    '700';

                senderLabel.style.marginBottom =
                    '3px';


                if (isUser) {

                    senderLabel.textContent =
                        'Resident';

                    senderLabel.style.color =
                        '#6d748a';

                }

                else if (isStaff) {

                    senderLabel.textContent =
                        'You';

                    senderLabel.style.color =
                        '#168b69';

                }

                else if (isSystem) {

                    senderLabel.textContent =
                        'System';

                    senderLabel.style.color =
                        '#9a7400';

                }

                else {

                    senderLabel.textContent =
                        'BIS Assistant';

                    senderLabel.style.color =
                        '#5968c5';

                }


                const messageText =
                    document.createElement(
                        'div'
                    );


                messageText.style.fontSize =
                    '13px';

                messageText.style.lineHeight =
                    '1.55';

                messageText.style.whiteSpace =
                    'pre-wrap';

                messageText.style.wordBreak =
                    'break-word';


                /*
                 * Safely handle message content.
                 */

                messageText.textContent =
                    getMessageText(
                        message.message ||
                        message.content ||
                        message.text ||
                        ''
                    );


                const timestamp =
                    document.createElement(
                        'div'
                    );


                timestamp.style.marginTop =
                    '4px';

                timestamp.style.fontSize =
                    '9px';

                timestamp.style.color =
                    '#9aa0b4';

                timestamp.textContent =
                    formatDate(
                        message.created_at ||
                        message.updated_at ||
                        ''
                    );


                bubble.appendChild(
                    senderLabel
                );


                bubble.appendChild(
                    messageText
                );


                bubble.appendChild(
                    timestamp
                );


                row.appendChild(
                    bubble
                );


                container.appendChild(
                    row
                );

            }
        );


        container.scrollTop =
            container.scrollHeight;

    }


    /* =========================================================
       UPDATE ACTION BUTTONS
       ========================================================= */

    function updateActionButtons(
        conversation
    ) {

        const mode =
            getSupportMode(
                conversation
            );


        const takeOverButton =
            el('takeOverConversation');


        const returnAIButton =
            el('returnToAI');


        const closeButton =
            el('closeSupportConversation');


        const input =
            el('staffMessageInput');


        const sendButton =
            el('staffSendButton');


        /*
         * -----------------------------------------------------
         * TAKE OVER
         * -----------------------------------------------------
         *
         * Keep the original UI.
         * Only change whether the button is enabled.
         */

        if (takeOverButton) {

            takeOverButton.disabled =
                mode !== 'waiting_human';

        }


        /*
         * -----------------------------------------------------
         * RETURN TO AI
         * -----------------------------------------------------
         */

        if (returnAIButton) {

            returnAIButton.disabled =
                mode !== 'human';

        }


        /*
         * -----------------------------------------------------
         * CLOSE
         * -----------------------------------------------------
         *
         * Can close:
         *
         * waiting_human
         * human
         */

        if (closeButton) {

            closeButton.disabled =
                !(
                    mode === 'waiting_human' ||
                    mode === 'human'
                );

        }


        /*
         * -----------------------------------------------------
         * STAFF MESSAGE COMPOSER
         * -----------------------------------------------------
         *
         * Staff can type only after takeover.
         */

        const canReply =
            mode === 'human';


        const composer =
            el('supportComposer');


        if (composer) {

            composer.style.display =
                canReply
                    ? ''
                    : 'none';

        }


        if (input) {

            input.disabled =
                !canReply;


            if (canReply) {

                input.placeholder =
                    'Type your message...';

            }

            else if (
                mode === 'waiting_human'
            ) {

                input.placeholder =
                    'Take over the conversation first...';

            }

            else if (
                mode === 'closed'
            ) {

                input.placeholder =
                    'Conversation is closed.';

            }

            else {

                input.placeholder =
                    'No active staff conversation.';

            }

        }


        if (sendButton) {

            sendButton.disabled =
                !canReply ||
                sendingMessage;

        }

    }


    /* =========================================================
       TAKE OVER
       ========================================================= */

    async function takeOver() {

        if (
            !selectedConversationId
        ) {

            showNotice(
                'Please select a resident conversation first.',
                'error'
            );

            return;

        }


        const takeOverButton =
            el('takeOverConversation');


        /*
         * Prevent double clicking.
         */

        if (takeOverButton) {

            takeOverButton.disabled =
                true;

        }


        try {

            const data =
                await fetchJson(
                    ENDPOINTS.takeOver,
                    {
                        method:
                            'POST',

                        headers: {

                            'Content-Type':
                                'application/json'

                        },

                        body:
                            JSON.stringify({

                                conversation_id:
                                    selectedConversationId

                            })

                    }
                );


            if (
                !data ||
                data.success !== true
            ) {

                throw new Error(
                    data?.message ||
                    data?.response ||
                    'Could not take over the conversation.'
                );

            }


            showNotice(
                'You have taken over the resident conversation.',
                'success'
            );


            /*
             * Immediately update local state.
             */

            if (selectedConversation) {

                selectedConversation.support_mode =
                    'human';

                selectedConversation.assigned_staff_id =
                    data.assigned_staff_id ??
                    selectedConversation.assigned_staff_id;

            }


            /*
             * Immediately enable staff reply.
             */

            updateActionButtons(
                selectedConversation || {
                    support_mode:
                        'human'
                }
            );


            /*
             * Reload conversation so the takeover
             * system message and latest state appear.
             */

            await loadConversation(
                selectedConversationId,
                false
            );


            /*
             * Refresh support queue.
             */

            await loadSupportConversations();

        }

        catch (error) {

            console.error(
                'Take over failed:',
                error
            );


            showNotice(
                error.message ||
                'Unable to take over the conversation.',
                'error'
            );


            /*
             * Reload current state after failure.
             */

            await loadConversation(
                selectedConversationId,
                false
            );

        }

    }


    /* =========================================================
       SEND STAFF MESSAGE
       ========================================================= */

    async function sendStaffMessage() {

        if (sendingMessage) {

            return;

        }


        if (
            !selectedConversationId
        ) {

            showNotice(
                'Please select a resident conversation first.',
                'error'
            );

            return;

        }


        /*
         * Staff should only send messages while
         * the conversation is in human-support mode.
         */

        if (
            getSupportMode(
                selectedConversation
            ) !== 'human'
        ) {

            showNotice(
                'Please take over the conversation first.',
                'error'
            );

            return;

        }


        const input =
            el('staffMessageInput');


        if (!input) {

            return;

        }


        const message =
            input.value.trim();


        if (!message) {

            return;

        }


        sendingMessage =
            true;


        const sendButton =
            el('staffSendButton');


        if (sendButton) {

            sendButton.disabled =
                true;

        }


        try {

            const data =
                await fetchJson(
                    ENDPOINTS.staffMessage,
                    {
                        method:
                            'POST',

                        headers: {

                            'Content-Type':
                                'application/json'

                        },

                        body:
                            JSON.stringify({

                                conversation_id:
                                    selectedConversationId,

                                message:
                                    message

                            })

                    }
                );


            if (
                !data ||
                data.success !== true
            ) {

                throw new Error(
                    data?.message ||
                    data?.response ||
                    'Could not send the message.'
                );

            }


            input.value =
                '';


            await loadConversation(
                selectedConversationId,
                false
            );


            await loadSupportConversations();

        }

        catch (error) {

            console.error(
                'Staff message failed:',
                error
            );


            showNotice(
                error.message ||
                'Unable to send message.',
                'error'
            );

        }

        finally {

            sendingMessage =
                false;


            /*
             * Do not blindly enable the send button.
             *
             * The conversation may have been closed or
             * returned to AI.
             */

            if (
                selectedConversation
            ) {

                updateActionButtons(
                    selectedConversation
                );

            }

            else if (sendButton) {

                sendButton.disabled =
                    true;

            }


            if (input) {

                input.focus();

            }

        }

    }


    /* =========================================================
       RETURN TO AI
       ========================================================= */

    async function returnConversationToAI() {

        if (
            !selectedConversationId
        ) {

            return;

        }


        const confirmed =
            window.confirm(
                'Return this conversation to the BIS Assistant?'
            );


        if (!confirmed) {

            return;

        }


        const returnAIButton =
            el('returnToAI');


        if (returnAIButton) {

            returnAIButton.disabled =
                true;

        }


        try {

            const data =
                await fetchJson(
                    ENDPOINTS.returnToAI,
                    {
                        method:
                            'POST',

                        headers: {

                            'Content-Type':
                                'application/json'

                        },

                        body:
                            JSON.stringify({

                                conversation_id:
                                    selectedConversationId

                            })

                    }
                );


            if (
                !data ||
                data.success !== true
            ) {

                throw new Error(
                    data?.message ||
                    data?.response ||
                    'Could not return the conversation to AI.'
                );

            }


            showNotice(
                'The conversation has been returned to the BIS Assistant.',
                'success'
            );


            await loadConversation(
                selectedConversationId,
                false
            );


            await loadSupportConversations();

        }

        catch (error) {

            console.error(
                'Return to AI failed:',
                error
            );


            showNotice(
                error.message ||
                'Unable to return the conversation to AI.',
                'error'
            );


            await loadConversation(
                selectedConversationId,
                false
            );

        }

    }


    /* =========================================================
       CLOSE SUPPORT
       ========================================================= */

    async function closeSupport() {

        if (
            !selectedConversationId
        ) {

            return;

        }


        const confirmed =
            window.confirm(
                'Close this support conversation?'
            );


        if (!confirmed) {

            return;

        }


        const closeButton =
            el('closeSupportConversation');


        if (closeButton) {

            closeButton.disabled =
                true;

        }


        try {

            const data =
                await fetchJson(
                    ENDPOINTS.closeSupport,
                    {
                        method:
                            'POST',

                        headers: {

                            'Content-Type':
                                'application/json'

                        },

                        body:
                            JSON.stringify({

                                conversation_id:
                                    selectedConversationId

                            })

                    }
                );


            if (
                !data ||
                data.success !== true
            ) {

                throw new Error(
                    data?.message ||
                    data?.response ||
                    'Could not close the conversation.'
                );

            }


            showNotice(
                'The support conversation has been closed.',
                'success'
            );


            /*
             * Update local state immediately.
             */

            if (selectedConversation) {

                selectedConversation.support_mode =
                    'closed';

            }


            updateActionButtons(
                selectedConversation || {
                    support_mode:
                        'closed'
                }
            );


            /*
             * Keep the conversation selected so that
             * its history remains visible.
             */

            await loadConversation(
                selectedConversationId,
                false
            );


            await loadSupportConversations();

        }

        catch (error) {

            console.error(
                'Close support failed:',
                error
            );


            showNotice(
                error.message ||
                'Unable to close the support conversation.',
                'error'
            );


            await loadConversation(
                selectedConversationId,
                false
            );

        }

    }


    /* =========================================================
       POLLING
       ========================================================= */

    function startPolling() {

        if (pollingTimer) {

            clearInterval(
                pollingTimer
            );

        }


        pollingTimer =
            setInterval(
                async function () {

                    /*
                     * Do not interrupt a manual operation.
                     */

                    if (
                        loadingConversations ||
                        loadingConversation ||
                        sendingMessage
                    ) {

                        return;

                    }


                    await loadSupportConversations();

                },
                2500
            );

    }


    /* =========================================================
       INPUT ENTER KEY
       ========================================================= */

    function bindInput() {

        const input =
            el('staffMessageInput');


        if (!input) {

            return;

        }


        input.addEventListener(
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

    }


    /* =========================================================
       BUTTON EVENTS
       ========================================================= */

    function bindButtons() {

        /*
         * IMPORTANT:
         *
         * Do NOT name this variable "takeOver".
         * There is already a function named takeOver().
         */

        const takeOverButton =
            el('takeOverConversation');


        if (takeOverButton) {

            takeOverButton.addEventListener(
                'click',
                function () {

                    takeOver();

                }
            );

        }


        const returnAIButton =
            el('returnToAI');


        if (returnAIButton) {

            returnAIButton.addEventListener(
                'click',
                function () {

                    returnConversationToAI();

                }
            );

        }


        const closeButton =
            el('closeSupportConversation');


        if (closeButton) {

            closeButton.addEventListener(
                'click',
                function () {

                    closeSupport();

                }
            );

        }


        const sendButton =
            el('staffSendButton');


        if (sendButton) {

            sendButton.addEventListener(
                'click',
                function () {

                    sendStaffMessage();

                }
            );

        }

    }


    /* =========================================================
       CLEAR SELECTED CONVERSATION
       ========================================================= */

    function clearConversationPanel() {

        selectedConversationId =
            null;

        selectedConversation =
            null;


        const name =
            el('supportResidentName');


        if (name) {

            name.textContent =
                'Select a conversation';

        }


        const status =
            el('supportResidentStatus');


        if (status) {

            status.textContent =
                'No conversation selected';

        }


        const statusText =
            el('supportStatusText');


        if (statusText) {

            statusText.textContent =
                '';

        }


        const statusDot =
            el('supportStatusDot');


        if (statusDot) {

            statusDot.style.background =
                '#bbb';

        }


        const avatar =
            el('supportResidentAvatar');


        if (avatar) {

            avatar.textContent =
                '?';

        }


        const messages =
            el('supportChatMessages');


        if (messages) {

            messages.style.display =
                '';


            messages.innerHTML = `

                <div
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        min-height:240px;
                        color:#9aa0b4;
                        text-align:center;
                        font-size:13px;
                    ">

                    <div>

                        <i
                            class="fas fa-comments"
                            style="
                                display:block;
                                font-size:35px;
                                margin-bottom:12px;
                                opacity:.45;
                            ">
                        </i>

                        Select a resident conversation
                        from the support queue.

                    </div>

                </div>

            `;

        }


        /*
         * No conversation selected.
         *
         * Everything must be disabled.
         */

        updateActionButtons({

            support_mode:
                'closed'

        });

    }


    /* =========================================================
       GLOBAL FUNCTIONS
       ========================================================= */

    window.customerServiceRefresh =
        function () {

            loadSupportConversations();

        };


    window.customerServiceSelect =
        function (id) {

            selectConversation(
                id
            );

        };


    window.customerServiceTakeOver =
        function () {

            takeOver();

        };


    window.customerServiceSend =
        function () {

            sendStaffMessage();

        };


    window.customerServiceReturnToAI =
        function () {

            returnConversationToAI();

        };


    window.customerServiceClose =
        function () {

            closeSupport();

        };


    /* =========================================================
       INITIALIZE
       ========================================================= */

    document.addEventListener(
        'DOMContentLoaded',
        async function () {

            console.log(
                'Bacolod BIS Customer Service initialized.',
                {
                    serviceRoot:
                        serviceRoot,

                    endpoints:
                        ENDPOINTS
                }
            );


            bindButtons();

            bindInput();


            clearConversationPanel();


            await loadSupportConversations();


            startPolling();

        }
    );


    /* =========================================================
       CLEANUP
       ========================================================= */

    window.addEventListener(
        'beforeunload',
        function () {

            if (pollingTimer) {

                clearInterval(
                    pollingTimer
                );

            }

        }
    );


})();