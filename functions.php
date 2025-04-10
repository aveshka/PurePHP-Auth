<?php
require_once 'config.php';

// Регистрация нового пользователя
function registerUser($name, $email, $phone, $password, $confirm_password) {
    global $pdo;
    
    $errors = [];
    
    // Проверка совпадения паролей
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    // Проверка уникальности email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким email уже существует";
    }
    
    // Проверка уникальности телефона
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким телефоном уже существует";
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashed_password]);
        return true;
    }
    
    return $errors;
}

// Авторизация пользователя
function loginUser($login, $password, $captcha_token) {
    global $pdo;
    
    // Проверка капчи
    if (!verifyCaptcha($captcha_token)) {
        return ["Неверная капча"];
    }
    
    // Поиск пользователя по email или телефону
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        return true;
    }
    
    return ["Неверный логин или пароль"];
}

// Проверка Yandex SmartCaptcha
function verifyCaptcha($token) {
    if (empty($token)) return false;
    
    $data = [
        'secret' => CAPTCHA_SERVER_KEY,
        'token' => $token,
        'ip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents('https://smartcaptcha.yandexcloud.net/validate', false, $context);
    $response = json_decode($result);
    
    return $response && $response->status === 'ok';
}

// Обновление профиля пользователя
function updateProfile($user_id, $name, $email, $phone, $new_password = null) {
    global $pdo;
    
    $errors = [];
    
    // Проверка уникальности email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким email уже существует";
    }
    
    // Проверка уникальности телефона
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
    $stmt->execute([$phone, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = "Пользователь с таким телефоном уже существует";
    }
    
    if (empty($errors)) {
        if ($new_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $hashed_password, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $user_id]);
        }
        
        // Обновляем данные в сессии
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = $phone;
        
        return true;
    }
    
    return $errors;
}
?>