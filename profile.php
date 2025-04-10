<?php
require_once 'config.php';
requireLogin();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $new_password = !empty($_POST['new_password']) ? $_POST['new_password'] : null;
    $confirm_password = $_POST['confirm_password'];
    
    // Проверка совпадения паролей если меняем
    if ($new_password && $new_password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    if (empty($errors)) {
        $result = updateProfile($_SESSION['user_id'], $name, $email, $phone, $new_password);
        
        if ($result === true) {
            $success = true;
        } else {
            $errors = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Профиль пользователя</h1>
        
        <?php if ($success): ?>
            <div class="success">Данные успешно обновлены!</div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_SESSION['user_name']) ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_SESSION['user_email']) ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($_SESSION['user_phone']) ?>">
            </div>
            
            <div class="form-group">
                <label for="new_password">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Повторите новый пароль:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            
            <button type="submit">Обновить данные</button>
        </form>
        
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>