<?php
// book.php
require 'config/db.php';
session_start();

header('Content-Type: application/json'); // Отвечаем в формате JSON

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, войдите в аккаунт.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_id'])) {
    $slot_id = (int)$_POST['slot_id'];
    $student_id = $_SESSION['user_id'];

    // Проверяем, есть ли уже бронь на этот слот
    $check = $pdo->prepare("SELECT id FROM bookings WHERE slot_id = ?");
    $check->execute([$slot_id]);
    if ($check->fetch()) {
        echo json_encode(['status' => 'busy', 'message' => 'Упс! Это время уже занято 😔']);
        exit;
    }

    // Узнаем ID репетитора
    $stmt = $pdo->prepare("SELECT tutor_id FROM slots WHERE id = ? AND is_booked = 0");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch();

    if ($slot) {
        $tutor_id = $slot['tutor_id'];
        
        if ($tutor_id == $student_id) {
            echo json_encode(['status' => 'error', 'message' => 'Вы не можете записаться к самому себе!']);
            exit;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE slots SET is_booked = 1 WHERE id = ?");
            $stmt->execute([$slot_id]);

            $stmt = $pdo->prepare("INSERT INTO bookings (slot_id, student_id, tutor_id, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$slot_id, $student_id, $tutor_id]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Вы успешно записаны!']);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Ошибка базы данных.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'busy', 'message' => 'Упс! Это время уже занято 😔']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Неверный запрос.']);
    exit;
}
?>