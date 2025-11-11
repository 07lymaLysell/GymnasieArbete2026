<script>
    // Svelte-friendly: bind elements and use reactive state instead of document.querySelector
    let hamActive = $state(false);
    function toggleMenu() {
        hamActive = !hamActive;
    }

    let showMenu = $state(false);
</script>

<main>
    <nav class="main-container">
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div
            class="side-meny"
            class:active={hamActive}
            onclick={() => (showMenu = !showMenu)}
        >
            <div
                class="ham-menu"
                onclick={toggleMenu}
                class:active={hamActive}
                aria-label="Toggle menu"
                role="button"
                tabindex="0"
            >
                <span></span>
                <span></span>
                <span></span>
            </div>
            {#if showMenu}
                <ul>
                    <li><button>Home</button></li>
                    <li><button>flow</button></li>
                    <li><button>messages</button></li>
                    <li><button>friends</button></li>
                    <li><button>account</button></li>
                </ul>
            {/if}
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
        background-image: url("/assets/backgrund9.png");
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
        background-color: white;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
        overflow: hidden;
        border: solid 2px black;
        border-radius: 40px;
    }

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
    .side-meny.active {
        width: 18vw;
    }
    .side-meny ul {
        position: absolute;
        top: 90px; /* place list below the hamburger */
        left: 0;
        right: 0;
        flex-direction: column;
        gap: 12px; /* space between buttons */
        align-items: center;
        padding: 8px 10px;
        margin: 0;
        list-style: none;
        box-sizing: border-box;
    }
    .side-meny ul button {
        width: calc(100% - 20px); /* inset from edges */
        max-width: 200px;
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        background: rgba(255, 255, 255, 0.12);
        color: white;
        cursor: pointer;
        text-align: center;
    }
    .ham-menu span:nth-child {
        margin: 0;
    }

    .ham-menu {
        display: block; /* Changed from none to always show */
        height: 50px;
        width: 50px;
        margin-top: 20px; /* Changed from margin: 10px auto */
        position: fixed;
        cursor: pointer;
        z-index: 1000; /*  alltid on top */
    }

    .ham-menu span {
        height: 5px;
        width: 80%;
        background-color: white;
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

    /* positionering av span högsta upp  */
    .ham-menu.active span:nth-child(1) {
        top: 60%;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    .ham-menu.active span:nth-child(2) {
        opacity: 0;
    }

    .ham-menu.active span:nth-child(3) {
        top: 50%;
        transform: translate(-50%, 50%) rotate(-45deg);
    }

    @media (max-width: 76px) {
        .side-meny {
            width: 60px;
            transform: translateX(-100%);
            z-index: 999;
        }

        .ham-menu {
            position: fixed;
            left: 10px;
            top: 10px;
        }

        .side-meny ul {
            opacity: 0;
            transition: opacity 0.3s ease;
            padding-top: 60px;
        }

        .side-meny.active ul {
            opacity: 1;
        }
    }
</style>
