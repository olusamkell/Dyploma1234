<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
  header('location:login.php');
};

if (isset($_GET['logout'])) {
  unset($user_id);
  session_destroy();
  header('location:login.php');
}


if (isset($_GET['confirm'])) {
  // Здесь добавьте код для удаления пользователя из базы данных
  $delete_query = mysqli_query($conn, "DELETE FROM user_form WHERE id = '$user_id'") or die('delete query failed');
  // После удаления пользователя, разрушите сессию и перенаправьте пользователя на страницу login.php
  session_destroy();
  header('location:login.php');
}

if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
  $old_password = mysqli_real_escape_string($conn, md5($_POST['old_password']));
  $new_password = mysqli_real_escape_string($conn, md5($_POST['new_password']));
  $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm_password']));
  $user_id = $_SESSION['user_id']; // Предполагается, что вы храните ID пользователя в сессии

  // Проверка старого пароля
  $check_password_query = mysqli_query($conn, "SELECT * FROM user_form WHERE id = '$user_id' AND password = '$old_password'") or die('query failed');
  if (mysqli_num_rows($check_password_query) > 0) {
    if ($new_password === $confirm_password) {
      // Обновление пароля
      mysqli_query($conn, "UPDATE user_form SET password = '$new_password' WHERE id = '$user_id'") or die('query failed');
      echo "Пароль успешно изменен!";

      // Разрушение всех данных сессии
      $_SESSION = array();
      session_destroy();

      // Перенаправление на страницу авторизации
      header("Location: login.php");
      exit;
    } else {
      echo "Новый пароль и его подтверждение не совпадают!";
    }
  } else {
    echo "Старый пароль введен неверно!";
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/profile.css">
  <title>Профиль пользователя</title>
</head>

<body>
  <?php
  if (isset($message)) {
    foreach ($message as $message) {
      echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
    }
  }
  ?>

  <div class="container">
    <div class="user-profile">
      <?php
      $select_user = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
      if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
      };
      ?>
      <a href="index.php" class="logopfp">
        <h2>Вершина</h2>
      </a>
      <br>
      <h3>Информация о пользователе:</h3>
      <br>
      <p>Логин: <span><?php echo $fetch_user['name']; ?></span> </p>
      <br>
      <p>Почта: <span><?php echo $fetch_user['email']; ?></span> </p>
      <br>
      <div class="changepass">
   <form action="uploadavatar.php" method="post" enctype="multipart/form-data">
      <input type="file" name="avatar">
      <input type="submit" value="Загрузить аватар">
   </form>
</div>
<br>
      <a href="profile.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Вы уверены что хотите выйти?')" class="delete-btn">Выйти</a>
      <br>
      <a href="profile.php?confirm=true" onclick="return confirm('Вы уверены что хотите удалить свой аккаунт?')" class="delete-btn">Удалить аккаунт</a>
      <div class="changepass">
        <form action="" method="post">
          <label for="old_password">Старый пароль:</label>
          <input type="password" id="old_password" name="old_password" required><br>

          <label for="new_password">Новый пароль:</label>
          <input type="password" id="new_password" name="new_password" required><br>

          <label for="confirm_password">Подтвердите новый пароль:</label>
          <input type="password" id="confirm_password" name="confirm_password" required><br>

          <input type="submit" value="Сменить пароль">
        </form>
        <a href="index.php"><input type="submit" value="Вернуться на главную"></a>
      </div>
    </div>
  </div>
</body>

</html>