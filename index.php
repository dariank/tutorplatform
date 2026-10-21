<?php
// index.php
require 'config/db.php';
require 'includes/header.php';

// Получаем 3-х одобренных репетиторов с самым высоким рейтингом
$stmt = $pdo->query("
    SELECT tp.id, u.full_name, tp.price_per_hour, tp.rating_avg, tp.city, tp.bio, tp.experience_years
    FROM tutor_profiles tp
    JOIN users u ON u.id = tp.user_id
    WHERE tp.is_approved = 1
    ORDER BY tp.rating_avg DESC, tp.reviews_count DESC
    LIMIT 3
");
$top_tutors = $stmt->fetchAll();

// Получаем последние 4 отзыва для блока "Отзывы"
$reviews_stmt = $pdo->query("
    SELECT r.rating, r.text, u_student.full_name as student_name, u_tutor.full_name as tutor_name 
    FROM reviews r
    JOIN users u_student ON u_student.id = r.student_id
    JOIN users u_tutor ON u_tutor.id = r.tutor_id
    ORDER BY r.created_at DESC
    LIMIT 4
");
$latest_reviews = $reviews_stmt->fetchAll();
?>

<!-- Баннер -->
<div style="text-align: center; margin-bottom: 60px; padding: 80px 20px; background: var(--primary-color); border-radius: var(--border-radius); box-shadow: 0 10px 30px rgba(156, 183, 212, 0.4); color: white; position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -80px; right: -30px; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>

    <div style="position: relative; z-index: 1; max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 48px; margin-bottom: 15px; font-weight: 800; line-height: 1.2; color: white;">
            Найдите идеального<br>репетитора онлайн
        </h1>
        
        <p style="font-size: 18px; margin-bottom: 30px; opacity: 0.9; font-weight: 400;">
            Более 15 опытных преподавателей по разным предметам. <br> Выбирайте по отзывам, цене и опыту!
        </p>

        <div style="display: flex; justify-content: center; gap: 30px; margin-bottom: 40px; font-size: 15px; font-weight: 500;">
            <span style="display: flex; align-items: center; gap: 8px;"><i class="bi bi-person-workspace"></i> 15+ репетиторов</span>
            <span style="display: flex; align-items: center; gap: 8px;"><i class="bi bi-chat-left-text"></i> 100% реальные отзывы</span>
            <span style="display: flex; align-items: center; gap: 8px;"><i class="bi bi-star-fill" style="color: #ffc107;"></i> 4.9 средняя оценка</span>
        </div>

        <a href="tutors.php" style="background: white; color: var(--primary-color); padding: 16px 40px; border-radius: 30px; font-size: 18px; font-weight: bold; display: inline-flex; align-items: center; gap: 10px; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.1)'">
            <i class="bi bi-search"></i> Найти репетитора
        </a>
    </div>
</div>

<!-- Топ репетиторов (ВОЗВРАЩЕНО) -->
<h2>Топ репетиторов</h2>
<div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 50px;">
    <?php foreach ($top_tutors as $tutor): ?>
        <div class="tutor-card" style="flex-direction: row; gap: 25px; align-items: flex-start; padding: 30px;">
            <div style="flex-shrink: 0; text-align: center;">
                <img src="https://i.pravatar.cc/150?img=<?= $tutor['id'] + 10 ?>" alt="Фото" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <div style="margin-top: 10px; font-size: 12px; color: #28a745; font-weight: bold;">● Свободен</div>
            </div>
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
</div>

<!-- Блок преимуществ -->
<h2>Почему выбирают нас</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 50px;">
    <!-- Карточка 1 -->
    <div class="tutor-card" style="flex-direction: row; align-items: flex-start; gap: 20px;">
        <div style="font-size: 40px; background: var(--bg-color); padding: 15px; border-radius: 12px; color: var(--primary-color); display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;"><i class="bi bi-search"></i></div>
        <div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Не надо искать и звонить</h3>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">Вы выбираете репетитора и отправляете заявку прямо на сайте. Мы свяжем вас в один клик.</p>
        </div>
    </div>
    <!-- Карточка 2 -->
    <div class="tutor-card" style="flex-direction: row; align-items: flex-start; gap: 20px;">
        <div style="font-size: 40px; background: var(--bg-color); padding: 15px; border-radius: 12px; color: var(--primary-color); display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;"><i class="bi bi-wallet2"></i></div>
        <div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Сколько стоит услуга</h3>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5; margin-bottom: 15px;">Прозрачные цены. Вы сразу видите стоимость часа занятий.</p>
            <a href="tutors.php" style="background: var(--primary-color); color: white; padding: 8px 15px; border-radius: 8px; font-size: 14px; display: inline-block; text-decoration: none;">Посмотреть цены</a>
        </div>
    </div>
    <!-- Карточка 3 -->
    <div class="tutor-card" style="flex-direction: row; align-items: flex-start; gap: 20px;">
        <div style="font-size: 40px; background: var(--bg-color); padding: 15px; border-radius: 12px; color: var(--primary-color); display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;"><i class="bi bi-patch-check"></i></div>
        <div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Частные специалисты</h3>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">Только проверенные репетиторы с реальным опытом. Вы можете прочитать их биографию.</p>
        </div>
    </div>
    <!-- Карточка 4 -->
    <div class="tutor-card" style="flex-direction: row; align-items: flex-start; gap: 20px;">
        <div style="font-size: 40px; background: var(--bg-color); padding: 15px; border-radius: 12px; color: var(--primary-color); display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;"><i class="bi bi-chat-quote"></i></div>
        <div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Настоящие отзывы</h3>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">Оставляют только те ученики, которые действительно провели занятие.</p>
        </div>
    </div>
    <!-- Карточка 5 -->
    <div class="tutor-card" style="flex-direction: row; align-items: flex-start; gap: 20px;">
        <div style="font-size: 40px; background: var(--bg-color); padding: 15px; border-radius: 12px; color: var(--primary-color); display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;"><i class="bi bi-gift"></i></div>
        <div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Это бесплатно</h3>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">Поиск репетитора, просмотр профилей и отправка заявок не стоят ни рубля.</p>
        </div>
    </div>
    <!-- Карточка 6 -->
    <div class="tutor-card" style="flex-direction: row; align-items: flex-start; gap: 20px;">
        <div style="font-size: 40px; background: var(--bg-color); padding: 15px; border-radius: 12px; color: var(--primary-color); display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;"><i class="bi bi-clock"></i></div>
        <div>
            <h3 style="margin-bottom: 10px; font-size: 18px;">Удобный график</h3>
            <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5;">Занимайтесь в любое удобное время. Вы сами выбираете дни и часы для уроков.</p>
        </div>
    </div>
</div>

<!-- Блок статистики и отзывов -->
<div style="margin-top: 80px; margin-bottom: 50px;">
    <div style="margin-bottom: 40px;">
        <h2 style="font-size: 28px; line-height: 1.4; color: var(--text-main); margin: 0;">
            <span style="color: var(--primary-color);">27 300 лучших</span> проверенных<br>
            репетиторов и более 95 400 отзывов
        </h2>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
        <div><h2 style="font-size: 28px; margin: 0;">Отзывы</h2></div>
        <div style="display: flex; gap: 10px;" id="review-nav">
            <button id="prev-btn" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #ccc; background: var(--white); cursor: pointer; font-size: 18px; color: var(--text-muted); transition: 0.3s;">←</button>
            <button id="next-btn" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #ccc; background: var(--white); cursor: pointer; font-size: 18px; color: var(--text-muted); transition: 0.3s;">→</button>
        </div>
    </div>

    <div style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; padding-bottom: 20px;" id="reviews-container">
        <?php foreach ($latest_reviews as $review): ?>
            <div style="flex: 0 0 350px; background: var(--white); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #f1c40f; font-size: 20px; margin-bottom: 15px; letter-spacing: 2px;"><?php for ($i = 0; $i < $review['rating']; $i++) echo '★'; ?></div>
                    <p style="font-size: 14px; color: var(--text-main); line-height: 1.6; margin-bottom: 20px; height: 120px; overflow: hidden;"><?= nl2br(htmlspecialchars(mb_substr($review['text'], 0, 150))) ?>...</p>
                </div>
                <div style="display: flex; align-items: center; gap: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                    <img src="https://i.pravatar.cc/100?img=<?= rand(1, 50) ?>" alt="User" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <p style="font-weight: bold; font-size: 14px; color: var(--text-main);"><?= htmlspecialchars($review['student_name']) ?></p>
                        <p style="font-size: 12px; color: var(--text-muted);">Репетитор: <?= htmlspecialchars($review['tutor_name']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Блок "Как это работает" -->
<div style="margin-top: 100px; margin-bottom: 80px; position: relative;">
    <h2 style="text-align: center; margin-bottom: 60px;">Всего 4 простых шага</h2>
    <div style="max-width: 900px; margin: 0 auto; position: relative; padding: 20px;">
        <svg id="steps-line" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;"></svg>
        <div style="position: relative; z-index: 2; display: flex; flex-direction: column; gap: 80px;">
            <!-- Шаг 1 -->
            <div class="step-row" style="display: flex; gap: 20px; align-items: flex-start; width: 55%;">
                <div class="step-circle" style="flex-shrink: 0; width: 80px; height: 80px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; box-shadow: 0 0 0 10px var(--bg-color); z-index: 3;">01</div>
                <div style="background: var(--white); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); width: 100%;">
                    <h3 style="margin-bottom: 10px; font-size: 20px;">Оставьте заявку</h3>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">Ответьте на несколько вопросов, чтобы мы могли порекомендовать подходящего репетитора.</p>
                </div>
            </div>
            <!-- Шаг 2 -->
            <div class="step-row" style="display: flex; flex-direction: row-reverse; gap: 20px; align-items: flex-start; width: 55%; margin-left: auto;">
                <div class="step-circle" style="flex-shrink: 0; width: 80px; height: 80px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; box-shadow: 0 0 0 10px var(--bg-color); z-index: 3;">02</div>
                <div style="background: var(--white); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: right; width: 100%;">
                    <h3 style="margin-bottom: 10px; font-size: 20px;">Изучите преподавателей</h3>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">Мы покажем вашу заявку подходящим репетиторам. Они откликнутся и напишут вам!</p>
                </div>
            </div>
            <!-- Шаг 3 -->
            <div class="step-row" style="display: flex; gap: 20px; align-items: flex-start; width: 55%;">
                <div class="step-circle" style="flex-shrink: 0; width: 80px; height: 80px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; box-shadow: 0 0 0 10px var(--bg-color); z-index: 3;">03</div>
                <div style="background: var(--white); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); width: 100%;">
                    <h3 style="margin-bottom: 10px; font-size: 20px;">Сделайте выбор</h3>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">Договоритесь с выбранным репетитором о начале занятий и удобном расписании.</p>
                </div>
            </div>
            <!-- Шаг 4 -->
            <div class="step-row" style="display: flex; flex-direction: row-reverse; gap: 20px; align-items: flex-start; width: 55%; margin-left: auto;">
                <div class="step-circle" style="flex-shrink: 0; width: 80px; height: 80px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; box-shadow: 0 0 0 10px var(--bg-color); z-index: 3;">04</div>
                <div style="background: var(--white); padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: right; width: 100%;">
                    <h3 style="margin-bottom: 10px; font-size: 20px;">Учитесь и узнавайте новое!</h3>
                    <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6;">Подключитесь к занятию в назначенное время и получайте удовольствие от обучения.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Блок FAQ -->
<div style="margin-top: 100px; margin-bottom: 80px;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="margin-bottom: 10px;">У вас остались вопросы?</h2>
        <p style="color: var(--text-muted); font-size: 16px;">Позвоните нам или посмотрите часто задаваемые вопросы</p>
    </div>
    <div style="max-width: 750px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px; padding: 0 15px;">
        <!-- Вопрос 1 -->
        <div style="background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
            <button onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 20px 25px; background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 17px; font-weight: 700; color: var(--text-main); font-family: inherit;">
                <span style="padding-right: 20px;">Как выбрать репетитора?</span>
                <div style="width: 36px; height: 36px; min-width: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s;"><i class="bi bi-plus-lg" style="font-size: 18px; transition: 0.3s;"></i></div>
            </button>
            <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: var(--bg-color);">
                <div style="padding: 20px 25px; color: var(--text-muted); line-height: 1.6; font-size: 15px;">Вы можете воспользоваться нашим каталогом. Обратите внимание на опыт работы, образование и отзывы других учеников. Если сомневаетесь, забронируйте пробное занятие.</div>
            </div>
        </div>
        <!-- Вопрос 2 -->
        <div style="background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
            <button onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 20px 25px; background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 17px; font-weight: 700; color: var(--text-main); font-family: inherit;">
                <span style="padding-right: 20px;">Сколько стоят занятия с репетитором?</span>
                <div style="width: 36px; height: 36px; min-width: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s;"><i class="bi bi-plus-lg" style="font-size: 18px; transition: 0.3s;"></i></div>
            </button>
            <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: var(--bg-color);">
                <div style="padding: 20px 25px; color: var(--text-muted); line-height: 1.6; font-size: 15px;">Стоимость зависит от опыта преподавателя и формата занятий. Цена за час указана в профиле каждого репетитора. Вы можете использовать фильтр по цене в каталоге.</div>
            </div>
        </div>
        <!-- Вопрос 3 -->
        <div style="background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
            <button onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 20px 25px; background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 17px; font-weight: 700; color: var(--text-main); font-family: inherit;">
                <span style="padding-right: 20px;">Можно ли подготовиться к экзаменам?</span>
                <div style="width: 36px; height: 36px; min-width: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s;"><i class="bi bi-plus-lg" style="font-size: 18px; transition: 0.3s;"></i></div>
            </button>
            <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: var(--bg-color);">
                <div style="padding: 20px 25px; color: var(--text-muted); line-height: 1.6; font-size: 15px;">Конечно! Большинство наших репетиторов специализируются на подготовке к ЕГЭ, ОГЭ и олимпиадам. В профиле преподавателя указаны предметы, с которыми он работает.</div>
            </div>
        </div>
        <!-- Вопрос 4 -->
        <div style="background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
            <button onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 20px 25px; background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 17px; font-weight: 700; color: var(--text-main); font-family: inherit;">
                <span style="padding-right: 20px;">Можно ли заниматься онлайн?</span>
                <div style="width: 36px; height: 36px; min-width: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s;"><i class="bi bi-plus-lg" style="font-size: 18px; transition: 0.3s;"></i></div>
            </button>
            <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: var(--bg-color);">
                <div style="padding: 20px 25px; color: var(--text-muted); line-height: 1.6; font-size: 15px;">Да, все наши репетиторы проводят занятия онлайн через Zoom, Skype или другие удобные платформы. Вы можете заниматься из любой точки мира.</div>
            </div>
        </div>
        <!-- Вопрос 5 -->
        <div style="background: var(--white); border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
            <button onclick="toggleFaq(this)" style="width: 100%; text-align: left; padding: 20px 25px; background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 17px; font-weight: 700; color: var(--text-main); font-family: inherit;">
                <span style="padding-right: 20px;">Как быстро я могу начать?</span>
                <div style="width: 36px; height: 36px; min-width: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s;"><i class="bi bi-plus-lg" style="font-size: 18px; transition: 0.3s;"></i></div>
            </button>
            <div style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: var(--bg-color);">
                <div style="padding: 20px 25px; color: var(--text-muted); line-height: 1.6; font-size: 15px;">Вы можете забронировать занятие прямо сейчас. Как только репетитор подтвердит вашу заявку, вы сможете связаться с ним и договориться о первом уроке.</div>
            </div>
        </div>
    </div>
</div>

<script>
// Скрипт прокрутки отзывов
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('reviews-container');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    if (nextBtn && prevBtn && container) {
        nextBtn.addEventListener('click', () => container.scrollBy({ left: 370, behavior: 'smooth' }));
        prevBtn.addEventListener('click', () => container.scrollBy({ left: -370, behavior: 'smooth' }));
        [prevBtn, nextBtn].forEach(btn => {
            btn.addEventListener('mouseenter', () => { btn.style.background = 'var(--primary-color)'; btn.style.color = '#fff'; btn.style.borderColor = 'var(--primary-color)'; });
            btn.addEventListener('mouseleave', () => { btn.style.background = 'var(--white)'; btn.style.color = 'var(--text-muted)'; btn.style.borderColor = '#ccc'; });
        });
    }
});

// Скрипт для аккордеона
function toggleFaq(button) {
    const content = button.nextElementSibling;
    const iconDiv = button.querySelector('div');
    const icon = iconDiv.querySelector('i');
    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
        content.style.maxHeight = '0px'; icon.className = 'bi bi-plus-lg'; iconDiv.style.transform = 'rotate(0deg)';
    } else {
        content.style.maxHeight = content.scrollHeight + 'px'; icon.className = 'bi bi-dash-lg'; iconDiv.style.transform = 'rotate(180deg)';
    }
}

// Скрипт для рисования линии
function drawStepsLine() {
    const svg = document.getElementById('steps-line');
    const circles = document.querySelectorAll('.step-circle');
    const container = svg.parentElement;
    if (circles.length < 2) return;
    
    svg.innerHTML = '';
    const containerRect = container.getBoundingClientRect();
    const points = [];
    circles.forEach(circle => {
        const rect = circle.getBoundingClientRect();
        points.push({
            x: rect.left + rect.width / 2 - containerRect.left,
            y: rect.top + rect.height / 2 - containerRect.top,
            radius: rect.width / 2
        });
    });
    
    svg.setAttribute('viewBox', `0 0 ${containerRect.width} ${containerRect.height}`);
    
    for (let i = 0; i < points.length - 1; i++) {
        const start = points[i];
        const end = points[i + 1];
        const dx = end.x - start.x;
        const dy = end.y - start.y;
        const angle = Math.atan2(dy, dx);
        
        const startX = start.x + Math.cos(angle) * start.radius;
        const startY = start.y + Math.sin(angle) * start.radius;
        const endX = end.x - Math.cos(angle) * end.radius;
        const endY = end.y - Math.sin(angle) * end.radius;
        
        const midX = (startX + endX) / 2;
        const midY = (startY + endY) / 2;
        
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', `M ${startX} ${startY} C ${midX} ${startY}, ${midX} ${endY}, ${endX} ${endY}`);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', 'var(--primary-color)');
        path.setAttribute('stroke-width', '3');
        path.setAttribute('stroke-dasharray', '10,10');
        path.setAttribute('stroke-linecap', 'round');
        path.setAttribute('opacity', '0.4');
        
        svg.appendChild(path);
    }
}

window.addEventListener('load', drawStepsLine);
window.addEventListener('resize', drawStepsLine);
</script>

<?php require 'includes/footer.php'; ?>