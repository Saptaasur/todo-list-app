<?php
// Include PHPMailer and Exception classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require '../../vendor/autoload.php'; // Ensure PHPMailer and dotenv are installed via Composer

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Start session
session_start();

// Initialize error message
$error_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    // Retrieve form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Database connection
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=todo_db', 'root', ''); // Update credentials as necessary
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $error_message = 'Email is already registered.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Generate a unique verification token
            $verification_token = bin2hex(random_bytes(16));

            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (:username, :email, :password, :verification_token)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':verification_token', $verification_token, PDO::PARAM_STR);
            $stmt->execute();

            // Send the verification email
            $mail = new PHPMailer(true);

            try {
                // SMTP configuration using environment variables
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USERNAME']; // Your Gmail address
                $mail->Password = $_ENV['SMTP_PASSWORD']; // Your Gmail password or app-specific password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS encryption
                $mail->Port = 587; // TCP port to connect to

                // Email details
                $mail->setFrom('no-reply@yourdomain.com', 'Todo-list-app');
                $mail->addAddress($email); // Add the recipient's email address

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $verification_link = "http://localhost/todo-list-app/backend/views/verify.php?token=" . $verification_token;
                $mail->Body = "Please click the link below to verify your email address:<br><a href='" . $verification_link . "'>Verify Email</a>";

                $mail->send();
                echo 'A verification link has been sent to your email. Please verify your email to log in.';
            } catch (Exception $e) {
                echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
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
    <title>Sign Up</title>
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
    width: 90%;
    max-width: 400px; /* Adjusted max-width for larger screens */
    text-align: center;
}

input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Ensures padding and border are included in element's total width and height */
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
        width: 95%;
        max-width: 100%;
    }
    
    button {
        font-size: 18px; /* Larger font size for better accessibility */
    }

    input[type="text"], input[type="email"], input[type="password"] {
        font-size: 16px; /* Larger font size for better readability */
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
            
            <input type="hidden" name="action" value="register">
            <button type="submit">Sign Up</button>
        </form>
        <a href="login.php">Login</a>
    </div>
</body>
</html>
