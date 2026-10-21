<?php
// tutors_list.php
require 'config/db.php';

$search = trim($_GET['search'] ?? '');
$city = trim($_GET['city'] ?? '');
$max_price = (int)($_GET['max_price'] ?? 0);
$subject_id = (int)($_GET['subject_id'] ?? 0); // Получаем ID предмета

// Базовый запрос
$sql = "SELECT tp.id, u.full_name, u.avatar_url, tp.price_per_hour, tp.rating_avg, tp.city, tp.bio, tp.experience_years 
        FROM tutor_profiles tp
        JOIN users u ON u.id = tp.user_id
        WHERE tp.is_approved = 1";
$params = [];

// Если выбран предмет, добавляем связь с таблицей tutor_subjects
if ($subject_id > 0) {
    $sql = "SELECT DISTINCT tp.id, u.full_name, u.avatar_url, tp.price_per_hour, tp.rating_avg, tp.city, tp.bio, tp.experience_years 
            FROM tutor_profiles tp
            JOIN users u ON u.id = tp.user_id
            JOIN tutor_subjects ts ON ts.tutor_profile_id = tp.id
            WHERE tp.is_approved = 1 AND ts.subject_id = :subject_id";
    $params[':subject_id'] = $subject_id;
}

if ($search !== '') {
    $sql .= " AND u.full_name LIKE :search";
    $params[':search'] = "%$search%";
}
if ($city !== '') {
    $sql .= " AND tp.city = :city";
    $params[':city'] = $city;
}
if ($max_price > 0) {
    $sql .= " AND tp.price_per_hour <= :max_price";
    $params[':max_price'] = $max_price;
}

$sql .= " ORDER BY tp.rating_avg DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tutors = $stmt->fetchAll();
?>

<?php if (count($tutors) > 0): ?>
    <?php foreach ($tutors as $tutor): ?>
        <!-- Карточка в стиле "список" -->
        <div class="tutor-card" style="flex-direction: row; gap: 25px; align-items: flex-start; padding: 30px;">
            
            <!-- Левая часть: Аватарка -->
            <div style="flex-shrink: 0; text-align: center;">
                <?php if (!empty($tutor['avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($tutor['avatar_url']) ?>" alt="Фото" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <?php else: ?>
                    <img src="https://i.pravatar.cc/150?img=<?= $tutor['id'] + 10 ?>" alt="Фото" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <?php endif; ?>
                <div style="margin-top: 10px; font-size: 12px; color: #28a745; font-weight: bold;">● Свободен</div>
            </div>

            <!-- Правая часть: Информация -->
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div>
                        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 5px;">Репетитор • <?= htmlspecialchars($tutor['city'] ?? 'Онлайн') ?></p>
                        <h3 style="margin-bottom: 5px; font-size: 22px; color: var(--text-main);"><?= htmlspecialchars($tutor['full_name']) ?></h3>
                        <p style="font-weight: bold; font-size: 16px; color: var(--text-main);"><?= $tutor['price_per_hour'] ?> ₽/час</p>
                    </div>
                    <a href="tutor_profile.php?id=<?= $tutor['id'] ?>" style="background: var(--bg-color); color: var(--primary-hover); padding: 10px 25px; border-radius: 8px; font-weight: bold; text-decoration: none; border: 1px solid transparent; transition: 0.3s;" onmouseover="this.style.borderColor='var(--primary-color)'" onmouseout="this.style.borderColor='transparent'">Подробнее</a>
                </div>

                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 15px;">
                    <b>Стаж:</b> <?= $tutor['experience_years'] ?> лет &nbsp;|&nbsp; <b>Рейтинг:</b> ⭐ <?= number_format($tutor['rating_avg'], 1) ?>
                </p>

                <p style="font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 15px;">
                    <?= htmlspecialchars(mb_substr($tutor['bio'], 0, 200)) ?>...
                </p>

                <div style="display: flex; gap: 20px; font-size: 13px; color: var(--text-muted);">
                    <span style="display: flex; align-items: center; gap: 5px;"><i class="bi bi-book" style="color: var(--primary-color);"></i> Подготовка к ЕГЭ/ОГЭ</span>
                    <span style="display: flex; align-items: center; gap: 5px;"><i class="bi bi-laptop" style="color: var(--primary-color);"></i> Онлайн-занятия</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div style="grid-column: 1 / -1; background: var(--white); padding: 40px; border-radius: var(--border-radius); text-align: center; color: var(--text-muted);">
        <h3>По вашему запросу никого не найдено </h3>
        <p>Попробуйте изменить фильтры.</p>
    </div>
<?php endif; ?>