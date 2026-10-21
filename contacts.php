<?php
// contacts.php
require 'config/db.php';
require 'includes/header.php';
?>

<!-- Блок О нас -->
<div style="background: var(--white); padding: 50px 40px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 60px; text-align: center;">
    <h1 style="font-size: 36px; margin-bottom: 20px;">О нас</h1>
    <p style="font-size: 16px; color: var(--text-muted); line-height: 1.8; max-width: 800px; margin: 0 auto;">
        TutorPlatform — это современная образовательная платформа, созданная для того, чтобы сделать поиск качественных знаний максимально простым и удобным. 
        Мы объединяем лучших репетиторов и мотивированных учеников. Наша миссия — помочь каждому раскрыть свой потенциал, подготовиться к экзаменам и достичь поставленных целей без лишних хлопот.
    </p>
</div>

<!-- Блок Контакты -->
<h2 style="text-align: center; margin-bottom: 40px;">Контакты</h2>

<div style="display: flex; gap: 50px; flex-wrap: wrap; margin-bottom: 60px;">
    
    <!-- Левая колонка: Информация -->
    <div style="flex: 1; min-width: 300px; background: var(--white); padding: 40px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3 style="font-size: 22px; margin-bottom: 15px;">Наш адрес</h3>
        <p style="font-size: 16px; color: var(--text-main); margin-bottom: 5px;">г. Нижний Новгород, ул. Покровская, д. 10</p>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 30px;">Метро: Горьковская</p>

        <h3 style="font-size: 22px; margin-bottom: 15px;">Телефон</h3>
        <p style="font-size: 16px; color: var(--text-main); margin-bottom: 5px;">+7 (495) 123-45-67</p>
        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 30px;">Пн-Вс: 10:00–22:00</p>

        <h3 style="font-size: 22px; margin-bottom: 15px;">Email</h3>
        <p style="font-size: 16px; color: var(--text-main); margin-bottom: 30px;">support@tutorplatform.ru</p>

        <h3 style="font-size: 22px; margin-bottom: 15px;">Мы в соцсетях</h3>
        <div style="display: flex; gap: 20px; font-size: 16px;">
            <a href="#" style="color: var(--primary-color); text-decoration: none;">ВКонтакте</a>
            <a href="#" style="color: var(--primary-color); text-decoration: none;">Одноклассники</a>
        </div>
    </div>

    <!-- Правая колонка: Карта и форма -->
    <div style="flex: 1; min-width: 300px;">
        <!-- Карта -->
        <div style="background: var(--white); padding: 10px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px;">
            <iframe 
                src="https://yandex.ru/map-widget/v1/?ll=44.0059%2C56.3104&z=16&text=Нижний%20Новгород%2C%20улица%20Покровская%2C%2010&mode=search&sll=44.0059%2C56.3104&sspn=0.01%2C0.01" 
                width="100%" 
                height="300" 
                frameborder="0" 
                style="border-radius: 8px; border: none;">
            </iframe>
            <p style="font-size: 14px; color: var(--text-muted); margin-top: 10px; text-align: center;">
                Открыть в <a href="https://yandex.ru/maps/?text=Нижний%20Новгород%2C%20улица%20Покровская%2C%2010" target="_blank" style="color: var(--primary-color);">Яндекс.Картах</a>
            </p>
        </div>
    </div>
</div>

<!-- Форма обратной связи -->
<div style="background: var(--white); padding: 50px 40px; border-radius: var(--border-radius); box-shadow: var(--shadow); max-width: 800px; margin: 0 auto 60px;">
    <h2 style="text-align: center; margin-bottom: 30px;">Напишите нам</h2>
    <form method="POST" action="#">
        <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: bold;">Имя</label>
                <input type="text" required placeholder="Иван Иванов" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: bold;">Email</label>
                <input type="email" required placeholder="you@mail.ru" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none;">
            </div>
        </div>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: bold;">Сообщение</label>
            <textarea rows="4" required placeholder="Ваше сообщение..." style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; outline: none; font-family: inherit;"></textarea>
        </div>
        <button type="submit" style="width: 100%; background: var(--primary-color); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">Отправить сообщение</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>