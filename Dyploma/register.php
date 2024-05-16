<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
include 'connect.php';

if (isset($_POST['submit'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $message[] = 'Неверный формат почты!';
   } else if ($pass !== $cpass) {
      $message[] = 'Пароли не совпадают!';
   } else if (strlen($_POST['password']) < 8) {
      $message[] = 'Пароль должен быть не менее 8 символов!';
   } else {
      $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'") or die('query failed');
      if (mysqli_num_rows($select) > 0) {
         $message[] = 'Такой пользователь уже существует!';
      } else {
         mysqli_query($conn, "INSERT INTO user_form(name, email, password) VALUES('$name', '$email', '$pass')") or die('query failed');
         $message[] = 'Вы успешно зарегистрировались!';
         header('location:login.php');
      }
   }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="css/regg.css">
   <title>Регистрация</title>
</head>

<body>
   <?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo '<div class="message" onclick="this.remove();">' . $msg . '</div>';
      }
   }
   ?>
   <div class="wrapper">
      <form class="login-passw" action="" method="post">
         <a class="logotip" href="index.php">
            <h2>Justice</h2>
         </a>
         <br>
         <h3>Регистрация</h3>
         <br>
         <label for="name">Логин:</label>
         <input type="text" name="name" required placeholder="Введите логин" class="box">
         <br>
         <label for="email">Почта:</label>
         <input type="email" name="email" required placeholder="Введите почту" class="box">
         <br>
         <label for="password">Пароль:</label>
         <input type="password" name="password" required placeholder="Введите пароль" class="box">
         <br>
         <label for="cpassword">Подтвердите пароль:</label>
         <input type="password" name="cpassword" required placeholder="Подтвердите пароль" class="box">
         <br>
         <input class="sub" type="submit" name="submit" class="btn" value="Зарегистрироваться">
         <br>
         <p>Продолжить как гость? <a class = "continue" href="index.php">Продолжить</a></p>
         <br>
         <p>Уже есть аккаунт? <a class="login" href="login.php">Войти</a></p>
      </form>
   </div>
</body>

</html>