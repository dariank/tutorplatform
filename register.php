<?php
// register.php
require 'config/db.php';
require 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Проверка на пустоту
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля.';
    } 
    // 2. Валидация Email (простая проверка через filter_var)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Пожалуйста, введите корректный Email (например, name@mail.ru).';
    }
    // 3. Валидация Телефона (проверяем, что это цифры, пробелы, скобки или дефисы, и длина от 10 до 18 символов)
    elseif (!preg_match('/^[0-9\+\-\s\(\)]{10,18}$/', $phone)) {
        $error = 'Пожалуйста, введите корректный номер телефона (не менее 10 цифр).';
    }
    // 4. Проверка длины пароля
    elseif (strlen($password) < 3) {
        $error = 'Пароль должен быть не менее 3 символов.';
    } 
    else {
        // Проверяем, нет ли уже такого email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким email уже существует.';
        } else {
            // Создаем нового пользователя (добавили поле phone в запрос)
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, phone, role, full_name) VALUES (?, ?, ?, 'student', ?)");
            
            if ($stmt->execute([$email, $password, $phone, $name])) {
                $success = 'Регистрация прошла успешно! Теперь вы можете <a href="login.php" style="color: var(--primary-color); font-weight: bold; text-decoration: underline;">войти на сайт</a>.';
            } else {
                $error = 'Что-то пошло не так. Попробуйте позже.';
            }
        }
    }
}
?>

<div style="max-width: 400px; margin: 50px auto; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
    <h2 style="text-align: center; margin-bottom: 20px;">Регистрация</h2>
    
    <?php if ($error): ?>
        <div style="background: #ffdddd; color: #d8000c; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
            <?= $success ?>
        </div>
    <?php else: ?>
        <form method="POST" action="register.php">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Ваше имя</label>
                <input type="text" name="name" required placeholder="Иван Иванов" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Email</label>
                <input type="email" name="email" required placeholder="you@mail.ru" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>

            <!-- Новое поле: Телефон -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Телефон</label>
                <input type="text" name="phone" required placeholder="+7 (999) 123-45-67" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Пароль</label>
                <input type="password" name="password" required placeholder="Минимум 3 символа" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>
            <button type="submit" style="width: 100%; background: var(--primary-color); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.3s;">
                Зарегистрироваться
            </button>
        </form>
    <?php endif; ?>
    
    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        Уже есть аккаунт? <a href="login.php" style="color: var(--primary-color); text-decoration: none;">Войти</a>
    </p>
</div>

<?php require 'includes/footer.php'; ?>