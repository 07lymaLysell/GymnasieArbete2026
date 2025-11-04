<script>
    import { get, writable } from "svelte/store";
    let isHovered = false;
    function handleMouseOver() {
        isHovered = true;
    }
    function handleMouseOut() {
        isHovered = false;
    }
    import "../routes/global.css";

    let position = writable({ x: 30, y: 93 });
    let isAnimating = false;
    let angle = 0; // Changed initial angle

    function moveShipCircular() {
        if (!isAnimating) {
            isAnimating = true;
            const radius = 20;
            const centerX = 30;
            const centerY = 45;
            let phase = 0;

            // Calculate initial angle based on starting position
            let currentAngle = Math.atan2(
                get(position).y - centerY,
                get(position).x - centerX,
            );

            function animate() {
                if (phase === 0) {
                    // Move to the entry point of the circle smoothly
                    const entryX = centerX + radius; // Start from right side of circle
                    const entryY = centerY;

                    const currentPos = get(position);
                    const dx = entryX - currentPos.x;
                    const dy = entryY - currentPos.y;

                    position.update((pos) => ({
                        x: pos.x + dx * 0.05,
                        y: pos.y + dy * 0.05,
                    }));

                    // When close enough to entry point, start rotation
                    if (Math.abs(dx) < 0.5 && Math.abs(dy) < 0.5) {
                        currentAngle = 0; // Start from 0 degrees
                        phase = 1;
                    }
                } else if (phase === 1) {
                    // Circular motion
                    currentAngle -= 0.05; // Slower rotation speed
                    const newX = centerX + radius * Math.cos(currentAngle);
                    const newY = centerY + radius * Math.sin(currentAngle);

                    position.update(() => ({ x: newX, y: newY }));
                    //varje 2 är ett fullt varv och 1 är ett halvt varv
                    if (currentAngle <= -100 * Math.PI) {
                        phase = 2;
                    }
                } else if (phase === 2) {
                    // Return to start
                    const currentPos = get(position);
                    const dx = 30 - currentPos.x;
                    const dy = 93 - currentPos.y;

                    position.update((pos) => ({
                        x: pos.x + dx * 0.05,
                        y: pos.y + dy * 0.05,
                    }));

                    if (Math.abs(dx) < 0.5 && Math.abs(dy) < 0.5) {
                        isAnimating = false;
                        position.set({ x: 30, y: 93 });
                        return;
                    }
                }

                requestAnimationFrame(animate);
            }
            animate();
        }
    }
</script>

<main>
    <div class="login-container">
        <h2>Sign in</h2>
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
            {#if !isHovered}
                <!-- svelte-ignore a11y_mouse_events_have_key_events -->
                <a
                    href="/signup"
                    id="signupRe"
                    on:mouseover={handleMouseOver}
                    on:mouseout={handleMouseOut}
                    style="cursor:pointer; user-select:none"
                    >Don't have an account?</a
                >
            {:else}
                <!-- svelte-ignore a11y_mouse_events_have_key_events -->
                <a
                    href="/signup"
                    id="signupRe"
                    on:mouseover={handleMouseOver}
                    on:mouseout={handleMouseOut}
                    style="cursor:pointer; user-select:none; color:blue; text-decoration:underline"
                    >Click here!</a
                >
            {/if}
            <button type="submit" on:click|preventDefault={moveShipCircular}
                >Login</button
            >
        </form>
    </div>
    <img
        src="/src/lib/assets/rymdfartyg.png"
        alt=""
        class="spaceship"
        style="left: {$position.x}%;top:{$position.y}%;"
    />
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
        background-image: url("/src/lib/assets/backgrund9.png");
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
    .spaceship {
        position: absolute;
        width: 150px;
        transition: all 0.016s ease; /* Adjusted for ~60 FPS (1/60s) */
        z-index: 0;
        pointer-events: none;
    }
</style>
