<?php
// admin_edit_tutor.php
require 'config/db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$is_edit = ($id > 0);

// --- ОБРАБОТКА ДОБАВЛЕНИЯ/РЕДАКТИРОВАНИЯ РЕПЕТИТОРА ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tutor'])) {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $price = (float)$_POST['price_per_hour'];
    $city = trim($_POST['city']);
    $bio = trim($_POST['bio']);
    $education = trim($_POST['education']);
    $experience = (int)$_POST['experience_years'];
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    $avatar_path = null;

    // Обработка загрузки фото
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

    if ($is_edit) {
        // Обновляем существующего (добавили телефон)
        $stmt = $pdo->prepare("UPDATE users u JOIN tutor_profiles tp ON u.id = tp.user_id SET u.full_name = ?, u.email = ?, u.phone = ? WHERE tp.id = ?");
        $stmt->execute([$name, $email, $phone, $id]);

        // Если загружено новое фото — обновляем его
        if ($avatar_path) {
            $stmt = $pdo->prepare("UPDATE users u JOIN tutor_profiles tp ON u.id = tp.user_id SET u.avatar_url = ? WHERE tp.id = ?");
            $stmt->execute([$avatar_path, $id]);
        }

        $stmt = $pdo->prepare("UPDATE tutor_profiles SET price_per_hour = ?, city = ?, bio = ?, education = ?, experience_years = ?, is_approved = ? WHERE id = ?");
        $stmt->execute([$price, $city, $bio, $education, $experience, $is_approved, $id]);
    } else {
        // Создаем нового (ДОБАВИЛИ phone)
        $password = '123';
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, full_name, phone, avatar_url) VALUES (?, ?, 'tutor', ?, ?, ?)");
        $stmt->execute([$email, $password, $name, $phone, $avatar_path]);
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO tutor_profiles (user_id, price_per_hour, city, bio, education, experience_years, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $price, $city, $bio, $education, $experience, $is_approved]);
        
        $id = $pdo->lastInsertId();
    }

    header("Location: admin_edit_tutor.php?id=$id&saved=1");
    exit;
}

// --- ОБРАБОТКА ДОБАВЛЕНИЯ СЛОТА ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slot'])) {
    $date = $_POST['slot_date'];
    $start = $_POST['slot_start'];
    $end = $_POST['slot_end'];
    
    $start_time = $date . ' ' . $start . ':00';
    $end_time = $date . ' ' . $end . ':00';

    $stmt = $pdo->prepare("SELECT user_id FROM tutor_profiles WHERE id = ?");
    $stmt->execute([$id]);
    $tutor_user_id = $stmt->fetchColumn();

    if ($tutor_user_id) {
        $stmt = $pdo->prepare("INSERT INTO slots (tutor_id, start_time, end_time, is_booked) VALUES (?, ?, ?, 0)");
        $stmt->execute([$tutor_user_id, $start_time, $end_time]);
        
        header("Location: admin_edit_tutor.php?id=$id&slot_added=1");
        exit;
    }
}

// --- ОБРАБОТКА УДАЛЕНИЯ СЛОТА ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_slot'])) {
    $slot_id = (int)$_POST['slot_id'];
    $stmt = $pdo->prepare("DELETE FROM slots WHERE id = ?");
    $stmt->execute([$slot_id]);
    header("Location: admin_edit_tutor.php?id=$id&slot_deleted=1");
    exit;
}

// --- ПОЛУЧАЕМ ДАННЫЕ ---
if ($is_edit) {
    // Добавили u.phone и u.avatar_url в запрос
    $stmt = $pdo->prepare("SELECT tp.id, tp.user_id, u.full_name, u.email, u.phone, u.avatar_url, tp.price_per_hour, tp.city, tp.bio, tp.education, tp.experience_years, tp.is_approved FROM tutor_profiles tp JOIN users u ON u.id = tp.user_id WHERE tp.id = ?");
    $stmt->execute([$id]);
    $tutor = $stmt->fetch();
    if (!$tutor) die("Репетитор не найден.");

    $slots_stmt = $pdo->prepare("SELECT id, start_time, end_time, is_booked FROM slots WHERE tutor_id = ? ORDER BY start_time ASC");
    $slots_stmt->execute([$tutor['user_id']]);
    $slots = $slots_stmt->fetchAll();
} else {
    $tutor = ['full_name' => '', 'email' => '', 'phone' => '', 'avatar_url' => '', 'price_per_hour' => '', 'city' => '', 'bio' => '', 'education' => '', 'experience_years' => 0, 'is_approved' => 1];
    $slots = [];
}
?>

<h2 style="margin-bottom: 20px;"><?= $is_edit ? 'Редактирование репетитора' : 'Новый репетитор' ?></h2>

<?php if (isset($_GET['saved'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Данные репетитора успешно сохранены!</div>
<?php endif; ?>

<div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: flex-start;">
    
    <!-- ЛЕВАЯ КОЛОНКА: ФОРМА РЕПЕТИТОРА -->
    <div style="flex: 2; min-width: 400px; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_tutor" value="1">
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Имя</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($tutor['full_name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Email (логин)</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($tutor['email']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Телефон</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($tutor['phone'] ?? '') ?>" placeholder="+7 (999) 123-45-67" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Цена (₽/час)</label>
                    <input type="number" name="price_per_hour" value="<?= htmlspecialchars($tutor['price_per_hour']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Город</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($tutor['city']) ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Опыт (лет)</label>
                    <input type="number" name="experience_years" value="<?= htmlspecialchars($tutor['experience_years']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Образование</label>
                <input type="text" name="education" value="<?= htmlspecialchars($tutor['education']) ?>" placeholder="Например: МГУ, мехмат" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">О себе</label>
                <textarea name="bio" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit;"><?= htmlspecialchars($tutor['bio']) ?></textarea>
            </div>

            <div style="margin-bottom: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                <label style="display: block; margin-bottom: 10px; font-weight: bold; font-size: 14px;">Фотография репетитора</label>
                
                <?php if (!empty($tutor['avatar_url'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?= htmlspecialchars($tutor['avatar_url']) ?>" alt="Текущее фото" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; box-shadow: var(--shadow);">
                    </div>
                <?php endif; ?>
                
                <input type="file" name="avatar" accept="image/*" style="width: 100%; font-size: 14px;">
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Оставьте пустым, если не хотите менять фото.</p>
            </div>

            <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_approved" id="is_approved" <?= $tutor['is_approved'] ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <label for="is_approved" style="font-weight: bold; cursor: pointer;">Одобрен (показывать на сайте)</label>
            </div>

            <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                <?= $is_edit ? 'Сохранить изменения' : 'Создать репетитора' ?>
            </button>
            <a href="admin_panel.php?tab=tutors" style="margin-left: 10px; color: var(--text-muted); text-decoration: none;">Назад</a>
        </form>
    </div>

    <!-- ПРАВАЯ КОЛОНКА: СЛОТЫ -->
    <?php if ($is_edit): ?>
    <div style="flex: 1; min-width: 350px; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3 style="margin-bottom: 20px;">Расписание (Слоты)</h3>
        
        <?php if (isset($_GET['slot_added'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">Слот успешно добавлен!</div>
        <?php endif; ?>
        <?php if (isset($_GET['slot_deleted'])): ?>
            <div style="background: #ffdddd; color: #d8000c; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">Слот удален!</div>
        <?php endif; ?>

        <form method="POST" style="margin-bottom: 20px; background: var(--bg-color); padding: 15px; border-radius: 8px;">
            <input type="hidden" name="add_slot" value="1">
            <div style="margin-bottom: 10px;">
                <label style="font-size: 12px; font-weight: bold;">Дата</label>
                <input type="date" name="slot_date" required style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Начало</label>
                    <input type="time" name="slot_start" required style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 12px; font-weight: bold;">Конец</label>
                    <input type="time" name="slot_end" required style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                </div>
            </div>
            <button type="submit" style="width: 100%; background: #28a745; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold;">+ Добавить слот</button>
        </form>

        <div style="display: flex; flex-direction: column; gap: 10px; max-height: 400px; overflow-y: auto;">
            <?php foreach ($slots as $s): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #eee; padding: 10px; border-radius: 5px; background: <?= $s['is_booked'] ? '#f8f9fa' : '#fff' ?>;">
                    <div>
                        <div style="font-weight: bold; font-size: 14px;"><?= date('d.m.Y', strtotime($s['start_time'])) ?></div>
                        <div style="font-size: 12px; color: var(--text-muted);"><?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?></div>
                        <?php if ($s['is_booked']): ?>
                            <span style="font-size: 11px; color: #d8000c;">Занято</span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('Удалить слот?');">
                        <input type="hidden" name="delete_slot" value="1">
                        <input type="hidden" name="slot_id" value="<?= $s['id'] ?>">
                        <button type="submit" style="background: none; border: none; color: #d8000c; cursor: pointer; font-size: 18px;" title="Удалить">✖</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if (empty($slots)): ?>
                <p style="color: var(--text-muted); font-size: 14px; text-align: center;">Нет добавленных слотов.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require 'includes/footer.php'; ?>