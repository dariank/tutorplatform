<?php
// tutor_profile.php
require 'config/db.php';
require 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);

// Получаем данные репетитора
$stmt = $pdo->prepare("
    SELECT tp.id, tp.user_id, u.full_name, tp.price_per_hour, tp.rating_avg, tp.city, tp.bio, tp.education, tp.experience_years, tp.reviews_count 
    FROM tutor_profiles tp
    JOIN users u ON u.id = tp.user_id
    WHERE tp.id = ? AND tp.is_approved = 1
");
$stmt->execute([$id]);
$tutor = $stmt->fetch();

if (!$tutor) {
    echo "<h2 style='text-align:center; margin-top:50px;'>Репетитор не найден или еще не одобрен 😔</h2>";
    require 'includes/footer.php';
    exit;
}

// Получаем свободные слоты на будущее
$slots_stmt = $pdo->prepare("
    SELECT id, start_time, end_time 
    FROM slots 
    WHERE tutor_id = ? AND is_booked = 0 AND start_time > NOW()
    ORDER BY start_time ASC
");
$slots_stmt->execute([$tutor['user_id']]);
$slots = $slots_stmt->fetchAll();
?>

<a href="tutors.php" style="display:inline-block; margin-bottom: 20px; color: var(--primary-color);">← Назад к каталогу</a>

<div style="display: flex; gap: 30px; flex-wrap: wrap;">
    <!-- Левая колонка: Инфо -->
    <div style="flex: 2; min-width: 300px; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h1 style="margin-bottom: 10px;"><?= htmlspecialchars($tutor['full_name']) ?></h1>
        <p style="color: var(--text-muted); margin-bottom: 20px;">
            <i class="bi bi-geo-alt-fill" style="color: #d8000c; margin-right: 5px;"></i> <?= htmlspecialchars($tutor['city'] ?? 'Онлайн') ?> | 
            <i class="bi bi-star-fill" style="color: #f1c40f; margin-left: 10px; margin-right: 5px;"></i> <?= number_format($tutor['rating_avg'], 1) ?> (<?= $tutor['reviews_count'] ?> отзывов)
        </p>
        
        <h3 style="margin-bottom: 10px;">О себе</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;"><?= nl2br(htmlspecialchars($tutor['bio'])) ?></p>
        
        <h3 style="margin-bottom: 10px;">Образование</h3>
        <p style="margin-bottom: 20px;"><?= htmlspecialchars($tutor['education'] ?? 'Не указано') ?></p>

        <h3 style="margin-bottom: 10px;">Опыт работы</h3>
        <p><?= $tutor['experience_years'] ?> лет</p>
    </div>

    <!-- Правая колонка: Запись -->
    <div style="flex: 1; min-width: 300px; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); height: fit-content;">
        <h2 style="margin-bottom: 5px;"><?= $tutor['price_per_hour'] ?> ₽ <span style="font-size: 16px; color: var(--text-muted); font-weight: normal;">/ час</span></h2>
        
        <h3 style="margin-top: 20px; margin-bottom: 15px;">Свободное время</h3>
        
        <?php if (count($slots) > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($slots as $slot): ?>
                    <button type="button" class="book-slot-btn" data-slot-id="<?= $slot['id'] ?>" style="width: 100%; background: var(--bg-color); border: 1px solid #ccc; padding: 12px; border-radius: 8px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 15px; color: var(--text-main); transition: 0.3s;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-calendar3" style="color: var(--primary-color);"></i> 
                            <?= date('d.m.Y', strtotime($slot['start_time'])) ?>
                        </span>
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-clock" style="color: var(--primary-color);"></i> 
                            <?= date('H:i', strtotime($slot['start_time'])) ?> - <?= date('H:i', strtotime($slot['end_time'])) ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted);">Пока нет свободных слотов для записи.</p>
        <?php endif; ?>
    </div>
</div>

<!-- МОДАЛЬНОЕ ОКНО -->
<div id="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--white); padding: 40px; border-radius: var(--border-radius); width: 100%; max-width: 400px; text-align: center; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div id="modal-icon" style="font-size: 50px; margin-bottom: 15px; display: flex; justify-content: center;"></div>
        <h2 id="modal-title" style="margin-bottom: 15px; font-size: 24px;">Заголовок</h2>
        <p id="modal-text" style="color: var(--text-muted); margin-bottom: 25px;">Текст</p>
        
        <div id="modal-actions">
            <!-- Кнопки будут добавляться через JS -->
        </div>
    </div>
</div>

<!-- СКРИПТ AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal');
    const modalIcon = document.getElementById('modal-icon');
    const modalTitle = document.getElementById('modal-title');
    const modalText = document.getElementById('modal-text');
    const modalActions = document.getElementById('modal-actions');
    
    // Закрытие окна при клике вне его
    modal.addEventListener('click', function(e) {
        if (e.target === modal) modal.style.display = 'none';
    });

    document.querySelectorAll('.book-slot-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const slotId = this.getAttribute('data-slot-id');
            
            // Блокируем кнопку, пока идет запрос
            this.disabled = true;
            this.style.opacity = '0.5';

            const formData = new FormData();
            formData.append('slot_id', slotId);

            fetch('book.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                modal.style.display = 'flex';
                
                if (data.status === 'success') {
                    // Иконка успеха (зеленая галочка)
                    modalIcon.innerHTML = '<i class="bi bi-check-circle-fill" style="color: #28a745;"></i>';
                    modalTitle.style.color = '#28a745';
                    modalTitle.textContent = 'Успешно!';
                    modalText.textContent = 'Вы успешно записаны на занятие.';
                    modalActions.innerHTML = `<a href="dashboard.php" style="display: block; width: 100%; background: var(--primary-color); color: white; text-decoration: none; padding: 12px; border-radius: 8px; font-weight: bold;">Перейти в кабинет</a>`;
                } else if (data.status === 'busy') {
                    // Иконка занятости (часы)
                    modalIcon.innerHTML = '<i class="bi bi-clock-history" style="color: #d8000c;"></i>';
                    modalTitle.style.color = '#d8000c';
                    modalTitle.textContent = 'Упс! Время занято';
                    modalText.textContent = 'Пожалуйста, выберите другой слот.';
                    modalActions.innerHTML = `<button onclick="document.getElementById('modal').style.display='none'" style="width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid #ccc; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">Понятно</button>`;
                    
                    this.disabled = false;
                    this.style.opacity = '1';
                } else {
                    // Иконка ошибки (восклицательный знак)
                    modalIcon.innerHTML = '<i class="bi bi-exclamation-triangle-fill" style="color: #d8000c;"></i>';
                    modalTitle.style.color = '#d8000c';
                    modalTitle.textContent = 'Ошибка';
                    modalText.textContent = data.message;
                    modalActions.innerHTML = `<button onclick="document.getElementById('modal').style.display='none'" style="width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid #ccc; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">Закрыть</button>`;
                    
                    this.disabled = false;
                    this.style.opacity = '1';
                }
            }) // <-- ЗДЕСЬ БЫЛА ПРОПУЩЕНА СКОБКА
            .catch(err => {
                console.error(err);
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
    });
});
</script>

<?php require 'includes/footer.php'; ?>