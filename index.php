<?php
require_once 'utils/config.php';

if (isLoggedIn()) {
    header("Location: pages/profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать!</h1>
        <p><a href="pages/login.php">Войти</a> или <a href="pages/register.php">зарегистрироваться</a></p>
    </div>
</body>
</html>