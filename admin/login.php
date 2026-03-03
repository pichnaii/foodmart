<?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";     
    $dbname = "foodmart";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $encryp_password = md5($password);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("SELECT id, user_role FROM users WHERE username = ? AND password = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ss", $username, $encryp_password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_role);
            $stmt->fetch();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = $user_role;
            $_SESSION['username'] = $username;
            $_SESSION['lastname'] = $lastname;

            $_SESSION['message'] = $username;
            $_SESSION['message_type'] = 'success';

            header('Location: index.php');
            exit();
        } else {
            $_SESSION['message'] = 'Invalid username or password!';
            $_SESSION['message_type'] = 'danger';

            header('Location: login.php');
            exit();
        }
        $stmt->close();
    }

    $config_page = $conn->query('SELECT * FROM configurations')->fetch_assoc();

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "include/header.php"?>
<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        :root {
            --body-background: <?php echo ($config_page['theme_color'] == 1) ? '#F3F3F3' : '#757575'; ?> !important;
            --body-content: <?php echo ($config_page['theme_color'] == 1) ? '#FFFFFF' : '#343434'; ?> !important;
            --body-icon: <?php echo ($config_page['theme_color'] == 1) ? '#009cff' : '#343434'; ?> !important;
            --body-btn: <?php echo ($config_page['theme_color'] == 1) ? '#009cff' : '#757575'; ?> !important;
            --body-btn-hover: <?php echo ($config_page['theme_color'] == 1) ? '#0582d2' : '#757575'; ?> !important;
            --body-btn-border: <?php echo ($config_page['theme_color'] == 1) ? '#009cff' : '#FFFFFF'; ?> !important;
            --body-title: <?php echo ($config_page['theme_color'] == 1) ? '#333333' : '#FFFFFF'; ?> !important;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var( --body-background);
            padding: 30px;
        }
        .container {
            position: relative;
            max-width: 850px;
            width: 100%;
            background: var( --body-content);
            padding: 40px 30px;
            border-radius: 9px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            perspective: 2700px;
        }
        .container .cover {
            position: absolute;
            top: 0;
            left: 50%;
            height: 100%;
            width: 50%;
            z-index: 98;
            transition: all 1s ease;
            transform-origin: left;
            transform-style: preserve-3d;
            backface-visibility: hidden;
        }
        .container #flip:checked ~ .cover {
            transform: rotateY(-180deg);
        }
        .container #flip:checked ~ .forms .login-form {
            pointer-events: none;
        }
        .container .cover .front{
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }
        .container .cover img {
            position: absolute;
            height: 100%;
            width: 100%;
            object-fit: cover;
            z-index: 10;
        }
        .container .cover .text::before {
            content: '';
            position: absolute;
            height: 100%;
            width: 100%;
            opacity: 0.5;
            background: #009cff;
        }
        .container .forms {
            height: 100%;
            width: 100%;
            background: #fff;
        }
        .container .form-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var( --body-content);
        }
        .form-content .login-form,
        .form-content .signup-form {
            width: calc(100% / 2 - 25px);
        }
        .forms .form-content .title {
            position: relative;
            font-size: 24px;
            font-weight: 500;
            color: var(--body-title);
        }
        .forms .form-content .title:before {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 25px;
            background: #009cff;
        }
        .forms .signup-form .title:before {
            width: 20px;
        }
        .forms .form-content .input-boxes {
            margin-top: 30px;
        }
        .forms .form-content .input-box {
            display: flex;
            align-items: center;
            height: 50px;
            width: 100%;
            margin: 10px 0;
            position: relative;
        }
        .form-content .input-box input {
            height: 100%;
            width: 100%;
            outline: none;
            border: none;
            padding: 0 30px;
            font-size: 16px;
            font-weight: 500;
            background: var(--body-background);
            color: var(--body-title);
            border-radius: 5px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        .form-content .input-box input:focus,
        .form-content .input-box input:valid {
            border-color: #009cff;
        }
        .form-content .input-box i {
            position: absolute;
            color: var(--body-icon);
            font-size: 17px;
        }
        .forms .form-content .text {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }
        .forms .form-content .text a {
            text-decoration: none;
        }
        .forms .form-content .text a:hover {
            text-decoration: underline;
        }
        .forms .form-content .button {
            color: #fff;
            margin-top: 40px;
        }
        .forms .form-content .button input {
            color: #fff;
            background: #009cff;
            border-radius: 6px;
            padding: 0;
            cursor: pointer;
            transition: all 0.4s ease;
        }
        .forms .form-content .button input:hover {
            background: #009cff;
        }
        .forms .form-content label {
            color: #5b13b9;
            cursor: pointer;
        }
        .forms .form-content label:hover {
            text-decoration: underline;
        }
        .forms .form-content .login-text,
        .forms .form-content .sign-up-text {
            text-align: center;
            margin-top: 25px;
        }
        .container #flip {
            display: none;
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #757575;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 5px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-primary {
            color: #000;
            background-color: var(--body-btn);
            border-color: var(--body-btn-border);
        }
        .btn-primary:hover {
            color: #fff;
            background-color: var(--body-btn-hover);
            border-color: var(--body-btn-border);
        }
        @media (max-width: 730px) {
            .container .cover {
                display: none;
            }
            .form-content .login-form,
            .form-content .signup-form {
                width: 100%;
            }
            .form-content .signup-form {
                display: none;
            }
            .container #flip:checked ~ .forms .signup-form {
                display: block;
            }
            .container #flip:checked ~ .forms .login-form {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cover">
            <div class="front">
                <img src="../admin/img/Logo.png" alt="Naii">
            </div>
        </div>
        <div class="forms">
            <div class="form-content">
                <div class="login-form">
                    <div class="title text-uppercase">Login</div>
                    <div class="input-boxes">
                        <?php if(isset($_SESSION['message'])){?>
                            <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show mt-4" role="alert">
                                <?php echo $_SESSION['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                        <?php } ?>
                        <form action="login.php" method="post">
                            <div class="input-box">
                                <i class="fas fa-user px-2"></i>
                                <input type="text" name="username" placeholder="Enter your username" value="super_admin" required>
                            </div>
                            <div class="input-box">
                                <i class="fas fa-lock px-2"></i>
                                <input type="password" name="password" placeholder="Enter your password" value="1234" required>
                            </div>
                            <div class="button input-box">
                                <button type="submit" name="login" class="btn btn-primary py-3 w-100 mb-4">Log In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>