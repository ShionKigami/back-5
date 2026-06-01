<?php
header('Content-Type: text/html; charset=UTF-8');

$user = 'u82197';
$pass = '6410666';
$db = new PDO('mysql:host=localhost;dbname=u82197', $user, $pass, [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$session_started = false;
if (!empty($_COOKIE[session_name()]) && session_start()) {
    $session_started = true;
    if (!empty($_SESSION['login'])) {
        header('Location: ./');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Вход в систему</title>
        <style>
            .error-message { color: red; margin-top: 10px; }
        </style>
    </head>
    <body>
        <h2>Вход в личный кабинет</h2>
        <form action="" method="post">
            <label for="login">Логин:</label>
            <input name="login" id="login" type="text" required /><br/><br/>
            <label for="pass">Пароль:</label>
            <input name="pass" id="pass" type="password" required /><br/><br/>
            <input type="submit" value="Войти" />
        </form>
        <?php
        if (!empty($_COOKIE['login_error'])) {
            echo '<p class="error-message">Неверный логин или пароль.</p>';
            setcookie('login_error', '', 100000); 
        }
        ?>
        <br/>
        <a href="index.php">На главную</a>
    </body>
    </html>
    <?php
    exit();
}
else {
    $login_input = $_POST['login'] ?? '';
    $pass_input = $_POST['pass'] ?? '';

    if (empty($login_input) || empty($pass_input)) {
       
        setcookie('login_error', '1', time() + 24 * 60 * 60);
        header('Location: login.php');
        exit();
    }

    try {
     
        $stmt = $db->prepare("SELECT id, login, pass_hash FROM users WHERE login = ?");
        $stmt->execute([$login_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($user && md5($pass_input) === $user['pass_hash']) {
       
            if (!$session_started) {
                session_start();
            }
            $_SESSION['login'] = $user['login'];
            $_SESSION['uid'] = $user['id'];

            header('Location: index.php');
            exit();
        } else {
            
            setcookie('login_error', '1', time() + 24 * 60 * 60);
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
}
?>
