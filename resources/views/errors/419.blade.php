<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>419 - Session Expired</title>

    <!-- Lottie Player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00b894, #0984e3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 14px;
            text-align: center;
            max-width: 440px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            font-size: 80px;
            margin: 0;
            color: #0984e3;
        }

        h2 {
            margin: 10px 0;
            font-weight: 600;
            color: #333;
        }

        p {
            font-size: 15px;
            color: #666;
            line-height: 1.6;
        }

        .countdown {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

        .progress {
            width: 100%;
            height: 6px;
            background: #eaeaea;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 15px;
        }

        .progress-bar {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #00b894, #0984e3);
            transition: width 1s linear;
        }

        a {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 26px;
            background: #0984e3;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        a:hover {
            background: #0652dd;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 65px;
            }
            .error-container {
                padding: 30px 22px;
            }
        }
    </style>
</head>

<body>

<div class="error-container">

    <!-- ⏳ Session Expired Lottie Animation -->
    <lottie-player
        src="https://assets10.lottiefiles.com/packages/lf20_jcikwtux.json"
        background="transparent"
        speed="1"
        style="width: 220px; height: 220px; margin: auto;"
        loop
        autoplay>
    </lottie-player>

    <h1>419</h1>
    <h2>Session Expired</h2>

    <p>
        Your session has expired due to inactivity or an invalid security token.
        Please log in again to continue.
    </p>

    <div class="countdown">
        Redirecting to login in <strong><span id="seconds">5</span></strong> seconds
    </div>

    <div class="progress">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <a href="{{ route('login') }}">Go to Login Now</a>

</div>

<script>
    let seconds = 5;
    const secondsEl = document.getElementById('seconds');
    const progressBar = document.getElementById('progressBar');

    const timer = setInterval(() => {
        seconds--;
        secondsEl.textContent = seconds;
        progressBar.style.width = (seconds / 5) * 100 + '%';

        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = "{{ route('login') }}";
        }
    }, 1000);
</script>

</body>
</html>
