<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из формы
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$appeal = $_POST['appeal'];

$id_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Предполагается, что id пользователя сохраняется в сессии
$sql = "INSERT INTO partnership (id_user, name, phone_number, email, appeal) 
        VALUES ('$id_user', '$name', '$phone', '$email', '$appeal')";

if ($conn->query($sql) === TRUE) {
    // Если запрос выполнен успешно, выведем сообщение об этом и выполним перенаправление на страницу partnership.php
    echo '<script>alert("Данные успешно добавлены в базу данных");</script>';
    echo '<script>window.location.href = "partnership.php";</script>';
} else {
    // Если возникла ошибка при выполнении запроса, выведем сообщение об ошибке
    echo "Ошибка: " . $sql . "<br>" . $conn->error;
}

// Закрытие соединения
$conn->close();
?>
