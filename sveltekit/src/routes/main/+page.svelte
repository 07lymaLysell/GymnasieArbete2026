<script>
    // Svelte-friendly: bind elements and use reactive state instead of document.querySelector
    let hamActive = false;
    function toggleMenu() {
        hamActive = !hamActive;
    }
</script>

<svelte:head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</svelte:head>

<main>
    <nav class="main-container">
        <!-- Combined side menu and hamburger -->
        <div class="side-meny" class:active={hamActive}>
            <!-- Hamburger menu moved inside side-meny -->
            <div
                class="ham-menu"
                on:click={toggleMenu}
                class:active={hamActive}
                aria-label="Toggle menu"
                role="button"
                tabindex="0"
            >
                <span></span>
                <span></span>
                <span></span>
            </div>

            <ul>
                <li><a class="active">Home</a></li>
                <li><a href="">flow</a></li>
                <li><a href="">messages</a></li>
                <li><a href="">friends</a></li>
                <li><a href="">account</a></li>
            </ul>
        </div>
    </nav>
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

    .main-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 95vh;
        width: 90vw;
        margin: 0;
        background-color: white;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
        overflow: hidden;
        border: solid 2px black;
        border-radius: 40px;
    }

    /* Side menu (left) */
    .side-meny {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 8vw;
        margin: 0;
        background-color: grey;
        border-right: solid 2px black;
        transition: 0.3s ease;
    }

    /* Optional: when hamburger toggles, the class exists for future animations */
    .side-meny.active {
        /* no visual change by default to preserve look; place for animations if desired */
    }

    .side-meny a {
        text-decoration: none;
    }
    .side-meny ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .side-meny a.active {
        transform: scale(1.2);
    }

    .ham-menu {
        display: block; /* Changed from none to always show */
        height: 50px;
        width: 50px;
        margin: 10px; /* Changed from margin: 10px auto */
        position: relative;
        cursor: pointer;
        z-index: 1000; /* Ensure it's always on top */
    }

    .ham-menu span {
        height: 5px;
        width: 100%;
        background-color: white; /* Changed from blue for better visibility */
        border-radius: 25px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        transition: 0.3s ease;
    }

    .ham-menu span:nth-child(1) {
        top: 25%;
    }
    .ham-menu span:nth-child(3) {
        top: 75%;
    }

    /* Animated state for hamburger (keeps current intended behavior) */
    .ham-menu.active span:nth-child(1) {
        top: 50%;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .ham-menu.active span:nth-child(2) {
        opacity: 0;
    }

    .ham-menu.active span:nth-child(3) {
        top: 50%;
        transform: translate(-50%, 50%) rotate(-45deg);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .side-meny {
            width: 60px;
            transform: translateX(-100%);
            z-index: 999; /* Added to ensure proper layering */
        }

        /* Remove display: block here since we're showing it by default now */
        .ham-menu {
            position: fixed; /* Changed to fixed */
            left: 10px; /* Position from left edge */
            top: 10px; /* Position from top edge */
        }

        .side-meny ul {
            opacity: 0;
            transition: opacity 0.3s ease;
            padding-top: 60px; /* Space for hamburger */
        }

        .side-meny.active ul {
            opacity: 1;
        }

        .side-meny a {
            padding: 15px 20px;
            display: block;
            color: white;
            font-size: 1.1em;
        }
    }
</style>
