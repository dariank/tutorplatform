<?php
// login.php
require 'config/db.php';
require 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Заполните все поля.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Сравниваем пароль как обычный текст (без password_verify)
        if ($user && $password === $user['password_hash']) {
            if ($user['is_blocked']) {
                $error = 'Ваш аккаунт заблокирован.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];
                
                                // Перенаправляем в зависимости от роли
                if ($user['role'] === 'admin') {
                    header('Location: admin_panel.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            }
        } else {
            $error = 'Неверный email или пароль.';
        }
    }
}
?>

<div style="max-width: 400px; margin: 50px auto; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
    <h2 style="text-align: center; margin-bottom: 20px;">Вход в систему</h2>
    
    <?php if ($error): ?>
        <div style="background: #ffdddd; color: #d8000c; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Email</label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;" value="student1@tutor.local">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; color: var(--text-muted);">Пароль</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;" value="123">
        </div>
        <button type="submit" style="width: 100%; background: var(--primary-color); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold;">
            Войти
        </button>
    </form>
    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
    </p>
</div>

<?php require 'includes/footer.php'; ?>