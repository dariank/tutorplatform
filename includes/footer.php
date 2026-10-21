<?php
// includes/footer.php
?>
</main> 

<footer style="background-color: #1a1a1a; color: #e0e0e0; border-top: 1px solid #333; padding: 40px 0 20px; margin-top: auto;">
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
        
        <!-- Колонка 1: О проекте -->
        <div>
            <a href="index.php" style="font-size: 24px; font-weight: bold; color: var(--primary-color); text-decoration: none; display: block; margin-bottom: 15px;">TutorPlatform</a>
            <p style="color: #a0a0a0; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                Платформа для поиска лучших репетиторов. Учитесь с удовольствием, выбирайте проверенных специалистов и достигайте своих целей.
            </p>
        </div>

        <!-- Колонка 2: Навигация -->
        <div>
            <h4 style="margin-bottom: 15px; color: #ffffff; font-size: 16px;">Навигация</h4>
                       <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                <li><a href="index.php" style="color: #a0a0a0; font-size: 14px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a0a0a0'">Главная</a></li>
                <li><a href="tutors.php" style="color: #a0a0a0; font-size: 14px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a0a0a0'">Все репетиторы</a></li>
                <li><a href="dashboard.php" style="color: #a0a0a0; font-size: 14px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a0a0a0'">Личный кабинет</a></li>
                <li><a href="contacts.php" style="color: #a0a0a0; font-size: 14px; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a0a0a0'">Контакты</a></li>
            </ul>
        </div>

        <!-- Колонка 3: Контакты и Соцсети -->
        <div>
            <h4 style="margin-bottom: 15px; color: #ffffff; font-size: 16px;">Контакты</h4>
            <p style="color: #a0a0a0; font-size: 14px; margin-bottom: 10px;">✉️ support@tutorplatform.ru</p>
            <p style="color: #a0a0a0; font-size: 14px; margin-bottom: 20px;">📞 +7 (999) 123-45-67</p>
            
            <!-- Соцсети -->
            <div style="display: flex; gap: 12px;">
                <!-- ВКонтакте -->
                <a href="#" title="ВКонтакте" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #333333; border-radius: 10px; transition: 0.3s;" onmouseover="this.style.background='var(--primary-color)'" onmouseout="this.style.background='#333333'">
                    <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/vk.svg" alt="VK" style="width: 20px; height: 20px; filter: invert(1); opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                </a>
                <!-- Одноклассники -->
                <a href="#" title="Одноклассники" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #333333; border-radius: 10px; transition: 0.3s;" onmouseover="this.style.background='var(--primary-color)'" onmouseout="this.style.background='#333333'">
                    <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/odnoklassniki.svg" alt="OK" style="width: 20px; height: 20px; filter: invert(1); opacity: 0.7; transition: 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                </a>
            </div>
        </div>
    </div>

    <!-- Нижняя полоса с копирайтом -->
    <div style="text-align: center; color: #666666; font-size: 14px; margin-top: 40px; padding-top: 20px; border-top: 1px solid #333333;">
        <p>&copy; <?= date('Y') ?> TutorPlatform. Все права защищены.</p>
        <p style="margin-top: 5px; font-size: 12px;">Сделано с любовью для портфолио.</p>
    </div>
</footer>

</body>
</html>