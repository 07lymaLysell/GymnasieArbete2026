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

    let position = writable({ x: 30, y: 93 }); // Adjusted start: (30%, 93%)
    let isAnimating = false;
    let angle = -90; // Start at bottom of circle

    function moveShipCircular() {
        if (!isAnimating) {
            isAnimating = true;
            const radius = 20; // 20% of screen width
            const centerX = 50; // Screen center (50%)
            const centerY = 50; // Screen center (50%)
            const rotations = 3; // 3 rotations
            let phase = 0; // 0: approach, 1: circle, 2: return
            let startX = get(position).x; // 30%
            let startY = get(position).y; // 93%

            function animate() {
                if (phase === 0) {
                    // Move toward entry point (50%, 69%)
                    position.update((pos) => ({
                        x: pos.x + 0.5, // Move 20% in ~40 frames
                        y: pos.y - 0.6, // Move from 93% to 69% (24% in ~40 frames)
                    }));

                    if (get(position).x >= 50) {
                        phase = 1;
                        // Ensure exact entry point
                        position.set({ x: 50, y: 69 });
                    }
                } else if (phase === 1) {
                    // Circular motion: 3 rotations
                    angle -= 3;
                    const newX =
                        centerX + radius * Math.cos((angle * Math.PI) / 180);
                    const newY =
                        centerY + radius * Math.sin((angle * Math.PI) / 180);
                    position.update(() => ({ x: newX, y: newY }));

                    if (angle <= -90 - 360 * rotations) {
                        phase = 2;
                        // Ensure exact exit point
                        position.set({ x: 50, y: 69 });
                    }
                } else if (phase === 2) {
                    // Return to start (30%, 93%)
                    position.update((pos) => ({
                        x: pos.x - 0.5,
                        y: pos.y + 0.6,
                    }));

                    if (get(position).x <= 30) {
                        isAnimating = false;
                        angle = -90; // Reset angle
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
