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


if(isset($_POST['add_to_cart'])){
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   // Проверка на роль гостя перед добавлением товара в корзину
   if ($_SESSION['role'] === 'guest') {
       $message[] = 'Добавление в корзину доступно только для зарегистрированных пользователей';
   } else {
       $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

       if(mysqli_num_rows($select_cart) > 0){
          $message[] = 'Товар уже добавлен в корзину!';
       } else {
          mysqli_query($conn, "INSERT INTO cart(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
          $message[] = 'Товар добавлен в корзину!';
       }
   }
}


if(isset($_POST['update_cart'])){
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'Количество товара успешно обновлено!';
}

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
   header('location:index.php');
}
  
if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:index.php');
}

?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productNames = document.querySelectorAll('.name');
        productNames.forEach(function (productName) {
            productName.addEventListener('click', function () {
                const description = this.getAttribute('data-description'); // Используем getAttribute для получения значения атрибута
                const modalDescription = document.getElementById('productDescription');
                modalDescription.innerHTML = description; // Заменяем textContent на innerHTML

                // Открываем модальное окно
                const modal = new bootstrap.Modal(document.getElementById('productModal'), {
                    keyboard: false
                });
                modal.show();
            });
        });
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/audit.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="topbtn.js"></script>
    <title>Финансы</title>
</head>
<body>
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
<div class="products">
   <h1 class="heading">Финансовые услуги</h1>

   <div class="for-message">       
<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?></div>

<!-- Модальное окно для описания товара -->
<div id="productModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Описание товара</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productDescription">
                <!-- Сюда будет загружено описание товара -->
            </div>
        </div>
    </div>
</div>

   <div class="box-container">

   <?php
      $select_product = mysqli_query($conn, "SELECT * FROM `products_finance`") or die('query failed');
      if(mysqli_num_rows($select_product) > 0){
         while($fetch_product = mysqli_fetch_assoc($select_product)){
   ?>
      <form method="post" class="box" action="">
         <img class="pic" src="/image/<?php echo $fetch_product['image']; ?>" alt="">
         <div class="name product-description-toggle" data-description="<?php echo $fetch_product['description']; ?>"><?php echo $fetch_product['name']; ?></div>
         <div class="price"><?php echo $fetch_product['price']; ?></div>
         <input type="number" class="quantity" min="1" max="100" name="product_quantity" value="1">
         <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
         <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
         <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
         <input type="submit" class="add-to-cart" value="Добавить в корзину" name="add_to_cart" class="btn">
      </form>
   <?php
      };
   };
   ?>
   </div>
    <footer>
      <div class="common-footer">
        <div class="internet-shop">
          <h3>Магазин</h3>
          <a href="">О нас</a>
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
    <input type="submit" class="send-btn" value="Отправить"> <!-- Добавляем кнопку отправки -->
</form>

<?php
if (isset($_GET['show_alert']) && $_GET['show_alert'] == 'true') {
    echo "<script>alert('Номер телефона успешно записан, мы с вами свяжемся!');</script>";
    echo '<script>window.history.replaceState({}, document.title, "' .$_SERVER['PHP_SELF'] . '");</script>';
}
    ?>  
        </div>
        </div>
      </footer>
</div>
</body>
</html>