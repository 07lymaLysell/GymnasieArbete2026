<script>
    import { get, writable } from "svelte/store";
    import { goto } from "$app/navigation";
    function goToSignup() {
        goto("/signup");
    }

    let isHovered = false;
    function handleMouseOver() {
        isHovered = true;
    }
    function handleMouseOut() {
        isHovered = false;
    }

    let position = writable({ x: 30, y: 93 });
    let isAnimating = false;
    let angle = 0;

    function moveShipCircular() {
        if (!isAnimating) {
            isAnimating = true;
            const radius = 25;
            const centerX = 33;
            const centerY = 49;
            let phase = 0;

            let currentAngle = Math.atan2(
                get(position).y - centerY,
                get(position).x - centerX,
            );

            function animate() {
                if (phase === 0) {
                    const entryX = centerX + radius;
                    const entryY = centerY;
                    const currentPos = get(position);
                    const dx = entryX - currentPos.x;
                    const dy = entryY - currentPos.y;

                    position.update((pos) => ({
                        x: pos.x + dx * 0.05,
                        y: pos.y + dy * 0.05,
                    }));

                    if (Math.abs(dx) < 0.2 && Math.abs(dy) < 0.2) {
                        currentAngle = 0;
                        phase = 1;
                    }
                } else if (phase === 1) {
                    currentAngle -= 0.05;
                    const newX = centerX + radius * Math.cos(currentAngle);
                    const newY = centerY + radius * Math.sin(currentAngle);

                    position.update(() => ({ x: newX, y: newY }));
                    if (currentAngle <= -100 * Math.PI) {
                        phase = 2;
                    }
                } else if (phase === 2) {
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
        transition: all 0.016s ease;
        z-index: 0;
        pointer-events: none;
    }
</style>
