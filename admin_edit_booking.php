<?php
// admin_edit_booking.php
require 'config/db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

// --- ОБРАБОТКА СОХРАНЕНИЯ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $tutor_id = (int)$_POST['tutor_id'];
    $status = $_POST['status'];
    
    // Получаем дату и время
    $date = $_POST['date'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    
    $start_time = $date . ' ' . $time_start . ':00';
    $end_time = $date . ' ' . $time_end . ':00';

    // Обновляем слот (время)
    $stmt = $pdo->prepare("UPDATE slots s JOIN bookings b ON b.slot_id = s.id SET s.start_time = ?, s.end_time = ?, s.tutor_id = ? WHERE b.id = ?");
    $stmt->execute([$start_time, $end_time, $tutor_id, $id]);

    // Обновляем саму заявку (ученик, репетитор, статус)
    $stmt = $pdo->prepare("UPDATE bookings SET student_id = ?, tutor_id = ?, status = ? WHERE id = ?");
    $stmt->execute([$student_id, $tutor_id, $status, $id]);

    header('Location: admin_panel.php?tab=bookings');
    exit;
}

// --- ПОЛУЧАЕМ ТЕКУЩИЕ ДАННЫЕ ---
$stmt = $pdo->prepare("
    SELECT b.id, b.student_id, b.tutor_id, b.status, s.start_time, s.end_time 
    FROM bookings b 
    JOIN slots s ON s.id = b.slot_id 
    WHERE b.id = ?
");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Заявка не найдена.");
}

// Списки для выпадающих меню
$students = $pdo->query("SELECT id, full_name FROM users WHERE role = 'student' ORDER BY full_name")->fetchAll();
$tutors = $pdo->query("SELECT u.id, u.full_name FROM users u JOIN tutor_profiles tp ON tp.user_id = u.id ORDER BY u.full_name")->fetchAll();

// Разбиваем дату и время для полей ввода
$date_val = date('Y-m-d', strtotime($booking['start_time']));
$time_start_val = date('H:i', strtotime($booking['start_time']));
$time_end_val = date('H:i', strtotime($booking['end_time']));
?>

<h2 style="margin-bottom: 20px;">Редактирование заявки #<?= $id ?></h2>

<div style="background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); max-width: 600px;">
    <form method="POST">
        
        <!-- Ученик -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Ученик</label>
            <select name="student_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $booking['student_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Репетитор -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Репетитор</label>
            <select name="tutor_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                <?php foreach ($tutors as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $t['id'] == $booking['tutor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Дата -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Дата</label>
            <input type="date" name="date" value="<?= $date_val ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
        </div>

        <!-- Время -->
        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Начало</label>
                <input type="time" name="time_start" value="<?= $time_start_val ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Конец</label>
                <input type="time" name="time_end" value="<?= $time_end_val ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
            </div>
        </div>

        <!-- Статус -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Статус</label>
            <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Ожидает</option>
                <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Подтверждено</option>
                <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Завершено</option>
                <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Отменено</option>
            </select>
        </div>

        <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer;">Сохранить изменения</button>
        <a href="admin_panel.php?tab=bookings" style="margin-left: 10px; color: var(--text-muted); text-decoration: none;">Отмена</a>
    </form>
</div>

<?php require 'includes/footer.php'; ?>