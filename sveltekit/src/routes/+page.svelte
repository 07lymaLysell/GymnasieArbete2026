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

    let position = writable({ x: -13, y: 93 });
    let isAnimating = false;
    let angle = 0;

    function moveShipCircular() {
        if (!isAnimating) {
            isAnimating = true;
            const radius = 200;
            const centerX = 50;
            const centerY = 50;
            let phase = 0; // 0: moving towards container, 1: circular motion, 2: return home
            let startX = get(position).x;
            let startY = get(position).y;

            function animate() {
                if (phase === 0) {
                    //  towards login container
                    position.update((pos) => ({
                        x: pos.x + 2,
                        y: pos.y - 0.5,
                    }));

                    if (get(position).x > 30) {
                        phase = 1;
                    }
                } else if (phase === 1) {
                    angle -= 3;
                    const newX =
                        centerX +
                        (radius / 2) * Math.cos((angle * Math.PI) / 180);
                    const newY =
                        centerY +
                        (radius / 2) * Math.sin((angle * Math.PI) / 180);
                    position.update(() => ({ x: newX, y: newY }));

                    if (angle < -360) {
                        // Changed to negative for counter-clockwise
                        phase = 2;
                    }
                } else if (phase === 2) {
                    // Return to starting position
                    position.update((pos) => ({
                        x: pos.x - 2,
                        y: pos.y + 0.5,
                    }));

                    if (get(position).x < -13) {
                        isAnimating = false;
                        angle = 0;
                        position.set({ x: -13, y: 93 }); // Reset to original position
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
        background: rgba(
            255,
            255,
            255,
            0.2
        ); /* Semi-transparent to match parent */
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
    }
    h2 {
        color: white; /* Solid color for readability */
    }
    input {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: calc(100% - 22px); /* Adjust for padding and border */
    }
    button {
        width: 100%;
        padding: 10px;
        background-color: #4caf50; /* Opaque background */
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
        color: black; /* Solid color for readability */
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
        transition: all 1s ease;
        z-index: 0;
        pointer-events: none; /* Add this to prevent the spaceship from interfering with clicks */
    }
</style>
