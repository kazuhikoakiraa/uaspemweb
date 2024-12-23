<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        const username = document.querySelector("input[name='username']");
        const email = document.querySelector("input[name='email']");
        const password = document.querySelector("input[name='password']");
        const confirmPassword = document.querySelector("input[name='confirm_password']");
        const fullName = document.querySelector("input[name='full_name']");
        const phone = document.querySelector("input[name='phone']");
        const dob = document.querySelector("input[name='dob']");
        const address = document.querySelector("textarea[name='address']");
        const gender = document.querySelector("select[name='gender']");

        function validateField(field, message) {
            if (!field.value.trim()) {
                alert(message);
                field.focus();
                return false;
            }
            return true;
        }

        form.addEventListener("submit", function(event) {
            if (!validateField(username, "Username is required") ||
                !validateField(email, "Email is required") ||
                !validateField(password, "Password is required") ||
                !validateField(confirmPassword, "Confirm Password is required") ||
                password.value !== confirmPassword.value ||
                !validateField(fullName, "Full Name is required") ||
                !validateField(phone, "Phone Number is required") ||
                !validateField(dob, "Date of Birth is required") ||
                !validateField(address, "Address is required") ||
                !validateField(gender, "Gender is required")) {
                event.preventDefault();
            }
        });
    });
    </script>
</head>

<body>
    <div class="container">
        <h2>Register</h2>
        <?php
session_start();
if (isset($_SESSION['register_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
    unset($_SESSION['register_error']);
}
?>
        <form action="proses.php" method="POST">
            <!-- Action untuk memproses register -->
            <input type="hidden" name="action" value="register">

            <!-- Username -->
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Username" required>

            <!-- Email -->
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <!-- Password -->
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <!-- Confirm Password -->
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"
                required>

            <!-- Full Name -->
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" placeholder="Full Name" required>

            <!-- Phone -->
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>

            <!-- Date of Birth -->
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required>

            <!-- Address -->
            <label for="address">Address</label>
            <textarea id="address" name="address" placeholder="Address" rows="3" required></textarea>

            <!-- Gender -->
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>

            <!-- Submit -->
            <button type="submit">Register</button>

            <!-- Login -->
            <a href="login.php">Jika sudah memiliki akun, login di sini.</a>
        </form>

    </div>

</body>

</html>