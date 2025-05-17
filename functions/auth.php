<?php
require_once __DIR__ . "/db.php";

function registerUser(string $username, string $email, string $password): array {
    $pdo = dbConnect();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->fetch()) {
        return [false, 'Пользователь с таким именем или email уже существует'];
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users(username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $passwordHash]);
    return [true, "Регистрация прошла успешно"];
}

function loginUser(string $username, string $password): array {
    $pdo = dbConnect();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return [false, 'Неверное имя пользователя или пароль'];
    }

    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in'] = true;

    return [true, "Вход выполнен успешно"];
}

function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: /dean_office/sections/login.php');
}

function isLoggedIn(): bool {
    session_start();
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireAuth(): void {
    session_start();
    if (!isLoggedIn()) {
        header('Location: /dean_office/sections/login.php');
        exit;
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }

    $pdo = dbConnect();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    return $stmt->fetch() ?: null;
}
?>