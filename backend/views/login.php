<?php
session_start();

// Initialize error message
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=todo_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute query
        $stmt = $pdo->prepare("SELECT id, username, password, verified FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['verified']) {
                    // User is verified, proceed with login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username']; // Store username in session
                    header('Location: welcome.php');
                    exit;
                } else {
                    $error_message = 'Please verify your email before logging in.';
                }
            } else {
                $error_message = 'Invalid email or password.';
            }
        } else {
            $error_message = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 90%; /* Use percentage for width to be fluid */
    max-width: 400px; /* Set a maximum width for larger screens */
    text-align: center;
    box-sizing: border-box; /* Include padding and border in element's width */
}

input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Include padding and border in element's width */
}

button {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px; /* Adjust font size for better readability */
}

button:hover {
    background-color: #45a049;
}

a {
    display: block;
    margin-top: 15px;
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Responsive styles */
@media (max-width: 600px) {
    .container {
        width: 95%; /* Adjust width for smaller screens */
        max-width: 100%; /* Allow full width for very small screens */
    }

    button {
        font-size: 18px; /* Larger font size for better accessibility on small screens */
    }

    input[type="email"], input[type="password"] {
        font-size: 16px; /* Larger font size for better readability on small screens */
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
            
            <input type="hidden" name="action" value="login">
            <button type="submit">Login</button>
        </form>
        <a href="signup.php">Sign Up</a>
    </div>
</body>
</html>
