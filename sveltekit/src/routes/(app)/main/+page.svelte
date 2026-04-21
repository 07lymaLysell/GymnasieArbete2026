<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";
    import { goto } from "$app/navigation";

    let user: any = null;
    let postText = "";
    let stats = { posts: 0, friends: 0, comments: 0 };
    let loading = true;
    let savingPost = false;
    let statusMessage = "";

    onMount(() => {
        const unsubscribe = authStore.subscribe((auth) => {
            if (auth?.isLoggedIn && auth.user) {
                user = auth.user;
                loadDashboardStats();
            } else {
                goto("/");
            }
        });
        return unsubscribe;
    });

    async function loadDashboardStats() {
        if (!user?.uid) return;

        loading = true;
        stats = { posts: 0, friends: 0, comments: 0 };

        try {
            const [friendsRes, threadsRes] = await Promise.all([
                fetch(`/api/getfriends.php?uid=${user.uid}`),
                fetch(`/api/getallthreads.php`),
            ]);

            if (friendsRes.ok) {
                const data = await friendsRes.json();
                stats.friends = Array.isArray(data.friends)
                    ? data.friends.length
                    : 0;
            }

            if (threadsRes.ok) {
                const data = await threadsRes.json();
                const threads = Array.isArray(data.threads)
                    ? (data.threads as any[])
                    : [];
                const myThreads = threads.filter(
                    (thread: any) =>
                        Number(thread.user_id) === Number(user.uid),
                );
                stats.posts = myThreads.length;
                stats.comments = myThreads.reduce(
                    (sum: number, thread: any) =>
                        sum + Number(thread.comment_count || 0),
                    0,
                );
            }
        } catch (error) {
            console.error("Failed to load dashboard stats", error);
        } finally {
            loading = false;
        }
    }

    async function createPost() {
        if (!user?.uid) return;
        const content = postText.trim();
        if (!content) {
            statusMessage = "Skriv något innan du publicerar.";
            return;
        }

        savingPost = true;
        statusMessage = "";

        try {
            const formData = new FormData();
            formData.append("uid", String(user.uid));
            formData.append("content", content);

            const res = await fetch("/api/addthread.php", {
                method: "POST",
                body: formData,
            });
            const data = await res.json();

            if (data.success) {
                postText = "";
                statusMessage = "Inlägget publicerades.";
                await loadDashboardStats();
            } else {
                statusMessage =
                    data.message || "Kunde inte publicera inlägget.";
            }
        } catch (error) {
            console.error("Create post failed", error);
            statusMessage = "Nätverksfel vid publicering.";
        } finally {
            savingPost = false;
            setTimeout(() => {
                statusMessage = "";
            }, 3500);
        }
    }
</script>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1>
                Välkommen tillbaka, {user?.firstname ||
                    user?.username ||
                    "vän"}!
            </h1>
            <p class="lead">Din översikt är uppdaterad med ditt konto.</p>
        </div>

        {#if loading}
            <p class="subtitle">Hämtar dina siffror...</p>
        {/if}
    </header>

    <div class="home-grid">
        <section class="card create-post">
            <h2>Skapa nytt inlägg</h2>
            <p class="description">
                Dela vad du jobbar med just nu eller något som inspirerar dig.
            </p>
            <textarea
                bind:value={postText}
                placeholder="Skriv något..."
                rows="4"
            ></textarea>
            <button
                class="post-btn"
                on:click={createPost}
                disabled={savingPost || !postText.trim()}
            >
                {savingPost ? "Publicerar..." : "Publicera"}
            </button>
            {#if statusMessage}
                <p class="status-message">{statusMessage}</p>
            {/if}
        </section>

        <section class="card stats-panel">
            <h2>Din aktivitet</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="number">{stats.posts}</span>
                    <span class="label">Inlägg</span>
                </div>
                <div class="stat-card">
                    <span class="number">{stats.friends}</span>
                    <span class="label">Vänner</span>
                </div>
                <div class="stat-card">
                    <span class="number">{stats.comments}</span>
                    <span class="label">Kommentarer på dina inlägg</span>
                </div>
            </div>
            <p class="stats-note">
                Uppdatera sidan om du nyligen lagt till en vän eller nytt
                inlägg.
            </p>
        </section>

        <section class="card summary-card">
            <h3>Hur ser det ut?</h3>
            <p>
                {#if stats.posts > 0}
                    Du har publicerat {stats.posts}
                    {stats.posts === 1 ? "inlägg" : "inlägg"} och fått {stats.comments}
                    {stats.comments === 1 ? "kommentar" : "kommentarer"}.
                {:else}
                    Du har inte publicerat något än. Börja med att skriva en
                    uppdatering ovan.
                {/if}
            </p>
            <p>
                Du har {stats.friends}
                {stats.friends === 1 ? "vän" : "vänner"} anslutna till ditt konto.
            </p>
        </section>
    </div>
</main>

<style>
    .main-content {
        max-width: 1100px;
        margin: 28px auto;
        padding: 24px;
        background: #ffffffcc;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 18px;
        margin-bottom: 16px;
    }

    .page-header h1 {
        font-size: 2rem;
        margin: 0;
    }

    .lead,
    .subtitle {
        color: #4a5568;
        margin: 6px 0 0;
    }

    .subtitle {
        font-size: 0.95rem;
    }

    .home-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    }

    .create-post h2,
    .stats-panel h2,
    .summary-card h3 {
        margin: 0 0 12px;
        font-size: 1.2rem;
    }

    .description {
        margin: 0 0 14px;
        color: #555;
        line-height: 1.5;
    }

    textarea {
        width: 100%;
        border: 1px solid #d8dee4;
        border-radius: 14px;
        padding: 16px;
        resize: vertical;
        min-height: 140px;
        font-size: 1rem;
        line-height: 1.5;
        color: #2d3748;
    }

    textarea:focus {
        outline: none;
        border-color: #3182ce;
        box-shadow: 0 0 0 4px rgba(66, 153, 225, 0.12);
    }

    .post-btn {
        margin-top: 16px;
        background: #2b6cb0;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        transition: background 0.2s ease;
    }

    .post-btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 14px;
    }

    .stat-card {
        background: #f7fafc;
        border-radius: 14px;
        padding: 18px;
        text-align: center;
        border: 1px solid rgba(66, 153, 225, 0.12);
    }

    .number {
        font-size: 2rem;
        font-weight: 700;
        color: #2b6cb0;
        display: block;
    }

    .label {
        color: #4a5568;
        font-size: 0.95rem;
    }

    .summary-card p {
        margin: 0 0 12px;
        color: #4a5568;
        line-height: 1.6;
    }

    .status-message {
        margin-top: 12px;
        color: #2f855a;
    }

    .stats-note {
        margin-top: 14px;
        color: #718096;
        font-size: 0.95rem;
    }

    @media (max-width: 900px) {
        .home-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
