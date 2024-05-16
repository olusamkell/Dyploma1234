<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
include 'connect.php';
session_start();
$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

if (!isset($user_id)) {
  header('location:login.php');
};

if (isset($_GET['logout'])) {
  unset($user_id);
  session_destroy();
  header('location:login.php');
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['phone_number'])) {
    // Защита от SQL-инъекций
    $phone = htmlspecialchars($_POST['phone_number']);

    // Вставка номера телефона в базу данных
    $query = "INSERT INTO call_order (user_id, phone_number) VALUES ('$user_id', '$phone')";

    if ($conn->query($query) === TRUE) {
      // Перенаправление с параметром для отображения алерта
      header('Location: ' . $_SERVER['PHP_SELF'] . '?show_alert=true');
      exit();
    } else {
      echo "Ошибка: " . $query . "<br>" . $conn->error;
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="topbtn.js"></script>
  <title>Justice</title>
</head>
    
<script>
    function getTimeOfDayMessage(username) {
        var hour = new Date().getHours();
        var greetingMessage = "";

        if (hour >= 6 && hour < 12) {
            greetingMessage = 'Доброе утро, ' + username + '!';
        } else if (hour >= 12 && hour < 18) {
            greetingMessage = 'Добрый день, ' + username + '!';
        } else {
            greetingMessage = 'Добрый вечер, ' + username + '!';
        }

        alert(greetingMessage);
    }

    // Получаем имя пользователя из PHP
    var username = "<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>";

    // Проверяем, было ли уже показано приветственное сообщение
    var greetingShown = sessionStorage.getItem('greetingShown');
    if (greetingShown !== 'true' && username.trim() !== '') {
        // Показываем приветственное сообщение
        getTimeOfDayMessage(username);
        // Записываем информацию о показе сообщения в SessionStorage
        sessionStorage.setItem('greetingShown', 'true');
    }
</script>

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
    </div>
    <div class="wrapper2">
 <main>
      <div class="preview">
        <div class="text-prev">
          <h1>Наслаждайся лучшим <br>выбором!</h1>
        </div>
        <div class="social-media-prev">
          <a href=""><img width="20px" height="30px" src="image/facebook.png" alt=""></a>
          <a href=""><img width="30px" height="30px" src="image/instagram.png" alt=""></a>
          <a href=""><img width="30px" height="30px" src="image/twitter.png" alt=""></a>
        </div>
        <div class="benefits-prev">
          <div class="first-prev">
            <h3>120,000 <br>Успешных сделок</h3>
          </div>
          <div class="second-prev">
            <h3>10,000 <br>Отзывов <img src="image/star.png"> (4.5)</h3>
          </div>
          <div class="third-prev">
            <h3>3 <br>Разных направлений</h3>
          </div>
        </div>
      </div>
      <div class="more-about-us">
        <h1>Узнай больше о нас</h1>
        <p>——— Информация о нас.</p>
      </div>
      <div class="justicepic">
        <div class="text-justice">
          <p class="text"><img class="pic-just" src="image/justicepic.png" alt="">В настоящее время компания Вершина является одной из крупнейших <br>
            юридических компаний в России. Она имеет офисы в Москве, Санкт-<br>
            Петербурге и других крупных городах страны. Компания <br>
            предоставляет широкий спектр юридических услуг, включая:<br>
            налоговое право, коммерческое право, трудовое право.</p>
          <a href="about.php" class="gradient-button">Узнать больше</a>
          <img width="200px" height="120px" class="insurance" src="image/ingostrax.png" alt="">
          <img width="200px" height="120px" class="insurance" src="image/rennessansstrahovanie.jpg" alt="">
          <img width="200px" height="120px" class="insurance" src="image/rosgosstrax.png" alt="">
        </div>
      </div>
      <div class="service">
        <h1>Мы предоставляем лучший сервис</h1>
        <p>——— Защита прав - это наша работа,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;а ваше спокойствие - наша цель!</p>
      </div>
      <div class="proofs">
        <div class="card1">
          <img src="image/Delivery.png" alt="">
          <h4 class="commonh">Быстрое оказание услуг</h4>
          <p class="commonp">Мы гарантируем быстрое<br> осуществление нашей <br>деятельности</p>
        </div>
        <div class="card2">
          <img src="image/Guarantee.png" alt="">
          <h4 class="commonh">100% Гарантия</h4>
          <p class="commonp">Мы предоставляем полноценную <br>гарантию </p>
        </div>
        <div class="card3">
          <img src="image/Quality.png" alt="">
          <h4 class="commonh">Лучшее качество услуг</h4>
          <p class="commonp">Качество было достигнуто <br>путем долгой работой нашей <br>компании.</p>
        </div>
      </div>
      <div class="comments">
        <h1>Что мы предлагаем?</h1>
        <p>——— Мы ценим отзыв каждого клиента и <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;всегда рады вам<br></p>
      </div>
      <h2 class="reviews-header">Наши скидки</h2>
      <div class="slider">
        <img class="slide" src="image/discount.png">
        <img class="slide" src="image/bonus-system.jpg">
        <img class="slide" src="image/firstorder.jpg">
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>

        <script>
          let slideIndex = 0;
          const slides = document.querySelectorAll(".slide");

          showSlide(slideIndex);

          function showSlide(index) {
            // сбрасываем все слайды
            for (let i = 0; i < slides.length; i++) {
              slides[i].classList.remove("active");
            }
            // устанавливаем нужный слайд
            slides[index].classList.add("active");
          }

          function nextSlide() {
            slideIndex++;
            if (slideIndex >= slides.length) {
              slideIndex = 0;
            }
            showSlide(slideIndex);
          }

          function prevSlide() {
            slideIndex--;
            if (slideIndex < 0) {
              slideIndex = slides.length - 1;
            }
            showSlide(slideIndex);
          }
        </script>

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
  </main>
    </div>
  </div>
</body>

</html>