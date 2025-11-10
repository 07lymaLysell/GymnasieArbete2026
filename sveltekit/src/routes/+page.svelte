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

    // position: skrivbar store med x/y i procent (används i inline style left/top)
    let position = writable({ x: 30, y: 93 });

    // isAnimating: hindrar att animationen startas flera gånger samtidigt
    let isAnimating = false;

    // angle används för cirkelrotation (i din kod uppdateras currentAngle lokalt)
    let angle = 0;

    // Huvudfunktionen som styr skeppets rörelse: approach -> cirkel -> return
    function moveShipCircular() {
        if (!isAnimating) {
            // om ingen animation pågår, starta en
            isAnimating = true;

            // radius och centerX/centerY är just nu hårdkodade procentvärden
            // Dessa bestämmer var cirkeln kommer att vara (ändra för att använda login-container DOM)
            const radius = 25;
            const centerX = 33;
            const centerY = 49;
            let phase = 0; // 0 = närma, 1 = cirkulera, 2 = återvänd

            // beräkna startvinkel utifrån nuvarande position relativt center
            let currentAngle = Math.atan2(
                get(position).y - centerY,
                get(position).x - centerX,
            );

            function animate() {
                if (phase === 0) {
                    // Approach: interpolera position mot en entry-punkt på cirkeln (ingen teleport)
                    const entryX = centerX + radius;
                    const entryY = centerY;
                    const currentPos = get(position);
                    const dx = entryX - currentPos.x;
                    const dy = entryY - currentPos.y;

                    // Easing-rörelse (använd procentuell interpolation för mjukhet)
                    position.update((pos) => ({
                        x: pos.x + dx * 2.0,
                        y: pos.y + dy * 2.0,
                    }));

                    // När vi är nära entry-punkten, byt fas till rotation
                    if (Math.abs(dx) < 0.2 && Math.abs(dy) < 0.2) {
                        // OBS: att sätta currentAngle = 0 kan orsaka hopp om skeppet inte ligger på cirkeln.
                        // Här återställs angle till 0 i din befintliga kod — rekommenderas att
                        // räkna ut angle från den faktiska positionen istället för att nollställa.
                        currentAngle = 0;
                        phase = 1;
                    }
                } else if (phase === 1) {
                    // Rotation: uppdatera vinkel och beräkna ny punkt på cirkeln
                    // currentAngle minskas för counter-clockwise rotation (steg i grader/radianer beroende på användning)
                    currentAngle -= 0.5;
                    const newX = centerX + radius * Math.cos(currentAngle);
                    const newY = centerY + radius * Math.sin(currentAngle);

                    // Sätt position direkt till punkt på cirkeln
                    position.update(() => ({ x: newX, y: newY }));

                    // Avsluta rotation efter viss vinkel (här ett stort värde; bättre att ackumulera rot.antal)
                    if (currentAngle <= -10 * Math.PI) {
                        phase = 2;
                    }
                } else if (phase === 2) {
                    // Return: interpolera tillbaka till startposition med easing
                    const currentPos = get(position);
                    const dx = 30 - currentPos.x;
                    const dy = 93 - currentPos.y;

                    position.update((pos) => ({
                        x: pos.x + dx * 0.05,
                        y: pos.y + dy * 0.05,
                    }));

                    // När vi är nära startposition, avsluta animation och återställ exakt
                    if (Math.abs(dx) < 0.5 && Math.abs(dy) < 0.5) {
                        isAnimating = false;
                        position.set({ x: 30, y: 93 });
                        return;
                    }
                }

                // Kör nästa frame
                requestAnimationFrame(animate);
            }
            animate();
        }
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
                    on:mouseover={handleMouseOver}
                    on:mouseout={handleMouseOut}
                    on:focus={handleMouseOver}
                    on:blur={handleMouseOut}
                    on:click={goToSignup}
                    style="background:none;border:none;padding:0;margin:0;color:inherit;cursor:pointer;user-select:none"
                    aria-label="Sign up">Don't have an account?</button
                >
            {:else}
                <button
                    type="button"
                    on:mouseover={handleMouseOver}
                    on:mouseout={handleMouseOut}
                    on:focus={handleMouseOver}
                    on:blur={handleMouseOut}
                    on:click={goToSignup}
                    style="background:none;border:none;padding:0;margin:0;cursor:pointer;user-select:none;color:blue;text-decoration:underline"
                    aria-label="Sign up">Click here!</button
                >
            {/if}

            <!-- Login-knapp som triggar skeppets animation; preventDefault så sidan inte skickas -->
            <button type="submit" on:click|preventDefault={moveShipCircular}
                >Login</button
            >
        </form>
    </div>

    <!-- Rymdskeppsbild: positioneras via % left/top från position-store -->
    <img
        src="/src/lib/assets/rymdfartyg.png"
        alt=""
        class="spaceship"
        style="left: {$position.x}%;top:{$position.y}%;"
    />
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
        background-image: url("/src/lib/assets/backgrund9.png");
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
