<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        form.addEventListener("submit", function(event) {
            const rememberMe = document.querySelector("input[name='remember_me']");
            if (rememberMe.checked) {
                localStorage.setItem("remember_me", "true");
            } else {
                localStorage.removeItem("remember_me");
            }
        });

        // Cek apakah ada parameter 'error' di URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            const error = urlParams.get('error');
            if (error === '1') {
                alert('Login gagal. Username atau password salah.');
            }
        }
    });
    </script>
</head>

<body>
    <div class="container">
        <h2>Login</h2>
        <?php
session_start();
if (isset($_SESSION['login_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
    unset($_SESSION['login_error']);
}
if (isset($_SESSION['register_success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
    unset($_SESSION['register_success']);
}
?>
        <form action="proses.php" method="POST">
            <input type="hidden" name="action" value="login">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <div class="checkbox">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me">Remember Me</label>
            </div>

            <button type="submit">Login</button>
            <a href="register.php">Belum punya akun? Register</a>
        </form>
    </div>
</body>

</html>