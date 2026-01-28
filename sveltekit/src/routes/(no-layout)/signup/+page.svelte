<script lang="ts">
    import { goto } from "$app/navigation";

    // Formdata
    let firstname = "";
    let surname = "";
    let username = "";
    let email = "";
    let password = "";
    let loading = false;
    let message = "";
    let messageType = ""; // 'success' eller 'error'

    // Hanterar registrering
    async function handleSignup(e: SubmitEvent) {
        e.preventDefault();
        loading = true;
        message = "";

        try {
            // Skapa FormData för att skicka till servern
            const formData = new FormData();
            formData.append("firstname", firstname);
            formData.append("surname", surname);
            formData.append("username", username);
            formData.append("password", password);
            formData.append("email", email);

            // Skicka POST-request till PHP API:et
            const response = await fetch("http://localhost/api/adduser.php", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                messageType = "success";
                message = data.message;
                // Rensa formulären
                firstname = "";
                surname = "";
                username = "";
                email = "";
                password = "";
                // Gå till login eller main efter 2 sekunder
                setTimeout(() => {
                    goto("/");
                }, 2000);
            } else {
                messageType = "error";
                message = data.message;
            }
        } catch (error) {
            messageType = "error";
            message =
                "Ett fel uppstod: " +
                (error instanceof Error ? error.message : String(error));
        } finally {
            loading = false;
        }
    }
</script>

<main>
    <div class="login-container">
        <h2>Registrering</h2>

        {#if message}
            <div class="message {messageType}">
                {message}
            </div>
        {/if}

        <form onsubmit={handleSignup}>
            <input
                type="text"
                placeholder="Förnamn"
                bind:value={firstname}
                required
            />
            <input
                type="text"
                placeholder="Efternamn"
                bind:value={surname}
                required
            />
            <input
                type="text"
                placeholder="Användarnamn"
                bind:value={username}
                required
            />
            <input
                type="text"
                placeholder="Email"
                bind:value={email}
                required
            />
            <input
                type="password"
                placeholder="Lösenord (min 6 tecken)"
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
        height: 100vh;
        width: 100vw;
        margin: 0;
        background-image: url("/assets/backgrund9.png");
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
        overflow: hidden;
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
