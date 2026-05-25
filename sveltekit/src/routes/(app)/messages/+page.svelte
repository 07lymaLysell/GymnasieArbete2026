<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";
    import ConversationList from "$lib/components/ConversationList.svelte";
    import ChatWindow from "$lib/components/ChatWindow.svelte";

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

    let user = $state<any>(null);
    let conversations = $state<Conversation[]>([]);
    let selectedConv = $state<Conversation | null>(null);
    let messageText = $state("");
    let messages = $state<Msg[]>([]);
    let searchTerm = $state("");

    // derived list based on search term
    let filteredConversations = $derived(
        conversations.filter((c) => {
            const nameMatch = c.display_name
                .toLowerCase()
                .includes(searchTerm.toLowerCase());
            const msgMatch = c.message
                .toLowerCase()
                .includes(searchTerm.toLowerCase());
            return searchTerm === "" || nameMatch || msgMatch;
        }),
    );

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

    $effect(() => {
        const unsubscribe = authStore.subscribe((v) => {
            user = v.user;
        });
        return unsubscribe;
    });

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
        <ConversationList
            {conversations}
            {filteredConversations}
            {selectedConv}
            {searchTerm}
            onSearchChange={(term) => (searchTerm = term)}
            onSelectConversation={selectConversation}
        />

        <ChatWindow
            {selectedConv}
            {messages}
            {messageText}
            onMessageChange={(text) => (messageText = text)}
            onSendMessage={sendMessage}
        />
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

    @media (max-width: 900px) {
        .messages-layout {
            flex-direction: column;
        }
    }
</style>
