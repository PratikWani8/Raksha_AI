<?php
include("../config/db.php"); 

if (isset($_POST['login'])) {

    //  Cloudflare Turnstile verification
    $secretKey = "0x4AAAAAACg_Y0W0WeAgRQ5XUYDw94iunH0";
    $token = $_POST['cf-turnstile-response'] ?? '';

    if (empty($token)) {
        echo "<script>alert('Please verify you are human');</script>";
        exit();
    }

    $verify = file_get_contents("https://challenges.cloudflare.com/turnstile/v0/siteverify", false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded",
            'content' => http_build_query([
                'secret' => $secretKey,
                'response' => $token
            ])
        ]
    ]));

    $responseData = json_decode($verify);

    if (!$responseData->success) {
        echo "<script>alert('Human verification failed');</script>";
        exit();
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<script>alert('All fields are required');</script>";
    }

    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
    }

    else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {

            $user = $res->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];

                header("Location: ../user/dashboard.php");
                exit();

            } else {
                echo "<script>alert('Invalid password');</script>";
            }

        } else {
            echo "<script>alert('Email not registered');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login - Raksha</title>
    <link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <!-- Firebase -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "",
            authDomain: "",
            projectId: "",
            appId: ""
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        window.googleLogin = function () {
            signInWithPopup(auth, provider)
            .then((result) => {
                const user = result.user;

                fetch("firebase_login.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        name: user.displayName,
                        email: user.email
                    })
                })
                .then(res => res.text())
                .then(() => {
                    window.location.href = "../user/dashboard.php";
                });
            })
            .catch((error) => {
                alert("Google Login Failed");
                console.error(error);
            });
        };
    </script>

    <style>
    .hero-container {
        text-align: center;
        color: black;
        padding: 10px 0;
        font-family: 'Poppins', sans-serif;
    }

    .google-btn {
        width: 100%;
        margin-top: 10px;
        background: #fff;
        color: #444;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 500;
    }

    .google-btn:hover {
        background: #f7f7f7;
    }

    .google-icon {
        width: 18px;
        height: 18px;
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 15px 0;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #ccc;
    }

    .divider span {
        padding: 0 10px;
        font-size: 14px;
        color: #666;
    }
    </style>
</head>

<body>

<header>
    <div class="header-container">
        <h2>🔐 User Login</h2>
    </div>
</header>

<div class="card">
    <div class="hero-container">
        <h2>Welcome Back!</h2>
        <p>Login to access your account and stay protected with Raksha.</p>
    </div>

    <form method="post" autocomplete="off">

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <!--  Turnstile -->
        <div class="cf-turnstile" data-sitekey="0x4AAAAAACg_Y_w7k6DVmCcq"></div>

        <button type="submit" name="login">Login</button>

        <div class="divider">
            <span>OR Login in with</span>
        </div>

        <!-- Google Login -->
        <button type="button" class="google-btn" onclick="googleLogin()">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="google-icon">
            Sign in with Google
        </button>

        <p style="text-align:center; margin-top:15px;">
            New user? <a href="register.php" style="text-decoration: none;">Register here</a>
        </p>

    </form>

</div>

<script>
document.querySelector("form").addEventListener("submit", () => {
    const btn = document.querySelector("button");
    btn.innerText = "Logging in...";
    btn.style.opacity = "0.8";
});
</script>

</body>
</html>