<script lang="ts">
    import { onMount } from "svelte";

    // Dummy data – byt senare mot riktig fetch från API
    let conversations = [
        {
            id: 1,
            name: "Emma Lind",
            username: "emmalind",
            lastMessage: "Haha ja verkligen! 😂",
            time: "14:22",
            unread: 2,
            avatar: "/assets/pfp.png",
        },
        {
            id: 2,
            name: "Oskar Berg",
            username: "oskberg",
            lastMessage: "Tack för tipset om gymmet!",
            time: "Igår",
            unread: 0,
            avatar: "/assets/pfp.png",
        },
        {
            id: 3,
            name: "Sara Karlsson",
            username: "sarak",
            lastMessage: "Ses imorgon 18:00?",
            time: "09:15",
            unread: 1,
            avatar: "/assets/pfp.png",
        },
    ];

    let selectedConv = conversations[0]; // Förvald konversation
    let messageText = "";

    let messages = [
        {
            fromMe: false,
            text: "Hej! Hur går det med projektet?",
            time: "14:05",
        },
        {
            fromMe: true,
            text: "Bra! Just nu fastnat lite på designen 😅",
            time: "14:07",
        },
        {
            fromMe: false,
            text: "Haha samma här. Har du testat den nya Figma-pluginen?",
            time: "14:10",
        },
        { fromMe: true, text: "Nej! Skicka länk pls 🙏", time: "14:12" },
        {
            fromMe: false,
            text: "Här kommer den: https://www.figma.com/community/plugin/12345",
            time: "14:15",
        },
    ];

    function sendMessage() {
        if (!messageText.trim()) return;
        messages = [
            ...messages,
            { fromMe: true, text: messageText, time: "nu" },
        ];
        messageText = "";
        // TODO: skicka till backend
    }

    function selectConversation(conv: {
        id: number;
        name: string;
        username: string;
        lastMessage: string;
        time: string;
        unread: number;
        avatar: string;
    }) {
        selectedConv = conv;
        // Här skulle du normalt ladda messages för vald konversation
    }
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Meddelanden</h1>
        <p class="lead">Prata med dina vänner</p>
    </header>

    <div class="messages-layout">
        <!-- Vänster: konversationslista -->
        <div class="conv-list">
            <div class="search-box">
                <input
                    type="text"
                    placeholder="Sök personer eller meddelanden..."
                />
            </div>

            {#each conversations as conv}
                <!-- svelte-ignore a11y_click_events_have_key_events -->
                <!-- svelte-ignore a11y_no_static_element_interactions -->
                <div
                    class="conv-item"
                    class:active={selectedConv?.id === conv.id}
                    on:click={() => selectConversation(conv)}
                >
                    <img
                        src={conv.avatar}
                        alt={conv.name}
                        class="conv-avatar"
                    />
                    <div class="conv-info">
                        <div class="conv-name">{conv.name}</div>
                        <div class="conv-last">{conv.lastMessage}</div>
                    </div>
                    <div class="conv-meta">
                        <div class="conv-time">{conv.time}</div>
                        {#if conv.unread > 0}
                            <span class="unread-badge">{conv.unread}</span>
                        {/if}
                    </div>
                </div>
            {/each}
        </div>

        <!-- Höger: öppen konversation -->
        <div class="chat-area">
            {#if selectedConv}
                <div class="chat-header">
                    <img
                        src={selectedConv.avatar}
                        alt={selectedConv.name}
                        class="chat-avatar"
                    />
                    <div>
                        <h3>{selectedConv.name}</h3>
                        <span>@{selectedConv.username}</span>
                    </div>
                </div>

                <div class="messages-container">
                    {#each messages as msg}
                        <div class="message" class:from-me={msg.fromMe}>
                            <div class="msg-bubble">{msg.text}</div>
                            <div class="msg-time">{msg.time}</div>
                        </div>
                    {/each}
                </div>

                <div class="chat-input">
                    <!-- svelte-ignore element_invalid_self_closing_tag -->
                    <textarea
                        bind:value={messageText}
                        placeholder="Skriv ett meddelande..."
                        rows="1"
                        on:keydown={(e) =>
                            e.key === "Enter" &&
                            !e.shiftKey &&
                            (e.preventDefault(), sendMessage())}
                    />
                    <button
                        on:click={sendMessage}
                        disabled={!messageText.trim()}
                    >
                        Skicka
                    </button>
                </div>
            {:else}
                <div class="no-chat-selected">
                    <p>Välj en konversation för att börja prata</p>
                </div>
            {/if}
        </div>
    </div>
</main>

<style>
    .main-content {
        max-width: 1200px;
        margin: 28px auto;
        padding: 24px;
        background: #ffffffcc;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        height: calc(100% - 56px);
        display: flex;
        flex-direction: column;
    }

    .page-header h1 {
        font-size: 1.8rem;
        margin: 0 0 8px;
    }
    .lead {
        color: #666;
        margin: 0;
    }

    .messages-layout {
        display: flex;
        flex: 1;
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .conv-list {
        width: 340px;
        border-right: 1px solid #eee;
        overflow-y: auto;
        background: #fafafa;
    }

    .search-box {
        padding: 16px;
        border-bottom: 1px solid #eee;
    }

    .search-box input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-size: 0.95rem;
    }

    .conv-item {
        display: flex;
        padding: 14px 16px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background 0.15s;
    }

    .conv-item:hover,
    .conv-item.active {
        background: #f0f4ff;
    }

    .conv-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px;
    }

    .conv-info {
        flex: 1;
        min-width: 0;
    }

    .conv-name {
        font-weight: 600;
        margin-bottom: 3px;
    }

    .conv-last {
        color: #666;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-meta {
        text-align: right;
        font-size: 0.82rem;
        color: #888;
    }

    .unread-badge {
        background: #2b6cb0;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        padding: 16px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
    }

    .chat-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .chat-header span {
        color: #777;
        font-size: 0.9rem;
    }

    .messages-container {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .message {
        max-width: 70%;
        align-self: flex-start;
    }

    .message.from-me {
        align-self: flex-end;
    }

    .msg-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        background: #e5efff;
        line-height: 1.4;
    }

    .message.from-me .msg-bubble {
        background: #2b6cb0;
        color: white;
    }

    .msg-time {
        font-size: 0.75rem;
        color: #aaa;
        margin-top: 4px;
        text-align: right;
    }

    .chat-input {
        padding: 16px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 12px;
        background: #f9f9f9;
    }

    .chat-input textarea {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 22px;
        resize: none;
        font-size: 1rem;
        line-height: 1.4;
        max-height: 120px;
    }

    .chat-input button {
        background: #2b6cb0;
        color: white;
        border: none;
        padding: 0 24px;
        border-radius: 22px;
        cursor: pointer;
        font-weight: 600;
    }

    .chat-input button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .no-chat-selected {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-size: 1.1rem;
    }

    @media (max-width: 900px) {
        .messages-layout {
            flex-direction: column;
        }
        .conv-list {
            width: 100%;
            max-height: 40vh;
            border-right: none;
            border-bottom: 1px solid #eee;
        }
    }
</style>
