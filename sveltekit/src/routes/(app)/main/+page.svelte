<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";
    import { goto } from "$app/navigation";

    let user: any = null;

    onMount(() => {
        const unsub = authStore.subscribe((auth) => {
            if (auth?.isLoggedIn && auth.user) {
                user = auth.user;
            } else {
                goto("/");
            }
        });
        return unsub;
    });
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Välkommen tillbaka, {user?.firstname || "vän"}! 👋</h1>
        <p class="lead">Här är vad som händer i ditt nätverk idag</p>
    </header>

    <div class="home-grid">
        <!-- Quick create post -->
        <div class="card create-post">
            <textarea placeholder="Vad tänker du på idag?" rows="3"></textarea>
            <button class="post-btn">Publicera</button>
        </div>

        <!-- Stats cards -->
        <div class="stats">
            <div class="stat-card">
                <span class="number">24</span>
                <span class="label">Inlägg</span>
            </div>
            <div class="stat-card">
                <span class="number">87</span>
                <span class="label">Vänner</span>
            </div>
            <div class="stat-card">
                <span class="number">12</span>
                <span class="label">Nya kommentarer</span>
            </div>
        </div>

        <!-- Welcome message / tip -->
        <div class="card welcome-tip">
            <h3>💡 Tips för idag</h3>
            <p>
                Berätta för dina vänner vad du gjorde i helgen — det ger alltid
                mest engagemang!
            </p>
        </div>
    </div>
</main>

<style>
    .main-content {
        /* same as account */
        max-width: 1100px;
        margin: 28px auto;
        padding: 24px;
        background: #ffffffcc;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .page-header h1 {
        font-size: 1.8rem;
        margin: 0 0 8px;
    }
    .lead {
        color: #666;
        margin: 0;
    }

    .home-grid {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 20px;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .create-post textarea {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 14px;
        resize: none;
        font-size: 1rem;
    }

    .post-btn {
        margin-top: 12px;
        background: #2b6cb0;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }

    .stats {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 18px;
        text-align: center;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .number {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2b6cb0;
        display: block;
    }

    .label {
        color: #666;
        font-size: 0.95rem;
    }

    @media (max-width: 900px) {
        .home-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
