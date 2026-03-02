<script lang="ts">
    let search = "";
    // Dummy users — replace with fetch to your findUsers / getUsers API
    let users = [
        {
            id: 1,
            display_name: "Emma Lind",
            username: "emmalind",
            bio: "Älskar att baka och resa",
        },
        {
            id: 2,
            display_name: "Oskar Berg",
            username: "oskberg",
            bio: "Fotograf och kaffeentusiast",
        },
        // ...
    ];
</script>

<main class="main-content">
    <header class="page-header">
        <h1>Vänner & Nätverk</h1>
    </header>

    <div class="search-bar">
        <input
            type="text"
            bind:value={search}
            placeholder="Sök efter användare..."
        />
    </div>

    <div class="friends-grid">
        {#each users as u}
            <div class="user-card">
                <img
                    src="/assets/pfp.png"
                    alt={u.display_name}
                    class="mini-pfp"
                />
                <div>
                    <strong>{u.display_name}</strong>
                    <div class="username">@{u.username}</div>
                    <p class="bio">{u.bio}</p>
                </div>
                <button class="add-btn">Lägg till</button>
            </div>
        {/each}
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

    .search-bar input {
        width: 100%;
        padding: 14px 20px;
        font-size: 1.1rem;
        border: 1px solid #ddd;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .friends-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .user-card {
        background: white;
        border-radius: 12px;
        padding: 18px;
        display: flex;
        gap: 16px;
        align-items: center;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .mini-pfp {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        object-fit: cover;
    }

    .username {
        color: #777;
        font-size: 0.95rem;
    }
    .bio {
        margin: 6px 0 0;
        font-size: 0.9rem;
        color: #555;
        line-height: 1.4;
    }

    .add-btn {
        margin-left: auto;
        background: #2b6cb0;
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
    }
</style>
