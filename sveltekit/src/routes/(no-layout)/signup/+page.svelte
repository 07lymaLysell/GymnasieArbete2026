<script lang="ts">
    import { enhance } from "$app/forms";
    import { page } from "$app/stores";

    let firstname = $state("");
    let surname = $state("");
    let username = $state("");
    let email = $state("");
    let password = $state("");
    let loading = $state(false);
</script>

<svelte:head>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* ← removes page-level scrollbar */
            background: #000; /* fallback – prevents white flash */
        }
    </style>
</svelte:head>

<main>
    <div class="login-container">
        <h2>Registrering</h2>

        {#if $page.form?.message}
            <div
                class="message {$page.form.message.includes('redan') ||
                $page.form.message.includes('felaktig')
                    ? 'error'
                    : 'success'}"
            >
                {$page.form.message}
            </div>
        {/if}

        <form
            method="POST"
            use:enhance={() => {
                loading = true;
                return async ({ update }) => {
                    loading = false;
                    await update();
                };
            }}
        >
            <input
                type="text"
                placeholder="Förnamn"
                name="firstname"
                bind:value={firstname}
                required
            />
            <input
                type="text"
                placeholder="Efternamn"
                name="surname"
                bind:value={surname}
                required
            />
            <input
                type="text"
                placeholder="Användarnamn"
                name="username"
                bind:value={username}
                required
            />
            <input
                type="text"
                placeholder="Email"
                name="email"
                bind:value={email}
                required
            />
            <input
                type="password"
                placeholder="Lösenord (min 6 tecken)"
                name="password"
                bind:value={password}
                required
            />

            <button type="submit" disabled={loading}>
                {loading ? "Registrerar..." : "Registrera"}
            </button>
        </form>

        <p class="login-link">
            Har du redan ett konto? <a href="/">Logga in här</a>
        </p>
    </div>
</main>

<style>
    main {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100dvh; /* ← key fix: use dvh instead of vh */
        width: 100vw;
        margin: 0;
        background-image: url("/assets/backgrund9.png");
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
        overflow: hidden; /* ← prevents inner scroll overflow */
    }

    .login-container {
        background: rgba(255, 255, 255, 0.2);
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
    }

    h2 {
        color: white;
        margin-top: 0;
    }

    .message {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 4px;
        font-weight: bold;
    }

    .message.success {
        background-color: #4caf50;
        color: white;
    }

    .message.error {
        background-color: #f44336;
        color: white;
    }

    input {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: calc(100% - 22px);
        box-sizing: border-box;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }

    button:hover:not(:disabled) {
        background-color: #45a049;
        scale: 1.03;
        transition: ease-in-out 0.2s;
    }

    button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }

    .login-link {
        color: white;
        margin-top: 15px;
    }

    .login-link a {
        color: #ffeb3b;
        text-decoration: none;
        font-weight: bold;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>
