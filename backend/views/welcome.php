<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize error message
$error_message = '';

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=todo_db', 'root', ''); // Update credentials if necessary
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch tasks for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Output the tasks array
    // Uncomment the following line to debug
    // var_dump($tasks);
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            margin-bottom: 1rem;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .task-list {
            margin-top: 1rem;
            list-style-type: none;
            padding: 0;
            text-align: left;
        }
        .task-list li {
            background-color: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
        }
        .task-list h3 {
            margin: 0;
        }
        .task-list p {
            margin: 0;
            color: #555;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        /* Responsive styles */
        @media (max-width: 600px) {
            button {
                width: 100%;
                padding: 12px;
                font-size: 18px;
            }
            .task-list li {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!</h1>
        <a href="dashboard.php"><button>Go to Dashboard</button></a>
        <a href="logout.php"><button>Logout</button></a>

        <?php if (isset($tasks) && count($tasks) > 0): ?>
            <h2>Your Tasks</h2>
            <ul class="task-list">
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars($task['description']); ?></p>
                        <small><?php echo htmlspecialchars($task['due_date']); ?> - <?php echo htmlspecialchars($task['status']); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have no tasks yet. <a href="create_task.php">Create a new task</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
