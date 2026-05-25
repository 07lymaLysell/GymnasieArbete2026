<script lang="ts">
    interface Props {
        messageText: string;
        onMessageChange: (text: string) => void;
        onSendMessage: () => void;
    }

    let { messageText, onMessageChange, onSendMessage }: Props = $props();

    function handleKeydown(e: KeyboardEvent) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            onSendMessage();
        }
    }
</script>

<div class="chat-input">
    <!-- svelte-ignore element_invalid_self_closing_tag -->
    <!-- svelte-ignore event_directive_deprecated -->
    <textarea
        value={messageText}
        on:input={(e) => onMessageChange(e.currentTarget.value)}
        on:keydown={handleKeydown}
        placeholder="Skriv ett meddelande..."
        rows="1"
    />
    <!-- svelte-ignore event_directive_deprecated -->
    <button on:click={onSendMessage} disabled={!messageText.trim()}>
        Skicka
    </button>
</div>

<style>
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
</style>
