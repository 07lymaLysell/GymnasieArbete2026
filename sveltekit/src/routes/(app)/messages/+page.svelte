<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";

    interface Conversation {
        other_id: number;
        display_name: string;
        username: string;
        bio?: string;
        message: string;
        created_at: string;
    }
    interface Msg {
        fromMe: boolean;
        text: string;
        time: string;
    }

    let user: any = null;
    authStore.subscribe((v) => (user = v.user));

    let conversations: Conversation[] = [];
    let selectedConv: Conversation | null = null;
    let messageText = "";
    let messages: Msg[] = [];

    let searchTerm = "";

    // derived list based on search term
    $: filteredConversations = conversations.filter((c) => {
        const nameMatch = c.display_name
            .toLowerCase()
            .includes(searchTerm.toLowerCase());
        const msgMatch = c.message
            .toLowerCase()
            .includes(searchTerm.toLowerCase());
        return searchTerm === "" || nameMatch || msgMatch;
    });

    async function loadConversations() {
        if (!user) return;
        try {
            const res = await fetch(
                `/api/getconversations.php?uid=${user.uid}`,
            );
            const data = await res.json();
            if (data.success) {
                conversations = data.conversations;
            }
        } catch (err) {
            console.error("Failed to load conversations", err);
        }
    }

    async function loadMessages(otherId: number) {
        if (!user) return;
        try {
            const res = await fetch(
                `/api/getmessages.php?uid=${user.uid}&other_id=${otherId}`,
            );
            const data = await res.json();
            if (data.success) {
                messages = data.messages.map((m: any) => ({
                    fromMe: m.from_id === user.uid,
                    text: m.message,
                    time: m.created_at,
                }));
            }
        } catch (err) {
            console.error("Failed to load messages", err);
        }
    }

    async function sendMessage() {
        if (!messageText.trim() || !selectedConv || !user) return;
        try {
            const res = await fetch("/api/addmessage.php", {
                method: "POST",
                body: new URLSearchParams({
                    from_id: String(user.uid),
                    to_id: String(selectedConv.other_id),
                    message: messageText,
                }),
            });
            const data = await res.json();
            if (data.success) {
                await loadMessages(selectedConv.other_id);
                await loadConversations();
                messageText = "";
            }
        } catch (err) {
            console.error("Send error", err);
        }
    }

    function selectConversation(conv: Conversation) {
        selectedConv = conv;
        loadMessages(conv.other_id);
    }

    onMount(async () => {
        await loadConversations();

        // check for ?with=userid parameter to open chat directly
        if (typeof window !== "undefined") {
            const params = new URLSearchParams(window.location.search);
            const withId = params.get("with");
            if (withId) {
                const other = parseInt(withId);
                const conv = conversations.find((c) => c.other_id === other);
                if (conv) {
                    selectConversation(conv);
                } else {
                    // no conversation yet (maybe no messages), still try to load messages
                    selectedConv = {
                        other_id: other,
                        display_name: "",
                        username: "",
                        message: "",
                        created_at: "",
                    } as Conversation;
                    loadMessages(other);
                }
            }
        }
    });
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Meddelanden</h1>
        <p class="lead">Prata med dina vänner</p>
    </header>

    <div class="messages-layout">
        <!-- Vänster konversationslista -->
        <div class="conv-list">
            <div class="search-box">
                <input
                    type="text"
                    bind:value={searchTerm}
                    placeholder="Sök personer eller meddelanden..."
                />
            </div>

            {#each filteredConversations as conv}
                <!-- svelte-ignore a11y_click_events_have_key_events -->
                <!-- svelte-ignore a11y_no_static_element_interactions -->
                <div
                    class="conv-item"
                    class:active={selectedConv?.other_id === conv.other_id}
                    on:click={() => selectConversation(conv)}
                >
                    <img
                        src="/assets/pfp.png"
                        alt={conv.display_name}
                        class="conv-avatar"
                    />
                    <div class="conv-info">
                        <div class="conv-name">{conv.display_name}</div>
                        <div class="conv-last">{conv.message}</div>
                    </div>
                    <div class="conv-meta">
                        <div class="conv-time">{conv.created_at}</div>
                    </div>
                </div>
            {/each}
        </div>

        <!-- Höger: öppen konversation -->
        <div class="chat-area">
            {#if selectedConv}
                <div class="chat-header">
                    <img
                        src="/assets/pfp.png"
                        alt={selectedConv.display_name}
                        class="chat-avatar"
                    />
                    <div>
                        <h3>{selectedConv.display_name}</h3>
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
