<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Получаем аватарку текущего пользователя для шапки (если он вошел)
$header_avatar = null;
if (isset($_SESSION['user_id']) && isset($pdo)) {
    // Проверяем, есть ли подключение к базе (чтобы не было ошибки, если файл подключен до db.php)
    try {
        $stmt_av = $pdo->prepare("SELECT avatar_url FROM users WHERE id = ?");
        $stmt_av->execute([$_SESSION['user_id']]);
        $header_user = $stmt_av->fetch();
        if ($header_user && $header_user['avatar_url']) {
            $header_avatar = $header_user['avatar_url'];
        }
    } catch (Exception $e) {
        // Игнорируем ошибку, если база еще не подключена
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Платформа поиска репетиторов</title>
    <!-- Подключаем стили -->
    <link rel="stylesheet" href="/css/style.css">
    <!-- Подключаем иконки -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
</head>
<body>

<header class="header">
    <div class="header-container">
        <a href="index.php" class="logo">TutorPlatform</a>
        <nav class="nav-links">
            <a href="index.php">Главная</a>
            <a href="tutors.php">Репетиторы</a>
            <a href="contacts.php">О нас / Контакты</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Если пользователь залогинен, проверяем его роль для ссылки -->
                <a href="<?= ($_SESSION['user_role'] === 'admin') ? 'admin_panel.php' : 'dashboard.php' ?>" style="display: inline-flex; align-items: center; gap: 8px;">
                    <?php if (!empty($header_avatar)): ?>
                        <img src="<?= htmlspecialchars($header_avatar) ?>" alt="Аватар" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                    <?php endif; ?>
                    Кабинет (<?= htmlspecialchars($_SESSION['user_name']) ?>)
                </a>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <!-- Если гость -->
                <a href="login.php">Вход</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">