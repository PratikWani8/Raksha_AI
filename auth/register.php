<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {

    //  Cloudflare Turnstile verification
    $secretKey = "secret-key";
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

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $pass  = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($phone) || empty($pass) || empty($cpass)) {
        echo "<script>alert('All fields are required');</script>";
    }

    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address');</script>";
    }

    else if (strlen($phone) !== 10) {
        echo "<script>alert('Phone number must be 10 digits');</script>";
    }

    else if (strlen($pass) < 6) {
        echo "<script>alert('Password must be at least 6 characters');</script>";
    }

    else if ($pass !== $cpass) {
        echo "<script>alert('Passwords do not match');</script>";
    }

    else {

        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            echo "<script>alert('Email already registered');</script>";
            $check->close();

        } else {

            $check->close();

            $password = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users (name, email, phone, password)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param("ssss", $name, $email, $phone, $password);

            if ($stmt->execute()) {

                $_SESSION['user_id'] = $stmt->insert_id;

                header("Location: ../user/dashboard.php");
                exit();

            } else {

                echo "<script>alert('Registration failed. Try again.');</script>";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration - Raksha</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--  Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <!--  Firebase -->
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

        window.googleSignup = function () {
            signInWithPopup(auth, provider)
            .then((result) => {
                const user = result.user;

                fetch("firebase_register.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        name: user.displayName,
                        email: user.email
                    })
                })
                .then(() => {
                    window.location.href = "../user/dashboard.php";
                });
            })
            .catch((error) => {
                alert("Google Signup Failed");
                console.error(error);
            });
        };
    </script>

    <link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../style.css">

    <style>
    .divider {
        display: flex;
        align-items: center;
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

    .google-btn {
        width: 100%;
        background: #fff;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
    }
    
    .google-btn:hover {
        background: #f7f7f7;
    }
    
    .google-icon {
        width: 18px;
    }
    </style>
</head>

<body>

<header>
    <div class="header-container">
        <h2>📝 User Registration</h2>
    </div>
</header>

<div class="card">

    <form method="post" autocomplete="off">

        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter full name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email" required>

        <label>Phone</label>
        <input type="text" name="phone" pattern="[0-9]{10}" placeholder="10 digit number" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Min 6 characters" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Re-enter password" required>

        <!-- Turnstile -->
        <div class="cf-turnstile" data-sitekey="site-key"></div>

        <button type="submit" name="register">Register</button>

        <div class="divider">
            <span>OR Sign up with</span>
        </div>

        <!-- Google Signup -->
        <button type="button" class="google-btn" onclick="googleSignup()">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="google-icon">
            Sign up with Google
        </button>

        <p style="text-align:center; margin-top:15px;">
            Already registered?
            <a href="login.php" style="text-decoration:none;">Login</a>
        </p>

    </form>

</div>

</body>
</html>