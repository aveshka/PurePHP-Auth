<?php
require_once 'config.php';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $captcha_token = $_POST['smart-token'];
    
    $result = loginUser($login, $password, $captcha_token);
    
    if ($result === true) {
        header("Location: profile.php");
        exit();
    } else {
        $errors = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Авторизация</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="login">Email или телефон:</label>
                <input type="text" id="login" name="login" required value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div id="captcha-container" class="smart-captcha" data-sitekey="<?= CAPTCHA_CLIENT_KEY ?>"></div>
            
            <button type="submit">Войти</button>
        </form>
        
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>