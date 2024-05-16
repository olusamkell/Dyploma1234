<?php
$session_cookies = 60 * 60;
session_set_cookie_params($session_cookies);
session_start();

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

include 'connect.php';

// Если пользователь не авторизован и нажал кнопку "Войти как гость"
if (isset($_POST['guest'])) {
    // Добавьте любую логику для входа гостя здесь
    $_SESSION['role'] = 'guest';
    header('Location: index.php');
    exit();
}

// Проверка, если пользователь не авторизован и нажал кнопку "Войти как гость"
if (isset($_POST['submitt'])) {
    $role = 'guest'; // Значение роли для гостя

    $insert_query = "INSERT INTO user_form (email, password, name, role) VALUES ('', '', 'Guest', '$role')";
    $insert_result = mysqli_query($conn, $insert_query);

    if ($insert_result) {
        // Данные успешно добавлены
        $_SESSION['user_id'] = mysqli_insert_id($conn); // ID нового гостя
        $_SESSION['name'] = 'Guest';
        $_SESSION['role'] = $role;

        header('Location: index.php');
        exit();
    } else {
        // Обработка ошибки добавления данных
        $message[] = 'Ошибка при добавлении гостя в базу данных';
    }
}



if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

    if (mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);
        $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];

        if ($email === 'admin@gmail.com') {
            $_SESSION['role'] = 'admin';
        }

        header('location:index.php');
        exit();
    } else {
        $message[] = 'Неверный логин или почта!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/aith.css">
    <title>Авторизация</title>
</head>

<body>
    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
        }
    }
    ?>
    <div class="wrapper">

        <form class="login-passw" action="" method="post">
            <a class="logotip" href="index.php">
                <h2>Justice</h2>
            </a>
            <br>
            <h3>Авторизация</h3>
            <br>
            <label for="email">Почта:</label>
            <input type="email" name="email"  placeholder="Введите почту" class="box">
            <br>
            <label for="password">Пароль:</label>
            <input type="password" name="password"  placeholder="Введите пароль" class="box">
            <br>
            <input class="sub" type="submit" name="submit" class="btn" value="Войти">
            <br>
            <input class="sub" type="submit" name="submitt" class="btn" value="Войти как гость">
            <br>
            <p>Нет аккаунта? <a class="auth" href="register.php">Зарегистрироваться</a></p>
        </form>

    </div>
</body>

</html>