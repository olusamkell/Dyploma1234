<?php
include 'connect.php'; // Подключаем файл с соединением с базой данных

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = $_POST['title'];
  $content = $_POST['content'];
  $author = $_POST['author'];
  $category = $_POST['category'];
  $read_time = $_POST['read_time'];
  $image_name = $_FILES['image']['name'] ?? null;

  // Проверяем, был ли выбран файл
  if(isset($_FILES['image']) && $_FILES['image']['error'] == 0 && $_FILES['image']['size'] > 0) {
    $image_name = $_FILES['image']['name']; // Получаем название файла изображения
    $target_dir = "image/"; // Папка, в которую будем сохранять изображение
    $target_file = $target_dir . basename($image_name); // Полный путь к файлу на сервере

    // Перемещаем файл изображения в папку на сервере
    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
      // Файл успешно загружен
    } else {
      // Ошибка при перемещении файла
      echo "Ошибка при загрузке файла.";
    }
  }

  // Сохраняем данные в базу данных
  $image_value = isset($image_name) ? "'$image_name'" : "NULL";
  $insert_query = "INSERT INTO News (Title, Content, Author, Publication_Date, Category, image, read_time)
                   VALUES ('$title', '$content', '$author', NOW(), '$category', $image_value, '$read_time')";
  $result = mysqli_query($conn, $insert_query);
  if ($result) {
    // Вставка данных выполнена успешно
    header('Location: admin_panel.php');
    exit();
  } else {
    // Ошибка при выполнении запроса на вставку данных
    echo 'Ошибка при добавлении новости: ' . mysqli_error($conn);
  }
}
?>







