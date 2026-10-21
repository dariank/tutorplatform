<?php
// tutors.php
require 'config/db.php';
require 'includes/header.php';

// Города для выпадающего списка
$cities = $pdo->query("SELECT DISTINCT city FROM tutor_profiles WHERE city IS NOT NULL AND is_approved = 1 ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);

// ПРЕДМЕТЫ для выпадающего списка
$subjects = $pdo->query("SELECT id, name FROM subjects ORDER BY name")->fetchAll();
?>

<h2 style="margin-bottom: 20px;">Все репетиторы</h2>

<!-- Форма фильтрации -->
<div style="background: var(--white); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px; display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
    <form id="filter-form" method="GET" action="tutors.php" style="display: flex; gap: 20px; flex-wrap: wrap; width: 100%;">
        
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 5px;">Имя репетитора</label>
            <input type="text" name="search" id="search-input" placeholder="Например, Иван" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
        </div>

        <!-- НОВЫЙ БЛОК: ВЫБОР ПРЕДМЕТА -->
        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 5px;">Предмет</label>
            <select name="subject_id" id="subject-select" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
                <option value="">Все предметы</option>
                <?php foreach ($subjects as $subj): ?>
                    <option value="<?= $subj['id'] ?>"><?= htmlspecialchars($subj['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 5px;">Город</label>
            <select name="city" id="city-select" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
                <option value="">Все города</option>
                <?php foreach ($cities as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 5px;">Цена до (₽/час)</label>
            <input type="number" name="max_price" id="price-input" placeholder="Например, 1500" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
        </div>

        <!-- Кнопка сброса фильтров -->
        <div>
            <label style="display: block; font-size: 13px; color: transparent; margin-bottom: 5px;">&nbsp;</label>
            <button type="button" id="reset-btn" style="background: #e0e0e0; color: var(--text-main); border: none; padding: 0 25px; border-radius: 8px; cursor: pointer; font-weight: bold; height: 44px; transition: 0.3s;">
                Сбросить
            </button>
        </div>
    </form>
</div>

<!-- Контейнер, куда будут подгружаться карточки -->
<div id="tutors-container" style="display: grid; grid-template-columns: 1fr; gap: 30px;">
    <?php require 'tutors_list.php'; ?>
</div>

<!-- Скрипт для мгновенной фильтрации -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const container = document.getElementById('tutors-container');
    const searchInput = document.getElementById('search-input');
    const citySelect = document.getElementById('city-select');
    const subjectSelect = document.getElementById('subject-select'); // Получили селект предметов
    const priceInput = document.getElementById('price-input');
    const resetBtn = document.getElementById('reset-btn');
    
    let debounceTimer;

    // Функция, которая запрашивает данные с сервера без перезагрузки страницы
    function fetchTutors() {
        const formData = new FormData(form);
        const searchParams = new URLSearchParams(formData);
        
        container.style.opacity = '0.5';

        fetch('tutors_list.php?' + searchParams.toString())
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1';
            })
            .catch(err => {
                console.error('Ошибка:', err);
                container.style.opacity = '1';
            });
    }

    // 1. Мгновенная реакция на выбор города и предмета
    citySelect.addEventListener('change', fetchTutors);
    subjectSelect.addEventListener('change', fetchTutors);

    // 2. Мгновенная реакция на ввод текста и цифр (с задержкой)
    function handleInput() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchTutors, 500);
    }

    searchInput.addEventListener('input', handleInput);
    priceInput.addEventListener('input', handleInput);

    // 3. Обработка кнопки "Сбросить"
    resetBtn.addEventListener('click', function() {
        form.reset();
        fetchTutors();
    });

    // 4. Останавливаем стандартную отправку формы
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchTutors();
    });
});
</script>
<?php require 'includes/footer.php'; ?>