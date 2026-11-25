<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eval CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Popperjs -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <!-- Tempus Dominus JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js" crossorigin="anonymous"></script>
    <!-- Tempus Dominus Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css" crossorigin="anonymous">

    <link href="https://use.fontawesome.com/releases/v6.2.0/css/all.css" rel="stylesheet">

    <!--script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script-->

    {if (!empty($template_file))}
        {include file="$template_file" template_type="head"}
    {/if}

    <style>
        main {
            padding: 0 0 0 0.5rem;
        }

        input[type="text"], select, textarea {
            border: solid #BBB 1px;
            border-radius: 4px;
            padding: 2px 8px;

            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .form-signin {
            max-width: 330px;
            padding: 1rem;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .text-small {
            font-size: 85%;
        }

        .dropdown-toggle:not(:focus) {
            outline: 0;
        }

        .rem-2 {
            width: 2rem;
        }

        .rem-6 {
            width: 6rem;
        }

        .rem-4 {
            width: 4rem;
        }

        .rem-8 {
            width: 8rem;
        }

        .rem-10 {
            width: 10rem;
        }

        .rem-12 {
            width: 12rem;
        }

        .rem-14 {
            width: 14rem;
        }

        .rem-16 {
            width: 16rem;
        }

        .rem-18 {
            width: 18rem;
        }

        .rem-20 {
            width: 20rem;
        }

        .loading {
            /*ローディング画面の縦横幅を画面いっぱいになるように指定*/
            width: 100vw;
            height: 100vh;
            /*ローディング画面の表示位置を固定*/
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            /*ローディング画面を0.5秒かけて非表示にする*/
            transition: all 0.2s linear;
        }

        /*ローディング画面を非表示にする*/
        .loading.loaded {
            /*0.5秒かけてopacityを0にする*/
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s linear;
        }

        .loading-text {
            color: #FFF;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        .spinner {
            display: block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 4px solid #FFF;
            border-left-color: #1082ce;
            /* アニメーションを1秒かけて実行 */
            animation: spinner-rotation 0.5s linear infinite;
        }

        td {
            border-left: 1px solid #EEE; /* 左側に実線 */
        }

        /* アニメーションの設定 */
        @keyframes spinner-rotation {
            0% {
                transform: rotate(0);
            }
            100% {
                transform: rotate(360deg);
            }
        }

    </style>

</head>