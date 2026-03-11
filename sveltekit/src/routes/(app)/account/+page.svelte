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

    // Password change state
    let showPasswordForm = false;
    let currentPassword = "";
    let newPassword = "";
    let confirmPassword = "";
    let changing = false;
    let changeMessage = "";

    // Profile picture upload state
    let uploading = false;
    let uploadMessage = "";
    let uploadSuccess = false;

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

    // Change password handler
    async function changePassword() {
        if (!user || typeof user.uid === "undefined") return;
        if (newPassword !== confirmPassword) {
            changeMessage = "Lösenorden matchar inte";
            return;
        }
        if (newPassword.length < 6) {
            changeMessage = "Lösenord måste vara minst 6 tecken";
            return;
        }

        changing = true;
        changeMessage = "";

        try {
            const res = await fetch("http://localhost/api/upd-password.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    uid: user.uid,
                    currentPassword,
                    newPassword,
                }),
            });

            const data = await res.json();
            if (data.success) {
                changeMessage = "Lösenord uppdaterat";
                showPasswordForm = false;
                currentPassword = newPassword = confirmPassword = "";
            } else {
                changeMessage = data.message || "Ett fel uppstod";
            }
        } catch (e) {
            changeMessage = "Nätverksfel";
            console.error(e);
        } finally {
            changing = false;
            setTimeout(() => (changeMessage = ""), 3000);
        }
    }

    // Handle profile picture upload
    async function handlePfpUpload(event: Event) {
        const input = event.target as HTMLInputElement;
        const file = input.files?.[0];
        if (!file) return;

        // Basic client-side validation
        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/webp",
        ];
        if (!allowedTypes.includes(file.type)) {
            uploadMessage = "Endast jpg, png, gif eller webp tillåts";
            uploadSuccess = false;
            return;
        }

        if (file.size > 3 * 1024 * 1024) {
            // 3 MB
            uploadMessage = "Filen är för stor (max 3 MB)";
            uploadSuccess = false;
            return;
        }

        uploading = true;
        uploadMessage = "Laddar upp...";
        uploadSuccess = false;

        const formData = new FormData();
        formData.append("profile_picture", file);
        formData.append("uid", user.uid.toString());

        try {
            const res = await fetch(
                "http://localhost/api/upd-profile-picture.php",
                {
                    method: "POST",
                    body: formData,
                },
            );

            const data = await res.json();

            if (data.success) {
                // Update local user object and store
                user = { ...user, profile_picture: data.path };
                loginUser(user);

                uploadMessage = "Profilbild uppdaterad!";
                uploadSuccess = true;
            } else {
                uploadMessage = data.message || "Kunde inte ladda upp bilden";
                uploadSuccess = false;
            }
        } catch (err) {
            uploadMessage = "Nätverksfel vid uppladdning";
            uploadSuccess = false;
            console.error(err);
        } finally {
            uploading = false;
            input.value = ""; // reset file input
            setTimeout(() => (uploadMessage = ""), 4000);
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
                        src={user.profile_picture || "/assets/pfp.png"}
                        alt="Profilbild för {fullName}"
                    />

                    <label for="pfp-upload" class="upload-label">
                        {uploading ? "Laddar upp..." : "Byt profilbild"}
                    </label>

                    <input
                        type="file"
                        id="pfp-upload"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        hidden
                        on:change={handlePfpUpload}
                        disabled={uploading}
                    />
                </figure>

                {#if uploadMessage}
                    <p
                        class:success={uploadSuccess}
                        class:error={!uploadSuccess}
                    >
                        {uploadMessage}
                    </p>
                {/if}

                <div class="basic-info">
                    <h2 class="name">{fullName}</h2>
                    <p class="role">{user.role || "Medlem"}</p>

                    <nav class="profile-actions" aria-label="Profile actions">
                        <button type="button">Redigera profil</button>
                        <button
                            type="button"
                            on:click={() =>
                                (showPasswordForm = !showPasswordForm)}
                        >
                            Byt lösenord
                        </button>
                    </nav>

                    {#if showPasswordForm}
                        <div
                            class="password-form"
                            style="margin-top:12px; text-align:left; width:100%;"
                        >
                            <label>
                                Nuvarande lösenord
                                <input
                                    type="password"
                                    bind:value={currentPassword}
                                    style="width:100%; margin-top:6px; padding:8px; border-radius:6px; border:1px solid #ddd;"
                                />
                            </label>

                            <label style="display:block; margin-top:8px;">
                                Nytt lösenord
                                <input
                                    type="password"
                                    bind:value={newPassword}
                                    style="width:100%; margin-top:6px; padding:8px; border-radius:6px; border:1px solid #ddd;"
                                />
                            </label>

                            <label style="display:block; margin-top:8px;">
                                Upprepa nytt lösenord
                                <input
                                    type="password"
                                    bind:value={confirmPassword}
                                    style="width:100%; margin-top:6px; padding:8px; border-radius:6px; border:1px solid #ddd;"
                                />
                            </label>

                            <div
                                style="margin-top:10px; display:flex; gap:8px; align-items:center;"
                            >
                                <button
                                    on:click={changePassword}
                                    disabled={changing ||
                                        newPassword !== confirmPassword ||
                                        newPassword.length < 6}
                                >
                                    {changing ? "Sparar..." : "Byt lösenord"}
                                </button>

                                <button
                                    type="button"
                                    on:click={() => {
                                        showPasswordForm = false;
                                        currentPassword =
                                            newPassword =
                                            confirmPassword =
                                                "";
                                    }}
                                >
                                    Avbryt
                                </button>

                                {#if changeMessage}
                                    <span
                                        style="margin-left:12px; color: {changeMessage.includes(
                                            'fel',
                                        )
                                            ? 'red'
                                            : 'green'};"
                                    >
                                        {changeMessage}
                                    </span>
                                {/if}
                            </div>
                        </div>
                    {/if}
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
                    </dl>
                </section>

                <section class="other-panel">
                    <h3>Om mig</h3>

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

    .profile-card {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: var(--gap);
    }

    .profile-left {
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
        text-align: center;
    }

    .pfp {
        position: relative;
        width: 140px;
        height: 140px;
    }

    .pfp img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid rgba(0, 0, 0, 0.06);
        display: block;
    }

    .upload-label {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.65);
        color: white;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        cursor: pointer;
        pointer-events: auto;
        white-space: nowrap;
    }

    .upload-label:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    p.success {
        color: #2e7d32;
        font-size: 0.9rem;
        margin-top: 8px;
    }

    p.error {
        color: #c62828;
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .basic-info {
        text-align: center;
    }

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
        justify-content: center;
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
        .pfp {
            width: 84px;
            height: 84px;
        }
        .profile-actions {
            justify-content: flex-start;
            margin-left: auto;
        }
    }
</style>
