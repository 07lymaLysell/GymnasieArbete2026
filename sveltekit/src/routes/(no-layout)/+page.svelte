<script lang="ts">
    import { goto } from "$app/navigation";
    import { loginUser } from "$lib/stores/auth";

    let username = $state("");
    let password = $state("");
    let errorMessage = $state("");
    let isLoading = $state(false);

    // Funktion som navigerar till signup-sidan när knappen trycks
    function goToSignup() {
        goto("/signup");
    }

    // Hover-flagga för att byta knapptext ("Don't have an account?" -> "Click here!")
    let isHovered = $state(false);
    function handleMouseOver() {
        isHovered = true;
    }
    function handleMouseOut() {
        isHovered = false;
    }

    async function handleLogin(e: any) {
        e.preventDefault();
        errorMessage = "";
        isLoading = true;

        try {
            const response = await fetch("http://localhost/api/auth.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    username: username,
                    password: password,
                }),
            });

            const text = await response.text();
            console.log("Raw server response:", text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("JSON parse failed. Raw response was:", text);
                errorMessage = "Server returned invalid response (not JSON)";
                return;
            }
            if (data.success) {
                loginUser(data.user);
                await goto("/account");
            } else {
                errorMessage = data.message || "Login failed";
            }
        } catch (error) {
            errorMessage = "Network error. Please try again.";
            console.error("Login error:", error);
        } finally {
            isLoading = false;
        }
    }
</script>

<svelte:head>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background: #000; /* fallback – prevents white flash */
        }
    </style>
</svelte:head>

<main>
    <!-- login-container: elementet runt formuläret som skeppet ska cirkulera kring -->
    <div class="login-container">
        <h2>Login</h2>
        <form action="">
            <input
                type="text"
                bind:value={username}
                placeholder="Username"
                required
            />
            <input
                type="password"
                bind:value={password}
                placeholder="Password"
                required
            />

            <!-- Knapp som navigerar till signup; byter text vid hover -->
            {#if !isHovered}
                <button
                    type="button"
                    onmouseover={handleMouseOver}
                    onmouseout={handleMouseOut}
                    onfocus={handleMouseOver}
                    onblur={handleMouseOut}
                    onclick={goToSignup}
                    style="background:none;border:none;padding:0;margin:0;color:inherit;cursor:pointer;user-select:none"
                    aria-label="Sign up">Don't have an account?</button
                >
            {:else}
                <button
                    type="button"
                    onmouseover={handleMouseOver}
                    onmouseout={handleMouseOut}
                    onfocus={handleMouseOver}
                    onblur={handleMouseOut}
                    onclick={goToSignup}
                    style="background:none;border:none;padding:0;margin:0;cursor:pointer;user-select:none;color:blue;text-decoration:underline"
                    aria-label="Sign up">Click here!</button
                >
            {/if}

            <!-- Login-knapp som triggar skeppets animation; preventDefault så sidan inte skickas -->
            <button type="submit" onclick={handleLogin} disabled={isLoading}>
                {isLoading ? "Logging in..." : "Login"}
            </button>

            {#if errorMessage}
                <p style="color: red; margin-top: 10px;">{errorMessage}</p>
            {/if}
        </form>
    </div>

    <!-- Rymdskeppsbild: positioneras via % left/top från position-store -->
</main>

<style>
    /* Main: helskärmsbakgrund och containrar */
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

    /* Login-container: semi-transparent ruta centralt i main */
    .login-container {
        background: rgba(255, 255, 255, 0.2);
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
    }
    h2 {
        color: white;
    }
    input {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: calc(100% - 22px);
    }
    button {
        width: 100%;
        padding: 10px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    button:hover {
        background-color: red;
        scale: 1.03;
        transition: ease-in-out 0.2s;
    }
</style>
