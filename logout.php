<?php
// logout.php
session_start();
session_destroy(); // Уничтожаем сессию
header('Location: index.php'); // Кидаем на главную
exit;
?>