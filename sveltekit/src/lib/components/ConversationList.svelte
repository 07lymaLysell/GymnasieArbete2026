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
        conversations: Conversation[];
        filteredConversations: Conversation[];
        selectedConv: Conversation | null;
        searchTerm: string;
        onSearchChange: (term: string) => void;
        onSelectConversation: (conv: Conversation) => void;
    }

    let {
        conversations,
        filteredConversations,
        selectedConv,
        searchTerm,
        onSearchChange,
        onSelectConversation,
    }: Props = $props();
</script>

<div class="conv-list">
    <div class="search-box">
        <!-- svelte-ignore event_directive_deprecated -->
        <input
            type="text"
            value={searchTerm}
            on:input={(e) => onSearchChange(e.currentTarget.value)}
            placeholder="Sök personer eller meddelanden..."
        />
    </div>

    {#each filteredConversations as conv}
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <!-- svelte-ignore event_directive_deprecated -->
        <div
            class="conv-item"
            class:active={selectedConv?.other_id === conv.other_id}
            on:click={() => onSelectConversation(conv)}
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

<style>
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

    @media (max-width: 900px) {
        .conv-list {
            width: 100%;
            max-height: 40vh;
            border-right: none;
            border-bottom: 1px solid #eee;
        }
    }
</style>
