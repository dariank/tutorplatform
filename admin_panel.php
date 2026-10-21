<?php
// admin_panel.php
require 'config/db.php';
require 'includes/header.php';

// Проверка: пускаем только админа
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// --- ОБРАБОТКА СМЕНЫ СТАТУСА ЗАЯВКИ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = $_POST['status'];
    
    if (in_array($new_status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
        header('Location: admin_panel.php?tab=bookings');
        exit;
    }
}

// Получаем статистику
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$tutors_count = $pdo->query("SELECT COUNT(*) FROM tutor_profiles")->fetchColumn();
$bookings_count = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending_count = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();

// Получаем все заявки
$bookings = $pdo->query("
    SELECT b.id, b.status, s.start_time, u_student.full_name as student_name, u_tutor.full_name as tutor_name
    FROM bookings b
    JOIN slots s ON s.id = b.slot_id
    JOIN users u_student ON u_student.id = b.student_id
    JOIN users u_tutor ON u_tutor.id = b.tutor_id
    ORDER BY b.created_at DESC
")->fetchAll();

// Получаем всех репетиторов
$tutors = $pdo->query("
    SELECT tp.id, u.full_name, tp.price_per_hour, tp.city, tp.is_approved
    FROM tutor_profiles tp
    JOIN users u ON u.id = tp.user_id
    ORDER BY u.full_name ASC
")->fetchAll();

$active_tab = $_GET['tab'] ?? 'bookings';
?>

<h1 style="margin-bottom: 30px;">Панель администратора</h1>

<!-- Блок статистики -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div style="background: var(--white); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 36px; font-weight: 800; color: var(--primary-color); margin-bottom: 10px;"><?= $users_count ?></div>
        <div style="color: var(--text-muted); font-size: 14px;">Всего пользователей</div>
    </div>
    <div style="background: var(--white); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 36px; font-weight: 800; color: #28a745; margin-bottom: 10px;"><?= $tutors_count ?></div>
        <div style="color: var(--text-muted); font-size: 14px;">Репетиторов</div>
    </div>
    <div style="background: var(--white); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 36px; font-weight: 800; color: #17a2b8; margin-bottom: 10px;"><?= $bookings_count ?></div>
        <div style="color: var(--text-muted); font-size: 14px;">Всего записей</div>
    </div>
    <div style="background: var(--white); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
        <div style="font-size: 36px; font-weight: 800; color: #ffc107; margin-bottom: 10px;"><?= $pending_count ?></div>
        <div style="color: var(--text-muted); font-size: 14px;">Ожидают подтверждения</div>
    </div>
</div>

<!-- Вкладки (Табы) -->
<div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
    <a href="admin_panel.php?tab=bookings" style="padding: 10px 20px; text-decoration: none; font-weight: bold; font-size: 16px; border-radius: 8px 8px 0 0; <?= $active_tab === 'bookings' ? 'background: var(--primary-color); color: white;' : 'color: var(--text-muted); background: transparent;' ?>">Заявки</a>
    <a href="admin_panel.php?tab=tutors" style="padding: 10px 20px; text-decoration: none; font-weight: bold; font-size: 16px; border-radius: 8px 8px 0 0; <?= $active_tab === 'tutors' ? 'background: var(--primary-color); color: white;' : 'color: var(--text-muted); background: transparent;' ?>">Репетиторы</a>
</div>

<!-- ВКЛАДКА: ЗАЯВКИ -->
<?php if ($active_tab === 'bookings'): ?>
    <div style="background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px; font-size: 22px;">Все заявки</h2>
        
        <?php if (count($bookings) > 0): ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee;">
                        <th style="padding: 10px; color: var(--text-muted);">Ученик</th>
                        <th style="padding: 10px; color: var(--text-muted);">Репетитор</th>
                        <th style="padding: 10px; color: var(--text-muted);">Дата и время</th>
                        <th style="padding: 10px; color: var(--text-muted);">Статус (быстро)</th>
                        <th style="padding: 10px; color: var(--text-muted);">Действие</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px 10px;"><?= htmlspecialchars($b['student_name']) ?></td>
                            <td style="padding: 12px 10px;"><?= htmlspecialchars($b['tutor_name']) ?></td>
                            <td style="padding: 12px 10px;"><?= date('d.m.Y H:i', strtotime($b['start_time'])) ?></td>
                            <td style="padding: 12px 10px;">
                                <form method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <select name="status" style="padding: 5px; border-radius: 5px; border: 1px solid #ccc; font-size: 13px;">
                                        <option value="pending" <?= $b['status'] === 'pending' ? 'selected' : '' ?>>Ожидает</option>
                                        <option value="confirmed" <?= $b['status'] === 'confirmed' ? 'selected' : '' ?>>Подтверждено</option>
                                        <option value="completed" <?= $b['status'] === 'completed' ? 'selected' : '' ?>>Завершено</option>
                                        <option value="cancelled" <?= $b['status'] === 'cancelled' ? 'selected' : '' ?>>Отменено</option>
                                    </select>
                                    <button type="submit" name="change_status" style="background: var(--primary-color); color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 13px;">ОК</button>
                                </form>
                            </td>
                            <td style="padding: 12px 10px;">
                                <a href="admin_edit_booking.php?id=<?= $b['id'] ?>" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">✎ Изменить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--text-muted);">Пока нет ни одной заявки.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- ВКЛАДКА: РЕПЕТИТОРЫ -->
<?php if ($active_tab === 'tutors'): ?>
    <div style="background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px; font-size: 22px;">Управление репетиторами</h2>
                <div style="margin-bottom: 20px;">
            <a href="admin_edit_tutor.php" style="background: #28a745; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">+ Добавить нового репетитора</a>
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #eee;">
                    <th style="padding: 10px; color: var(--text-muted);">Имя</th>
                    <th style="padding: 10px; color: var(--text-muted);">Город</th>
                    <th style="padding: 10px; color: var(--text-muted);">Цена (₽/час)</th>
                    <th style="padding: 10px; color: var(--text-muted);">Статус</th>
                    <th style="padding: 10px; color: var(--text-muted);">Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tutors as $t): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 10px; font-weight: bold;"><?= htmlspecialchars($t['full_name']) ?></td>
                        <td style="padding: 12px 10px;"><?= htmlspecialchars($t['city'] ?? 'Онлайн') ?></td>
                        <td style="padding: 12px 10px;"><?= $t['price_per_hour'] ?></td>
                        <td style="padding: 12px 10px;">
                            <?php if ($t['is_approved']): ?>
                                <span style="color: #155724; background: #d4edda; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Одобрен</span>
                            <?php else: ?>
                                <span style="color: #856404; background: #fff3cd; padding: 4px 8px; border-radius: 4px; font-size: 12px;">На модерации</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px 10px;">
                            <a href="admin_edit_tutor.php?id=<?= $t['id'] ?>" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">Редактировать</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div style="margin-top: 30px; text-align: center;">
    <a href="dashboard.php" style="color: var(--text-muted); text-decoration: none;">← Вернуться в личный кабинет</a>
</div>

<?php require 'includes/footer.php'; ?>