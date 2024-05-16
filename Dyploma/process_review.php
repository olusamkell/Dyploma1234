<?php
$session_cookies = 60 * 60; session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
  header('location:login.php');
};

$name = $_POST['name'] ?? '';
$message = $_POST['message'] ?? '';


// Проверка, что пользователь не гость
if ($_SESSION['role'] === 'guest') {
    echo "<script>alert('В режиме гостя нельзя оставлять комментарии,пожалуйста зарегистрируйтесь!'); window.location='comments.php';</script>";
    die();
}

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['commentId'])) {
    $commentId = $_POST['commentId'];
    
    // Удаляем комментарий по id
    $sql = "DELETE FROM comments WHERE id = $commentId";
    if ($conn->query($sql) === TRUE) {
        $affectedRows = $conn->affected_rows;
        if($affectedRows > 0) {
            // Проверяем user_id
            $sql = "DELETE FROM comments WHERE id = $commentId AND user_id = $user_id";
            if ($conn->query($sql) === TRUE) {
                echo "Комментарий успешно удален!";
            } else {
                echo "Ошибка при удалении комментария из базы данных: " . $conn->error;
            }
        } else {
            echo "Комментарий не найден или уже удален.";
        }
    } else {
        echo "Ошибка при удалении комментария из базы данных: " . $conn->error;
    }
}

// Проверка наличия данных
if (isset($_POST['name'], $_POST['message'], $_POST['user_id'])) {
    $name = $_POST['name'];
    $message = $_POST['message'];
    $user_id = $_POST['user_id'];

    // Подготовка и выполнение запроса к базе данных
    $sql = "INSERT INTO comments (user_id, name, message) VALUES ('$user_id', '$name', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "Отзыв успешно отправлен!";
        header('location:comments.php');
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }

    // Закрытие соединения
    $conn->close();
}
?>