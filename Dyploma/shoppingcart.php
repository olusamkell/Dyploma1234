<?php
$session_cookies = 60 * 60; session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
  header('location:login.php');
};


if(isset($_POST['update_cart'])){
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'Количества товара успешно обновлено!';
}

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
   header('location:shoppingcart.php');
}
  
if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:shoppingcart.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png"/>
    <link rel="icon" type="png" sizes="32x32" href="image/favicon-32x32.png" />
    <link rel="icon" type="png" sizes="16x16" href="image/favicon-16x16.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&family=Rubik:wght@300&family=Ubuntu:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/shopcart.css">
    <title>Корзина услуг</title>
</head>
<body>
     <div class="wrapper">
<h1 class="heading">Корзина услуг</h1>
<div class="dlyalogo">
   <a class = "logoback" href="index.php"><h2>Вершина</h2></a>
</div>

<div class="shopping-cart">

   <div class="shopp-cart">
         <table class = "main-table">
      <thead class = "categories">
         <th>Изображение</th>
         <th>Наименование</th>
         <th>Цена</th>
         <th>Количество</th>
         <th>Конечная цена</th>
         <th>Действие</th>
      </thead>
      <tbody>
      <?php
         $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         $grand_total = 0;
         if(mysqli_num_rows($cart_query) > 0){
            while($fetch_cart = mysqli_fetch_assoc($cart_query)){
      ?>
         <tr>
            <td><div class = "tdt"><img src="image/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></div></td>
            <td ><div class = "tdt"><?php echo $fetch_cart['name']; ?></div></td>
            <td><div class = "tdt"><?php echo $fetch_cart['price']; ?> </div></td>
            <td><div class = "tdt">
               <form class = "update-form" action="" method="post">
                  <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                  <input type="number" class ="counter" min="1" max = "100" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                  <input type="submit" name="update_cart" value="Обновить" class="update-btn">
               </form></div>
            </td>
            <td><div class = "tdt"><?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?> ₽</div></td>
            <td><div class = "tdt"><a href="shoppingcart.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('Удалить товар из корзины?');">Удалить</a></div></td>
         </tr>
      <?php
         $grand_total += $sub_total;
            }
         }else{
            echo '<tr><td style=" padding:20px; text-align: center; text-transform:lowercase;" colspan="6">Ничего не добавлено</td></tr>';
         }
      ?>
      <tr class="table-bottom">
         <td colspan="4">Итоговая цена:</td>
         <td><div class = "tdt"><?php echo $grand_total; ?> ₽</div></td>
         <td><div class = "tdt"><a href="shoppingcart.php?delete_all" onclick="return confirm('Удалить все из корзины?');" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">Удалить все</a></div></td>
      </tr>
   </tbody>
   </table>

</div>
<script type="text/javascript">
  window.onload = function() {
    document.querySelector('.gradient-button').addEventListener('click', function(e) {
      if(<?php echo $grand_total; ?> <= 1){
        e.preventDefault(); 
        alert('Корзина пуста. Пожалуйста, добавьте товары в корзину перед выполнением оплаты.');
      }
    });
  }
</script>
   </div>
   <div class="cart-btn">
      <a href="payment_confirm.php"  class="gradient-button <?php echo ($grand_total > 1)?'':'disabled'; ?>">Выполнить оплату</a>
   </div>
</div>
</div>
</body>
</html>
