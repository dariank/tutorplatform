<?php
// cancel_booking.php
require 'config/db.php';
session_start();

// Проверка на вход
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    $user_id = $_SESSION['user_id'];

    // Проверяем, что это бронирование принадлежит текущему пользователю (ученику или репетитору)
    $stmt = $pdo->prepare("SELECT slot_id FROM bookings WHERE id = ? AND (student_id = ? OR tutor_id = ?)");
    $stmt->execute([$booking_id, $user_id, $user_id]);
    $booking = $stmt->fetch();

    if ($booking) {
        // Начинаем транзакцию
        $pdo->beginTransaction();
        
        try {
            // Удаляем бронь из таблицы bookings
            $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$booking_id]);
            
            // Освобождаем слот (is_booked = 0), чтобы другие могли записаться
            $pdo->prepare("UPDATE slots SET is_booked = 0 WHERE id = ?")->execute([$booking['slot_id']]);
            
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }
}

// Возвращаемся в кабинет
header('Location: dashboard.php');
exit;
?>