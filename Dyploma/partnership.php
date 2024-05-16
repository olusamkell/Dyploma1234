<?php
$session_cookies = 60 * 60; session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
  header('location:login.php');
};
  
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

if(isset($_GET['logout'])){
  unset($user_id);
  session_destroy();
  header('location:login.php');
}

    // Проверка подключения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/css/style.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="topbtn.js"></script>
    <link rel="stylesheet" href="css/comments.css">
    <title>Отзывы</title>
</head>
<div class="wrapper">
      <header class="shapka">
        <div class="logo">
          <a href="index.php"><h2>Вершина</h2></a>
          <button id = "myBtn" onclick="window.scrollTo({top: 0, left: 0, behavior: 'smooth'});"><img src="image/arrowup.png" alt=""></button>
        </div>
        <div class="nav-menu">
        <?php
        if (isAdmin()) {
          echo '<a href="admin_panel.php">Админ панель</a>';
        }
        ?>
        <a href="index.php">Главная</a>
        <a href="about.php">О нас</a>
        <a href="audit.php">Аудит</a>
        <a href="finance.php">Финансы</a>
        <a href="tax.php">Налоги</a>

        <a href="legal.php">Юридические</a>
        <a href="comments.php">Отзывы</a>
        <a href="news.php">Новости</a>
        <a href="partnership.php">Партнерская программа</a>
        <a href="orders.php"><img src="image/order_box.png" class="common-btn" alt=" " /></a>
        <a href="shoppingcart.php"><img src="image/korzina.png" class="common-btn" alt=" " /></a>
        <a href="profile.php"><img src="image/profile.png" class="common-btn" alt=" " /></a>
        <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Вы уверены?');" class="delete-btn">Выйти</a>
        </div>
      </header>
      <div class="container">
  <div class="user-profile">
  <?php
  $select_user = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die ('query failed');
  if(mysqli_num_rows($select_user) > 0){
    $fetch_user = mysqli_fetch_assoc($select_user);
  };
  ?>
          <div class="allincommon">
        <img src="image/<?php echo $fetch_user['avatar']; ?>" alt="Аватар пользователя" style="width: 50px; height: 50px;">
        <p>Логин: <span style="color:rgb(109, 108, 109); font-weight:bold;"><?php echo $fetch_user['name']; ?></span> </p>
        </div>
  </div>
</div>
    <h2>Если у вас появились вопросы,<br> напишите нам!</h2>
    <form action="submit_partner.php" method="post">
        <label for="name">Имя:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="phone">Номер телефона:</label><br>
        <input type="text" id="phone" name="phone" required><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="appeal">Обращение:</label><br>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
        <textarea id="appeal" name="appeal" rows="4" required></textarea><br><br>
        <input type="submit" value="Отправить">
    </form>
</div>
      <footer>
        <div class="common-footer">
          <div class="internet-shop">
            <h3>Магазин</h3>
            <a href="about.php">О нас</a>
            <a href="">FAQ</a>
            <a href="">Как заказать?</a>
          </div>
          <div class="company">
            <h3>Компания</h3>
            <a href="">Вакансии</a>
            <a href="">Контакты</a>
            <a href="">Партнерам</a>
          </div>
          <div class="customer-help">
            <form class="call-order" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              <h3>Заказать звонок</h3>
              <input type="text" name="phone_number" required placeholder="Введите номер телефона" title="Пожалуйста, введите только цифры" class="box-call">
              <br>
              <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
              <br>
              <input type="submit" class="send-btn" value="Отправить">
            </form>

            <?php
            if (isset($_GET['show_alert']) && $_GET['show_alert'] == 'true') {
              echo "<script>alert('Номер телефона успешно записан, мы с вами свяжемся!');</script>";
              echo '<script>window.history.replaceState({}, document.title, "' . $_SERVER['PHP_SELF'] . '");</script>';
            }
            ?>
          </div>
        </div>
      </footer>
</div>
</body>
</html>