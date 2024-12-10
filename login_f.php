<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($pass, $row['password'])) {
            $_SESSION['student_id'] = $row['student_id'];
            $_SESSION['user_type'] = $row['user_type'];
            
            // Redirect based on user type
            if ($row['user_type'] == 'admin') {
                header('location:admin_dashboard.php');
            } else {
                header('location:student_dashboard.php');
            }
            exit();
        } else {
            $error[] = 'Incorrect Email or Password! Please Try Again.';
        }
    } else {
        $error[] = 'User not found! Please Try Again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 30px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h3 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: #ffffff;
            font-size: 14px;
        }

        .input-field:focus {
            outline: none;
            border-color: #ff0000;
            background: rgba(255, 255, 255, 0.2);
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to bottom, #000000 0%, #800000 40%, #ff0000 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .form-btn:hover {
            background: linear-gradient(to bottom, #1a0000 0%, #990000 40%, #ff1a1a 100%);
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .forgot-password {
            color: #00a67d;
            text-decoration: none;
            font-size: 13px;
            display: block;
            text-align: left;
            margin-bottom: 15px;
        }

        .register-link {
            text-align: center;
            color: #666;
            font-size: 13px;
            margin-top: 20px;
        }

        .register-link a {
            color: #00a67d;
            text-decoration: none;
        }

        .error-msg {
            background: rgba(255, 255, 255, 0.1);
            color: #ff4d4d;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
        }

        /* Hide unnecessary elements */
        .options-row {
            display: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3>Login</h3>
        <form action="" method="post">
            <?php
            if(isset($error)){
                foreach($error as $err){
                    echo '<div class="error-msg">'.$err.'</div>';
                }
            }
            ?>
            
            <div class="input-group">
                <input type="email" name="email" placeholder="Enter your email" required class="input-field">
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Enter your password" required class="input-field">
            </div>
            <input type="submit" name="submit" value="Login" class="form-btn">
            
            <div class="register-link">
                Don't have an account? <a href="register_f.php">Sign up</a>
            </div>
        </form>
    </div>
</body>
</html>
