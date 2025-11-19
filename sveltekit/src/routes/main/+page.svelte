<script>
    import { goto } from "$app/navigation";

    let hamActive = false;

    function toggleMenu() {
        hamActive = !hamActive;
    }

    function gotoAccount() {
        goto("/account");
    }
    function gotoFriends() {
        goto("/friends");
    }
    function gotoFlow() {
        goto("/flow");
    }
    function gotoMessages() {
        goto("/messages");
    }
</script>

<!-- Full-screen background -->
<div class="background"></div>

<!-- Centered card containing sidebar + main content -->
<div class="app-card">
    <!-- Grey sidebar -->
    <nav class="sidebar" class:expanded={hamActive}>
        <!-- Hamburger (mobile only) -->
        <div
            class="ham-menu"
            class:active={hamActive}
            onclick={toggleMenu}
            onkeypress={(e) => e.key === "Enter" && toggleMenu()}
            role="button"
            tabindex="0"
            aria-label="Toggle menu"
        >
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Icon menu (desktop) -->
        <div class="huvud-menu">
            <ul>
                <li>
                    <button
                        ><img
                            src="/assets/home_button.png"
                            alt="Home"
                        /></button
                    >
                </li>
                <li>
                    <button
                        ><img
                            src="/assets/exploore.jpg"
                            alt="Explore"
                        /></button
                    >
                </li>
                <li>
                    <button
                        ><img
                            src="/assets/message.svg"
                            alt="Messages"
                        /></button
                    >
                </li>
                <li>
                    <button
                        ><img src="/assets/friends.png" alt="Friends" /></button
                    >
                </li>
                <li>
                    <button onclick={gotoAccount}>
                        <img
                            src="/assets/account-removebg-preview.png"
                            alt="Account"
                        />
                    </button>
                </li>
            </ul>
        </div>

        <!-- Mobile text menu (appears when hamburger is open) -->
        {#if hamActive}
            <ul class="mobile-menu">
                <li><button>Home</button></li>
                <li><button onclick={gotoFlow}>Flow</button></li>
                <li><button onclick={gotoMessages}>Messages</button></li>
                <li><button onclick={gotoFriends}>Friends</button></li>
                <li><button onclick={gotoAccount}>Account</button></li>
            </ul>
        {/if}
    </nav>

    <!-- Main white content area -->
    <main class="main-content">
        <h1>Welcome!</h1>
        <p>
            Your app content goes here. The background is visible all around the
            card.
        </p>
        <!-- Add your real page content here -->
    </main>
</div>

<style>
    /* Full background */
    .background {
        position: fixed;
        inset: 0;
        background: url("/assets/backgrund9.png") center/cover no-repeat;
        z-index: -1;
    }

    /* The centered "card" that holds everything */
    .app-card {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90vw;
        max-width: 1200px;
        height: 90vh;
        max-height: 800px;
        background: white;
        border-radius: 40px;
        border: 3px solid black;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        display: flex;
    }

    /* Grey sidebar */
    .sidebar {
        width: 90px;
        background: #2d2d2d;
        border-right: 2px solid black;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 30px;
        transition: width 0.3s ease;
        position: relative;
    }

    /* Expand sidebar on mobile when menu is open */
    .sidebar.expanded {
        width: 240px;
    }

    /* Hamburger */
    .ham-menu {
        display: none;
        width: 50px;
        height: 50px;
        cursor: pointer;
        position: relative;
        margin-bottom: 40px;
    }
    .ham-menu span {
        background: white;
        height: 5px;
        width: 35px;
        border-radius: 10px;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        transition: 0.3s ease;
    }
    .ham-menu span:nth-child(1) {
        top: 12px;
    }
    .ham-menu span:nth-child(2) {
        top: 22px;
    }
    .ham-menu span:nth-child(3) {
        top: 32px;
    }
    .ham-menu.active span:nth-child(1) {
        transform: translateX(-50%) rotate(45deg);
        top: 22px;
    }
    .ham-menu.active span:nth-child(2) {
        opacity: 0;
    }
    .ham-menu.active span:nth-child(3) {
        transform: translateX(-50%) rotate(-45deg);
        top: 22px;
    }

    /* Desktop icon menu */
    .huvud-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 35px;
    }
    .huvud-menu button {
        background: none;
        border: none;
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .huvud-menu button:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.15);
    }
    .huvud-menu img {
        width: 42px;
        height: 42px;
        object-fit: contain;
    }

    /* Mobile text menu */
    .mobile-menu {
        list-style: none;
        padding: 0;
        width: 100%;
        margin-top: 20px;
    }
    .mobile-menu button {
        width: 100%;
        padding: 16px 20px 16px 30px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        text-align: left;
        font-size: 1.1rem;
        cursor: pointer;
    }

    /* Main content area */
    .main-content {
        flex: 1;
        padding: 40px;
        overflow-y: auto;
        background: white;
        border-radius: 0 37px 37px 0; /* matches outer card */
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 767px) {
        .app-card {
            width: 100vw;
            height: 100vh;
            max-width: none;
            max-height: none;
            border-radius: 0;
            border: none;
            top: 0;
            left: 0;
            transform: none;
        }

        .ham-menu {
            display: block;
        }
        .huvud-menu {
            display: none;
        }
    }

    @media (min-width: 768px) {
        .mobile-menu {
            display: none !important;
        }
        .ham-menu {
            display: none;
        }
    }
</style>
