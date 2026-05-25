<script lang="ts">
    import ChatHeader from "./ChatHeader.svelte";
    import ChatMessage from "./ChatMessage.svelte";
    import ChatInput from "./ChatInput.svelte";

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

    interface Props {
        selectedConv: Conversation | null;
        messages: Msg[];
        messageText: string;
        onMessageChange: (text: string) => void;
        onSendMessage: () => void;
    }

    let {
        selectedConv,
        messages,
        messageText,
        onMessageChange,
        onSendMessage,
    }: Props = $props();
</script>

<div class="chat-area">
    {#if selectedConv}
        <ChatHeader {selectedConv} />

        <div class="messages-container">
            {#each messages as msg}
                <ChatMessage message={msg} />
            {/each}
        </div>

        <ChatInput {messageText} {onMessageChange} {onSendMessage} />
    {:else}
        <div class="no-chat-selected">
            <p>Välj en konversation för att börja prata</p>
        </div>
    {/if}
</div>

<style>
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .messages-container {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .no-chat-selected {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-size: 1.1rem;
    }
</style>
