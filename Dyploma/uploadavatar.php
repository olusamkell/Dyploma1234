<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
  header('location:login.php');
  exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_FILES['avatar']) || $_FILES['avatar']['size'] == 0) {
    echo '<script>alert("Ошибка: Вы не загрузили аватар"); window.location.href = "profile.php";</script>';
  } else {
    $file_name = $_FILES['avatar']['name'];
    $file_tmp = $_FILES['avatar']['tmp_name'];
    $file_ext = strtolower(end(explode('.', $file_name)));
    $extensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $extensions)) {
      $avatar = $user_id . '.' . $file_ext;
      move_uploaded_file($file_tmp, 'image/' . $avatar);

      // Сохраняем ссылку на аватар в базу данных
      include 'connect.php';
      $update_avatar_query = mysqli_query($conn, "UPDATE user_form SET avatar = '$avatar' WHERE id = '$user_id'");
      if ($update_avatar_query) {
        // Обновляем данные пользователя
        $_SESSION['avatar'] = $avatar;

        // Редирект на профиль
        header('location: profile.php');
        exit;
      } else {
        echo "Ошибка при загрузке аватара";
      }
    } else {
      echo "Разрешены только jpeg, jpg и png файлы";
    }
  }
}


?>