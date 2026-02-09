<script>
    import { get, writable } from "svelte/store";
    import { goto } from "$app/navigation";

    // Funktion som navigerar till signup-sidan när knappen trycks
    function goToSignup() {
        goto("/signup");
    }

    // Hover-flagga för att byta knapptext ("Don't have an account?" -> "Click here!")
    let isHovered = false;
    function handleMouseOver() {
        isHovered = true;
    }
    function handleMouseOut() {
        isHovered = false;
    }
</script>

<main>
    <!-- login-container: elementet runt formuläret som skeppet ska cirkulera kring -->
    <div class="login-container">
        <h2>Login</h2>
        <form action="">
            <input
                type="text"
                name="username"
                placeholder="Username"
                required
            />
            <input
                type="password"
                name="password"
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
            <button type="submit">Login</button>
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
    #signupRe {
        display: block;
        color: black;
        cursor: pointer;
        font-size: 13px;
        margin-bottom: 10px;
        text-decoration: none;
        border-radius: 3px;
    }
    #signupRe:hover {
        color: blue;
        text-decoration: underline;
    }

    /* Spaceship: absolut position, uppdateras varje frame via JS.
       OBS: CSS-transition kan orsaka "buffring" när JS också flyttar left/top.
       För maximal mjukhet rekommenderas `transition: none;` och transform: translate(-50%,-50%)
       men jag ändrar inte din logik här — detta är bara en notering. */
    .spaceship {
        position: absolute;
        width: 150px;
        transition: all 0.016s ease;
        z-index: 0;
        pointer-events: none;
    }
</style>
