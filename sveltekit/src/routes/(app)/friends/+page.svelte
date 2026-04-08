<script lang="ts">
    import { onMount } from "svelte";
    import { authStore } from "$lib/stores/auth";
    import { goto } from "$app/navigation";

    interface User {
        // API sometimes returns `id` (friends) and sometimes `uid` (search results)
        id?: number;
        uid?: number;
        display_name: string;
        username: string;
        bio: string;
    }

    let search = $state("");
    let searchResults = $state<User[]>([]);
    let friends = $state<User[]>([]);
    let user = $state<any>(null);

    authStore.subscribe((val) => {
        user = val.user;
        if (user) {
            loadFriends();
        }
    });

    async function loadFriends() {
        if (!user) return;
        try {
            const response = await fetch(`/api/getfriends.php?uid=${user.uid}`);
            const data = await response.json();
            if (data.success) {
                friends = data.friends;
            }
        } catch (err) {
            console.error("Failed to load friends", err);
        }
    }

    async function searchUsers() {
        if (!search.trim()) {
            searchResults = [];
            return;
        }
        try {
            const response = await fetch(
                `/api/findusers.php?q=${encodeURIComponent(search)}`,
            );
            const data = await response.json();
            if (data.success) {
                // exclude self and already friended
                searchResults = data.users.filter(
                    (u: any) =>
                        u.uid !== user.uid &&
                        !friends.find((f) => f.id === u.uid),
                );
            }
        } catch (err) {
            console.error("Search error", err);
        }
    }

    async function addFriend(uid: number) {
        if (!user) return;
        try {
            const response = await fetch("/api/addfriend.php", {
                method: "POST",
                body: new URLSearchParams({
                    uid: String(user.uid),
                    friend_id: String(uid),
                }),
            });
            const data = await response.json();
            if (data.success) {
                await loadFriends();
                await searchUsers();
            }
        } catch (err) {
            console.error("Failed to add friend", err);
        }
    }

    function goToConversation(id: number) {
        goto(`/messages?with=${id}`);
    }
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
            oninput={searchUsers}
        />
    </div>

    {#if search && searchResults.length > 0}
        <section class="search-results">
            <h2>Sökresultat</h2>
            <div class="friends-grid">
                {#each searchResults as u}
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
                        <button
                            class="add-btn"
                            onclick={() => addFriend(u.uid ?? u.id ?? 0)}
                        >
                            Lägg till
                        </button>
                    </div>
                {/each}
            </div>
        </section>
    {/if}

    <h2>Dina vänner</h2>
    <div class="friends-grid">
        {#each friends as u}
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
                <button class="add-btn" onclick={() => goToConversation(u.id!)}>
                    Meddela
                </button>
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
