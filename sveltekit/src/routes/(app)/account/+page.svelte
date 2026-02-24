<script lang="ts">
    import { goto } from "$app/navigation";
    import { authStore, loginUser } from "$lib/stores/auth";
    import { onMount } from "svelte";

    // Local user object populated from authStore
    let user: any = null;

    // biography and saving state for "Om mig" panel
    let biography = "";
    let saving = false;
    let saveMessage = "";

    // Reactive full name derived from user
    let fullName = "Inloggad användare";

    // Subscribe to authStore on mount and keep local user in sync
    onMount(() => {
        const unsubscribe = authStore.subscribe((auth) => {
            if (auth && auth.isLoggedIn && auth.user) {
                user = auth.user;
                biography = user.biography || "";
                // Compute fullName
                fullName =
                    (
                        (user.firstname || "") +
                        " " +
                        (user.surname || "")
                    ).trim() ||
                    user.username ||
                    "Användare";
            } else {
                goto("/");
            }
        });

        return unsubscribe;
    });

    // Save biography to backend and update store + localStorage
    async function saveBio() {
        if (!user || typeof user.uid === "undefined") return;
        saving = true;
        saveMessage = "";

        try {
            const res = await fetch("http://localhost/api/upd-bio.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ uid: user.uid, biography }),
            });

            const data = await res.json();
            if (data.success) {
                // Update local auth store and localStorage
                const updatedUser = { ...user, biography };
                loginUser(updatedUser);
                saveMessage = "Sparat";
            } else {
                saveMessage = data.message || "Ett fel uppstod";
            }
        } catch (e) {
            saveMessage = "Nätverksfel";
            console.error(e);
        } finally {
            saving = false;
            setTimeout(() => (saveMessage = ""), 3000);
        }
    }
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Min profil</h1>
        <p class="lead">Kontoinställningar och personlig information</p>
    </header>

    {#if user}
        <section class="profile-card">
            <div class="profile-left">
                <figure class="pfp">
                    <img
                        src="/assets/pfp.png"
                        alt="Profilbild för {fullName}"
                    />
                </figure>

                <div class="basic-info">
                    <h2 class="name">{fullName}</h2>
                    <p class="role">{user.role || "Medlem"}</p>
                    <!-- fallback if no role exists -->

                    <nav class="profile-actions" aria-label="Profile actions">
                        <button type="button">Redigera profil</button>
                        <button type="button">Byt lösenord</button>
                    </nav>
                </div>
            </div>

            <div class="profile-right">
                <section class="info-panel" aria-labelledby="personal-info">
                    <h3 id="personal-info">Personlig information</h3>
                    <dl class="info-list">
                        <div>
                            <dt>Användarnamn</dt>
                            <dd>{user.username || "—"}</dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>{user.email || "—"}</dd>
                        </div>
                        <div>
                            <dt>Förnamn</dt>
                            <dd>{user.firstname || "—"}</dd>
                        </div>
                        <div>
                            <dt>Efternamn</dt>
                            <dd>{user.surname || "—"}</dd>
                        </div>
                        <!-- Add more fields if they exist in user object, e.g. -->
                        <!-- <div><dt>Ålder</dt><dd>{user.age || "—"}</dd></div> -->
                        <!-- <div><dt>Land</dt><dd>{user.location || "Sverige"}</dd></div> -->
                    </dl>
                </section>

                <!-- Inside .profile-right -->

                <section class="other-panel">
                    <h3>Om mig</h3>

                    <!-- svelte-ignore element_invalid_self_closing_tag -->
                    <textarea
                        bind:value={biography}
                        placeholder="Berätta lite om dig själv..."
                        rows="5"
                        style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;"
                    />

                    <div style="margin-top:12px;">
                        <button
                            on:click={saveBio}
                            disabled={saving ||
                                biography === (user.biography || "")}
                        >
                            {saving ? "Sparar..." : "Spara"}
                        </button>

                        {#if saveMessage}
                            <span
                                style="margin-left:12px; color: {saveMessage.includes(
                                    'fel',
                                )
                                    ? 'red'
                                    : 'green'};"
                            >
                                {saveMessage}
                            </span>
                        {/if}
                    </div>
                </section>
            </div>
        </section>
    {:else}
        <p style="text-align: center; color: #666;">Laddar profil...</p>
    {/if}
</main>

<style>
    :root {
        --card-bg: #ffffffcc;
        --muted: #666;
        --accent: #2b6cb0;
        --gap: 18px;
        --max-width: 1100px;
    }

    .main-content {
        max-width: var(--max-width);
        margin: 28px auto;
        padding: 24px;
        background: var(--card-bg);
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        box-sizing: border-box;
    }

    /* header */
    .page-header {
        margin-bottom: 18px;
    }
    .page-header h1 {
        margin: 0 0 6px;
        font-size: 1.6rem;
    }
    .lead {
        margin: 0;
        color: var(--muted);
    }

    /* profile card: grid layout */
    .profile-card {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: var(--gap);
    }

    /* left column */
    .profile-left {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
        text-align: center;
    }

    .pfp img {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid rgba(0, 0, 0, 0.06);
        display: block;
    }

    /* basic info & actions */
    .name {
        margin: 6px 0 2px;
        font-size: 1.1rem;
    }
    .role {
        margin: 0;
        color: var(--muted);
        font-size: 0.95rem;
    }

    .profile-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }
    .profile-actions button {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: white;
        cursor: pointer;
        font-size: 0.9rem;
    }
    .profile-actions button:hover {
        transform: translateY(-1px);
    }

    /* right column panels */
    .info-panel,
    .other-panel {
        background: rgba(255, 255, 255, 0.6);
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }
    .info-panel h3,
    .other-panel h3 {
        margin: 0 0 10px;
        font-size: 1rem;
    }

    .info-list {
        display: grid;
        gap: 8px;
    }
    .info-list dt {
        font-weight: 600;
        color: var(--muted);
    }
    .info-list dd {
        margin: 2px 0 8px 0;
        color: #222;
    }

    /* responsive: stack columns on small screens */
    @media (max-width: 800px) {
        .profile-card {
            grid-template-columns: 1fr;
        }
        .profile-left {
            flex-direction: row;
            align-items: center;
            text-align: left;
            gap: 14px;
        }
        .pfp img {
            width: 84px;
            height: 84px;
        }
        .profile-actions {
            margin-left: auto;
        }
    }
</style>
