<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";

    interface Thread {
        thread_id: number;
        user_id: number;
        display_name: string;
        username: string;
        content: string;
        created_at: string;
        comment_count?: number;
    }

    interface Comment {
        id: number;
        user_id: number;
        display_name: string;
        username: string;
        content: string;
        created_at: string;
    }

    let user = $state<any>(null);
    let threads = $state<Thread[]>([]);
    let newPostContent = $state("");
    let expandedThread = $state<number | null>(null);
    let threadComments = $state<Record<number, Comment[]>>({});
    let commentText = $state<Record<number, string>>({});
    let isPosting = $state(false);

    authStore.subscribe((val) => {
        user = val.user;
    });

    onMount(() => {
        loadThreads();
    });

    async function loadThreads() {
        try {
            const response = await fetch("/api/getallthreads.php");
            const data = await response.json();
            if (data.success) {
                threads = data.threads;
            }
        } catch (err) {
            console.error("Failed to load threads", err);
        }
    }

    async function loadCommentsForThread(threadId: number) {
        try {
            const response = await fetch(
                `/api/getcomments.php?thread_id=${threadId}`,
            );
            const data = await response.json();
            if (data.success) {
                threadComments = {
                    ...threadComments,
                    [threadId]: data.comments,
                };
            }
        } catch (err) {
            console.error("Failed to load comments", err);
        }
    }

    async function createPost() {
        if (!newPostContent.trim() || !user) return;

        isPosting = true;
        try {
            const response = await fetch("/api/addthread.php", {
                method: "POST",
                body: new URLSearchParams({
                    uid: String(user.uid),
                    content: newPostContent,
                }),
            });
            const data = await response.json();
            if (data.success) {
                newPostContent = "";
                await loadThreads();
            }
        } catch (err) {
            console.error("Failed to create post", err);
        } finally {
            isPosting = false;
        }
    }

    async function addComment(threadId: number) {
        if (!user) return;
        const content = commentText[threadId] || "";
        if (!content.trim()) return;

        try {
            const response = await fetch("/api/addcomment.php", {
                method: "POST",
                body: new URLSearchParams({
                    thread_id: String(threadId),
                    uid: String(user.uid),
                    content: content,
                }),
            });
            const data = await response.json();
            if (data.success) {
                const updatedCommentText = { ...commentText };
                delete updatedCommentText[threadId];
                commentText = updatedCommentText;
                await loadCommentsForThread(threadId);
            }
        } catch (err) {
            console.error("Failed to add comment", err);
        }
    }

    function toggleThreadExpand(threadId: number) {
        if (expandedThread === threadId) {
            expandedThread = null;
        } else {
            expandedThread = threadId;
            if (!threadComments[threadId]) {
                loadCommentsForThread(threadId);
            }
        }
    }
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Flöde</h1>
        <p class="lead">Dela dina tankar och ha diskussioner</p>
    </header>

    <div class="flow-container">
        <!-- Skapa nytt inlägg -->
        <div class="create-post">
            <img
                src="/assets/pfp.png"
                alt={user?.display_name}
                class="user-pfp"
            />
            <div class="post-input-area">
                <textarea
                    bind:value={newPostContent}
                    placeholder="Vad tänker du på?"
                    class="post-input"
                ></textarea>
                <button
                    class="post-btn"
                    onclick={() => createPost()}
                    disabled={newPostContent.trim() === "" || isPosting}
                >
                    {isPosting ? "Publicerar..." : "Publicera"}
                </button>
            </div>
        </div>

        <!-- Trådar/inlägg -->
        <div class="threads-list">
            {#each threads as thread (thread.thread_id)}
                <div class="thread-card">
                    <div class="thread-header">
                        <img
                            src="/assets/pfp.png"
                            alt={thread.display_name}
                            class="thread-pfp"
                        />
                        <div class="thread-user-info">
                            <div class="user-name">{thread.display_name}</div>
                            <div class="user-handle">@{thread.username}</div>
                            <div class="thread-date">{thread.created_at}</div>
                        </div>
                    </div>

                    <div class="thread-content">
                        {thread.content}
                    </div>

                    <button
                        class="expand-btn"
                        onclick={() => toggleThreadExpand(thread.thread_id)}
                    >
                        {expandedThread === thread.thread_id
                            ? "Dölj kommentarer"
                            : "Visa kommentarer"}
                    </button>

                    {#if expandedThread === thread.thread_id}
                        <div class="comments-section">
                            <div class="comments-list">
                                {#if threadComments[thread.thread_id] && threadComments[thread.thread_id].length > 0}
                                    {#each threadComments[thread.thread_id] as comment (comment.id)}
                                        <div class="comment">
                                            <img
                                                src="/assets/pfp.png"
                                                alt={comment.display_name}
                                                class="comment-pfp"
                                            />
                                            <div class="comment-content">
                                                <div class="comment-header">
                                                    <strong
                                                        >{comment.display_name}</strong
                                                    >
                                                    <span
                                                        class="comment-handle"
                                                    >
                                                        @{comment.username}
                                                    </span>
                                                </div>
                                                <div class="comment-text">
                                                    {comment.content}
                                                </div>
                                                <div class="comment-date">
                                                    {comment.created_at}
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                {:else}
                                    <div class="no-comments">
                                        Ingen har kommenterat ännu
                                    </div>
                                {/if}
                            </div>

                            <div class="add-comment-area">
                                <input
                                    type="text"
                                    value={commentText[thread.thread_id] || ""}
                                    oninput={(e) =>
                                        (commentText = {
                                            ...commentText,
                                            [thread.thread_id]:
                                                e.currentTarget.value,
                                        })}
                                    placeholder="Skriv en kommentar..."
                                    class="comment-input"
                                />
                                <button
                                    class="comment-btn"
                                    onclick={() => addComment(thread.thread_id)}
                                    disabled={!(
                                        commentText[thread.thread_id] || ""
                                    ).trim()}
                                >
                                    Kommentera
                                </button>
                            </div>
                        </div>
                    {/if}
                </div>
            {/each}

            {#if threads.length === 0}
                <div class="no-threads">
                    <p>Inga inlägg ännu. Var först med att publicera något!</p>
                </div>
            {/if}
        </div>
    </div>
</main>

<style>
    .main-content {
        max-width: 700px;
        margin: 28px auto;
        padding: 24px;
        background: #ffffffcc;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .page-header {
        margin-bottom: 28px;
    }

    .page-header h1 {
        margin: 0 0 8px;
        font-size: 1.8rem;
    }

    .lead {
        color: #666;
        margin: 0;
        font-size: 0.95rem;
    }

    .flow-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Skapa inlägg */
    .create-post {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .user-pfp {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .post-input-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .post-input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
    }

    .post-btn {
        align-self: flex-end;
        background: #2b6cb0;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .post-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    /* Trådar */
    .threads-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .thread-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .thread-header {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
    }

    .thread-pfp {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .thread-user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .user-handle {
        color: #777;
        font-size: 0.85rem;
    }

    .thread-date {
        color: #999;
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .thread-content {
        margin: 12px 0;
        line-height: 1.5;
        color: #333;
    }

    .expand-btn {
        background: transparent;
        border: 1px solid #ddd;
        color: #2b6cb0;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: background 0.15s;
    }

    .expand-btn:hover {
        background: #f0f4ff;
    }

    /* Kommentarer */
    .comments-section {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #eee;
    }

    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 16px;
    }

    .comment {
        display: flex;
        gap: 10px;
    }

    .comment-pfp {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .comment-content {
        flex: 1;
        background: #f5f5f5;
        padding: 10px 12px;
        border-radius: 8px;
    }

    .comment-header {
        font-size: 0.9rem;
        margin-bottom: 4px;
    }

    .comment-handle {
        color: #777;
        margin-left: 6px;
    }

    .comment-text {
        font-size: 0.9rem;
        color: #333;
        line-height: 1.4;
    }

    .comment-date {
        color: #999;
        font-size: 0.75rem;
        margin-top: 4px;
    }

    .no-comments {
        text-align: center;
        color: #999;
        padding: 16px;
        font-size: 0.9rem;
    }

    .add-comment-area {
        display: flex;
        gap: 8px;
    }

    .comment-input {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: inherit;
    }

    .comment-btn {
        background: #2b6cb0;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .comment-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .no-threads {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .no-threads p {
        margin: 0;
        font-size: 1.1rem;
    }
</style>
