<?php
require_once __DIR__ . "/../functions/auth.php";

if (isLoggedIn()) {
    header('Location: /dean_office/index.php');
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    [$success, $message] = loginUser($username, $password);
    
    if ($success) {
        header('Location: /dean_office/index.php');
        exit();
    }
}
echo <<< _PAGE
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Пожалуйста войдите</title>
</head>
<body style="background: #2A385C;">
    <div class="page d-flex flex-column" id="page">
        <div class="header" id="header">
            <div class="container d-flex justify-content-center">
                <div class="item">
                    <a href="/dean_office" class="logo">
                        <img src="../assets/img/logo.svg" alt="mpgu-logo">
                    </a>
                </div>
            </div>
        </div>
        <div class="login" id="login">
            <div class="container d-flex justify-content-center">
                <form action="" method="POST" class="login-form d-flex flex-column">
                    <label for="username">Логин:</label>
                    <input type="text" id="username" name="username" required autofocus>

                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit">Войти</button>
                    <span id="login-err">$message</span>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
_PAGE;
?>