<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Проверка авторизации
if (!isAdmin()) {
  header('Location: login.php');
  exit();
}

// Обработка удаления пользователя
if (isset($_GET['delete_user'])) {
  $user_id = $_GET['delete_user'];
  $delete_query = "DELETE FROM user_form WHERE id = $user_id";
  mysqli_query($conn, $delete_query) or die('Ошибка при удалении пользователя');
  header('Location: admin_panel.php');
  exit();
}



// Получение списка пользователей
$select_users = mysqli_query($conn, "SELECT * FROM user_form") or die('Ошибка при получении пользователей');

$select_orders = mysqli_query($conn, "SELECT * FROM orders") or die('Ошибка при получении заявок');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="topbtn.js"></script>
  <link rel="stylesheet" href="css/adm_panel.css" />
</head>

<body>
  <div class="wrapper">
    <header class="shapka">
      <div class="logo">
        <a href="index.php">
          <h2>Вершина</h2>
        </a>
        <button id="myBtn" onclick="window.scrollTo({top: 0, left: 0, behavior: 'smooth'});"><img src="image/arrowup.png" alt=""></button>
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
        $select_user = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($select_user) > 0) {
          $fetch_user = mysqli_fetch_assoc($select_user);
        };
        ?>
        <div class="allincommon">
        <img src="image/<?php echo $fetch_user['avatar']; ?>" alt="Аватар пользователя" style="width: 50px; height: 50px;">
        <p>Логин: <span style="color:rgb(109, 108, 109); font-weight:bold;"><?php echo $fetch_user['name']; ?></span> </p>
        </div>
      </div>
      <div class="wrapper">
        <div class="container">
          <h1>Список пользователей</h1>

          <table border="1">
            <tr>
              <td class="table-cell">ID</td>
              <td class="table-cell">Email</td>
              <td class="table-cell">Действие</td>
            </tr>
            <?php while ($user = mysqli_fetch_assoc($select_users)) : ?>
              <tr>
                <td class="table-cell"><?php echo $user['id']; ?></td>
                <td class="table-cell"><?php echo $user['email']; ?></td>
                <td class="table-cell"><a href="admin_panel.php?delete_user=<?php echo $user['id']; ?>">Удалить</a></td>
              </tr>
            <?php endwhile; ?>
          </table>
    <h1 class = "order_status" >Статус заказов</h1>  
           </table>
<?php   
$order_query = "SELECT orders.order_id, user_form.name, orders.user_id, orders.products, orders.total_amount, orders.pending
                FROM orders
                INNER JOIN user_form ON orders.user_id = user_form.id";
$order_result = mysqli_query($conn, $order_query);

echo '<table border="1">';
echo '<tr><th class="table-cell">Order ID</th><th class="table-cell">User ID</th><th class="table-cell">User Name</th><th class="table-cell">Products</th><th class="table-cell">Total Amount</th><th>Action</th></tr>';

while ($order = mysqli_fetch_assoc($order_result)) {
    echo '<tr>';
    echo '<td class="table-cell">' . $order['order_id'] . '</td>';
    echo '<td class="table-cell">' . $order['user_id'] . '</td>';
    echo '<td class="table-cell">' . $order['name'] . '</td>';
    echo '<td class="table-cell">' . $order['products'] . '</td>';
    echo '<td class="table-cell">' . $order['total_amount'] . '</td>';
    echo '<td class="table-cell">
        <form method="post" action="update_order_status.php">
            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
            <select name="new_status">
                <option value="Новое" ' . ($order['pending'] == 'Новое' ? 'selected' : '') . '>Новое</option>
                <option value="Отказ" ' . ($order['pending'] == 'Отказ' ? 'selected' : '') . '>Отказ</option>
                <option value="Завершено" ' . ($order['pending'] == 'Завершено' ? 'selected' : '') . '>Завершено</option>
                <option value="Принято в работу" ' . ($order['pending'] == 'Принято в работу' ? 'selected' : '') . '>Принято в работу</option>
            </select>
            <input type="submit" value="Изменить">
        </form>
    </td>';
    echo '</tr>';
}

echo '</table>';
?>
  <h1>Добавить новость</h1>
<div class="containerr">

  <form method="post" action="add_news.php" enctype="multipart/form-data">
    <label for="title">Заголовок:</label><br>
    <input type="text" id="title" name="title" required><br>
    <label for="content">Содержание:</label><br>
    <textarea id="content" name="content" rows="4" required></textarea><br>
    <label for="author">Автор:</label><br>
    <input type="text" id="author" name="author" required><br>
    <label for="author">Время на прочтение:</label><br>
    <input type="text" id="read_time" name="read_time" required><br>
    <label for="category">Категория:</label><br>
    <input type="text" id="category" name="category" required>
    <br>
    <label for="image">Выберите изображение:</label><br>
    <input type="file" id="image" name="image" accept="image/*"><br>
    <br>
    <input type="submit" value="Опубликовать">
  </form>
</div>



        </div>
      </div>

      <body>
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

      </body>

</html>