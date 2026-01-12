<script>
    import { base } from "$app/paths";
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Min profil</h1>
        <p class="lead">Kontoinställningar och personlig information</p>
    </header>

    <section class="profile-card">
        <div class="profile-left">
            <figure class="pfp">
                <img src="/assets/pfp.png" alt="Profilbild för Max Lysell" />
            </figure>

            <div class="basic-info">
                <h2 class="name">Namn Efternamn</h2>
                <p class="role">Roll / Titel</p>

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
                        <dt>Name</dt>
                        <dd>Max</dd>
                    </div>
                    <div>
                        <dt>Age</dt>
                        <dd>18</dd>
                    </div>
                    <div>
                        <dt>Location</dt>
                        <dd>Sweden</dd>
                    </div>
                </dl>
            </section>

            <section class="other-panel">
                <h3>Övrigt</h3>
                <p>
                    Här kan du visa statistik, inställningar eller annan
                    relevant information.
                </p>
            </section>
        </div>
    </section>
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
