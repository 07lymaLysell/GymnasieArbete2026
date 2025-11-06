<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</head>
<body>
    <main>
        <div class="main-container">
            <div class="side-meny"></div>
        </div>
    </main>
</body>

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
    .side-meny {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 8vw;
        margin: 0;
        background-color: grey;
        border-right: solid 2px black;
    }
</style>
