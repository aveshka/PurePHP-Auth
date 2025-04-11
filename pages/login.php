<?php
require __DIR__ . '/../utils/config.php';
require __DIR__ . '/../utils/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $captcha_token = $_POST['smart-token'];
    
    $result = loginUser($login, $password, $captcha_token,$captchaServerKey);
    
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
    <link rel="stylesheet" href="../assets/styles.css">
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
                <input type="text" id="login" name="login" required value="<?= isset($_POST['login']) ? 
                htmlspecialchars($_POST['login']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div id="captcha-container" class="smart-captcha" data-sitekey="<?= $captchaClientKey?>"></div>
            
            <button type="submit">Войти</button>
        </form>
        
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>