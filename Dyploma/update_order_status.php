<?php
// Подключение к базе данных
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $update_query = "UPDATE orders SET pending = '$new_status' WHERE order_id = '$order_id'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "Статус заказа успешно изменен";
    } else {
        echo "Ошибка при изменении статуса заказа: " . mysqli_error($conn);
    }

    header("Location: admin_panel.php");
    exit;
}
?>