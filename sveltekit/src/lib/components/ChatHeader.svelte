<script lang="ts">
    interface Conversation {
        other_id: number;
        display_name: string;
        username: string;
        bio?: string;
        message: string;
        created_at: string;
    }

    interface Props {
        selectedConv: Conversation;
    }

    let { selectedConv }: Props = $props();
</script>

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

<style>
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
</style>
