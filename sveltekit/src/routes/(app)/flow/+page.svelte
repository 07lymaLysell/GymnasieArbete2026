<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";

    let posts: any[] = [];
    let newPostText = "";

    // Dummy data for now — replace with real fetch to /api/getAllPosts.php later
    onMount(() => {
        posts = [
            {
                pid: 1,
                username: "SaraK",
                display_name: "Sara Karlsson",
                post_txt: "Vilken underbar sol idag! 🌞",
                date: "2 min sedan",
                comments: 5,
            },
            {
                pid: 2,
                username: "LukasB",
                display_name: "Lukas Berg",
                post_txt: "Just avslutat en grym löprunda 🏃‍♂️ 8 km!",
                date: "47 min sedan",
                comments: 12,
            },
            // ... more
        ];
    });

    async function addPost() {
        if (!newPostText.trim()) return;
        // TODO: call your addPost API
        posts = [
            {
                pid: Date.now(),
                username: "Du",
                display_name: "Du",
                post_txt: newPostText,
                date: "just nu",
                comments: 0,
            },
            ...posts,
        ];
        newPostText = "";
    }
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Flödet</h1>
        <p class="lead">Vad händer bland dina vänner?</p>
    </header>

    <!-- New post box -->
    <div class="card new-post-box">
        <textarea
            bind:value={newPostText}
            placeholder="Vad tänker du på just nu?"
        ></textarea>
        <button on:click={addPost} class="post-btn">Publicera</button>
    </div>

    <!-- Feed -->
    {#each posts as post}
        <div class="post-card">
            <div class="post-header">
                <strong>{post.display_name}</strong>
                <span class="username">@{post.username}</span>
                <span class="time">· {post.date}</span>
            </div>
            <p class="post-text">{post.post_txt}</p>
            <div class="post-actions">
                <button>❤️ {post.comments}</button>
                <button>💬 Kommentera</button>
            </div>
        </div>
    {/each}
</main>

<style>
    /* Same base as account */
    .main-content {
        max-width: 1100px;
        margin: 28px auto;
        padding: 24px;
        background: #ffffffcc;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .new-post-box,
    .post-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .new-post-box textarea {
        width: 100%;
        min-height: 110px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 14px;
        font-size: 1.05rem;
    }

    .post-header {
        margin-bottom: 10px;
        color: #555;
    }
    .username {
        color: #888;
    }
    .time {
        color: #aaa;
        font-size: 0.9rem;
    }

    .post-text {
        font-size: 1.05rem;
        line-height: 1.5;
        margin: 12px 0;
    }

    .post-actions button {
        background: none;
        border: none;
        color: #2b6cb0;
        font-size: 0.95rem;
        margin-right: 20px;
        cursor: pointer;
    }
</style>
