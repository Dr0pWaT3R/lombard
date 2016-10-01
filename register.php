<?php
if (isset($_GET['register'])) {

    $template = $twig->loadTemplate('register.html');
    echo $template->render(array('title' => 'Бүртгүүлэх', 'year' => date('Y')));

}
elseif(isset($_POST['username'])){
    //print_r($_REQUEST);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $salbar = filter_var($_POST['salbar'], FILTER_SANITIZE_STRING);

    $pass = md5($pass);

    $date = date('Y-m-d H:i:s');

    $query = "INSERT INTO `users`(`username`,`email`, `password`,
        `salbar`, `createdAt`, `updatedAt`) VALUES ('".$username."',
        '".$email."', '".$pass."', '".$salbar."', '".$date."', '".$date."')";


    if ($conn->query($query)) {

        $_SESSION['auth'] = $_REQUEST;
        $template = $twig->loadTemplate('home.html');
        echo $template->render(array('title' => 'Нэвтрэх хуудас', 'year'
            => date('Y'), 'user' => $_SESSION['auth'], 'message' => 'Амжилттай бүргэлээ'));

    }
    else{

        $template = $twig->loadTemplate('register.html');
        echo $template->render(array('title' => 'Бүртгүүлэх', 'year'
            => date('Y'), 'message' => 'Amjiltgui'));
    }

}
?>
