<?php
// dashboard.php
require 'config/db.php';
require 'includes/header.php';

// Если не вошел — кидаем на вход
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

// Получаем актуальные данные пользователя из базы ДО обработки формы
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

// --- ОБРАБОТКА РЕДАКТИРОВАНИЯ ПРОФИЛЯ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['full_name'] ?? '');
    $new_phone = trim($_POST['phone'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $avatar_path = null;

    // Проверяем, не занята ли почта другим пользователем
    if ($new_email !== $current_user['email']) {
        $check_email = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->execute([$new_email, $user_id]);
        if ($check_email->fetch()) {
            header('Location: dashboard.php?error=email_busy');
            exit;
        }
    }

    // Проверка загрузки аватарки
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['avatar']['type'], $allowed)) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $destination = 'uploads/' . $filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                $avatar_path = $destination;
            }
        }
    }

    // Обновляем данные в базе (добавили email)
    if ($avatar_path) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, email = ?, avatar_url = ? WHERE id = ?");
        $stmt->execute([$new_name, $new_phone, $new_email, $avatar_path, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->execute([$new_name, $new_phone, $new_email, $user_id]);
    }

    // Обновляем имя в сессии
    $_SESSION['user_name'] = $new_name;
    header('Location: dashboard.php?success=1');
    exit;
}

// Получаем бронирования
if ($role === 'student') {
    $stmt = $pdo->prepare("
        SELECT b.*, s.start_time, s.end_time, u.full_name as tutor_name, tp.price_per_hour
        FROM bookings b
        JOIN slots s ON s.id = b.slot_id
        JOIN users u ON u.id = b.tutor_id
        JOIN tutor_profiles tp ON tp.user_id = b.tutor_id
        WHERE b.student_id = ? ORDER BY s.start_time DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT b.*, s.start_time, s.end_time, u.full_name as student_name, tp.price_per_hour
        FROM bookings b
        JOIN slots s ON s.id = b.slot_id
        JOIN users u ON u.id = b.student_id
        JOIN tutor_profiles tp ON tp.user_id = b.tutor_id
        WHERE b.tutor_id = ? ORDER BY s.start_time DESC
    ");
}
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>

<!-- Приветственная карточка -->
<div style="background: var(--white); padding: 30px 40px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    
    <!-- Блок с текстом -->
    <div style="display: flex; flex-direction: column; justify-content: center;">
        <h2 style="margin: 0 0 5px 0; color: var(--text-main); font-size: 26px; line-height: 1.2;">Привет, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
        <p style="color: var(--text-muted); font-size: 15px; margin: 0; line-height: 1.4;">
            <?= $role === 'student' ? 'Здесь вы можете управлять своими занятиями.' : 'Здесь вы можете видеть записи ваших учеников.' ?>
        </p>
    </div>

    <!-- Блок с кнопками -->
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <!-- Кнопка Изменить данные -->
        <button onclick="document.getElementById('profileModal').style.display='flex'" style="background: var(--bg-color); color: var(--text-main); border: 1px solid #ccc; padding: 0 20px; height: 44px; border-radius: 8px; font-weight: bold; font-size: 15px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s;" onmouseover="this.style.background='#e0e0e0'" onmouseout="this.style.background='var(--bg-color)'">
            <i class="bi bi-gear" style="font-size: 18px;"></i> Изменить данные
        </button>

        <!-- Кнопка Найти репетитора -->
        <?php if ($role === 'student'): ?>
            <a href="tutors.php" style="background: var(--primary-color); color: white; padding: 0 20px; height: 44px; border-radius: 8px; font-weight: bold; font-size: 15px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; border: 1px solid transparent;" onmouseover="this.style.background='var(--primary-hover)'" onmouseout="this.style.background='var(--primary-color)'">
                Найти репетитора
            </a>
        <?php endif; ?>
    </div>
</div>


<!-- Мои записи -->
<div style="max-width: 800px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Мои записи</h2>
    
    <?php if (count($bookings) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($bookings as $b): ?>
                <div class="tutor-card" style="border-left: 5px solid <?= $b['status'] === 'confirmed' ? '#28a745' : ($b['status'] === 'pending' ? '#ffc107' : '#6c757d') ?>; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h4 style="margin-bottom: 10px; font-size: 18px;">
                                <?= $role === 'student' ? 'Репетитор: ' . htmlspecialchars($b['tutor_name']) : 'Ученик: ' . htmlspecialchars($b['student_name']) ?>
                            </h4>
                            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 5px; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-calendar3" style="color: var(--primary-color);"></i> <?= date('d.m.Y', strtotime($b['start_time'])) ?> 
                                <span style="color: #ccc;">|</span> 
                                <i class="bi bi-clock" style="color: var(--primary-color);"></i> <?= date('H:i', strtotime($b['start_time'])) ?> - <?= date('H:i', strtotime($b['end_time'])) ?>
                            </p>
                            <p style="font-weight: bold; color: var(--primary-color); margin-bottom: 15px;">
                                К оплате: <?= $b['price_per_hour'] ?> ₽
                            </p>
                        </div>
                        <div>
                            <?php if ($b['status'] === 'pending'): ?>
                                <span style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 5px; font-size: 13px;">Ожидает</span>
                            <?php elseif ($b['status'] === 'confirmed'): ?>
                                <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 5px; font-size: 13px;">Подтверждено</span>
                            <?php elseif ($b['status'] === 'completed'): ?>
                                <span style="background: #e2e3e5; color: #383d41; padding: 5px 10px; border-radius: 5px; font-size: 13px;">Завершено</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid #eee; padding-top: 15px; text-align: right;">
                        <?php if (strtotime($b['start_time']) > time() && $b['status'] !== 'cancelled'): ?>
                            <form method="POST" action="cancel_booking.php" onsubmit="return confirm('Вы уверены, что хотите отменить занятие?');" style="display: inline-block;">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" style="background: #ffdddd; color: #d8000c; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px;">Отменить</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: var(--white); padding: 40px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 40px; margin-bottom: 15px;">📭</div>
            <h3 style="margin-bottom: 10px; color: var(--text-main);">У вас пока нет записей</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Самое время найти своего первого репетитора!</p>
            <a href="tutors.php" style="background: var(--primary-color); color: white; padding: 10px 25px; border-radius: 8px; font-weight: bold; text-decoration: none;">Перейти в каталог</a>
        </div>
    <?php endif; ?>
</div>

<!-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ -->
<div id="profileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--white); padding: 40px; border-radius: var(--border-radius); width: 100%; max-width: 450px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        
        <!-- Кнопка закрытия (крестик) -->
        <button onclick="document.getElementById('profileModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
        
        <h2 style="margin-bottom: 20px; text-align: center;">Редактирование профиля</h2>

        <!-- Аватарка -->
        <div style="text-align: center; margin-bottom: 20px;">
            <?php if (!empty($current_user['avatar_url'])): ?>
                <img src="<?= htmlspecialchars($current_user['avatar_url']) ?>" alt="Аватар" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 10px;">
            <?php else: ?>
                <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--bg-color); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; color: var(--text-muted);">
                    <i class="bi bi-person" style="font-size: 50px;"></i>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" action="dashboard.php" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--text-muted);">Имя</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($current_user['full_name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>
            
            <!-- ДОБАВИЛИ ПОЛЕ EMAIL -->
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--text-muted);">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($current_user['email']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--text-muted);">Телефон</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($current_user['phone'] ?? '') ?>" placeholder="+7 (999) 123-45-67" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--text-muted);">Фотография</label>
                <input type="file" name="avatar" accept="image/*" style="width: 100%; font-size: 14px;">
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">JPG, PNG или GIF.</p>
            </div>

            <button type="submit" name="update_profile" style="width: 100%; background: var(--primary-color); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Сохранить изменения
            </button>
            
            <?php if (isset($_GET['success'])): ?>
                <p style="color: #28a745; margin-top: 10px; font-size: 14px; text-align: center;">✅ Данные успешно обновлены!</p>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'email_busy'): ?>
                <p style="color: #d8000c; margin-top: 10px; font-size: 14px; text-align: center;">Этот email уже занят другим пользователем!</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php require 'includes/footer.php'; ?>