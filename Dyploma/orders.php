<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
};

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE order_id = '$remove_id'") or die('query failed');
   header('location:orders.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png" />
   <link rel="icon" type="png" sizes="32x32" href="image/favicon-32x32.png" />
   <link rel="icon" type="png" sizes="16x16" href="image/favicon-16x16.png" />
   <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png" />
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=DM+Sans&family=Rubik:wght@300&family=Ubuntu:wght@300&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="css/shopcart.css">
   <title>Статус заказа</title>
</head>

<body>
   <div class="wrapper">
      <h1 class="heading">Статус услуги</h1>
      <div class="dlyalogo">
         <a class="logoback" href="index.php">
            <h2>Вершина</h2>
         </a>
      </div>

      <div class="shopping-cart">

         <div class="shopp-cart">
            <table class="main-table">
               <thead class="categories">
                  <th>Наименование</th>
                  <th>Номер заказа</th>
                  <th>Идентификатор пользователя</th>
                  <th>Конечная цена</th>
                  <th>Статус</th>
                  <th>Отмена заказа</th>
               </thead>
               <tbody>
                  <?php
                  $select_orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id'") or die(mysqli_error($conn));
                  if (mysqli_num_rows($select_orders) > 0) {
                     while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
                  ?>
                        <tr>
                           <td><?php echo $fetch_orders['products'] ?></td>
                           <td><?php echo $fetch_orders['order_id'] ?></td>
                           <td><?php echo $fetch_orders['user_id'] ?></td>
                           <td><?php echo $fetch_orders['total_amount'] ?></td>
                           <td><?php echo $fetch_orders['pending'] ?></td>
                           <td>
    <div class="tdt">
        <?php
        $orderStatus = $fetch_orders['pending'];
        $orderId = $fetch_orders['order_id'];

        if ($orderStatus !== 'Завершено' && $orderStatus !== 'Отказ') { 
            echo '<a href="orders.php?remove=' . $orderId . '" class="delete-btn" onclick="return confirm(\'Отменить заказ?\');">Отменить</a>';
        }
        ?>
    </div>
</td>
                        </tr> <?php
                           }
                        } else {  ?>
                     <tr>
                        <td colspan="6" style="text-align: center;">Нет заказов</td>
                     </tr> <?php
                        } ?>
               </tbody>
            </table>

         </div>
      </div>
   </div>
   </div>
</body>

</html>