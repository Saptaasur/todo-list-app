<?php
// Include the necessary files and initialize dotenv if needed
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require '../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Start session
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Database connection
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=todo_db', 'root', ''); // Update credentials as necessary
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verify the token
        $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = :token");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Token is valid, update user to set email as verified
            $stmt = $pdo->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE verification_token = :token");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();

            echo 'Your email has been verified. You can now log in.';
        } else {
            echo 'Invalid or expired verification link.';
        }
    } catch (PDOException $e) {
        echo 'Database error: ' . $e->getMessage();
    }
} else {
    echo 'Verification token is missing.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
            width: 300px;
            text-align: center;
        }
        p {
            margin: 0;
            color: #333;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Email Verification</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php else: ?>
            <p>Your email has been successfully verified.</p>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
