<?php

    require_once 'config/dbconnection.php';

    $conn = getConnection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    mysqli_set_charset($conn, "utf8");

    Class Event{};
    $content = new Event();
    $content->success = false;
    $content->response = "";

    if(isset($_POST['table'])){

        $compID = $_POST['id'];
        $table = $_POST['table'];
        $query = "DELETE FROM ".$table." WHERE id='".$compID."'";
        echo ($query); #query hewlej uzsen console deer

        if($conn->query($query)) {
            $query = "DELETE FROM user WHERE id='".$compID."'";
            $conn->query($query);
            echo ($query); #query hewlej uzsen console deer
            $content->success = true;
            $content->response = "Компани устгагдалаа";
        }else{
            $content->success = false;
            $content->response = "";
        }

        echo json_encode($content);

    }

?>
