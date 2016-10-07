<?php

    require_once 'config/dbconnection.php';

    $conn = getConnection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    Class Event{};
    $content = new Event();
    $content->success = false;
    $content->response = "";

    if(isset($_POST['table'])){

        $createdAt = $_POST['createdAt'];
        $expiredAt = $_POST['expiredAt'];
        $compID = $_POST['id'];
        $date = date('Y-m-d H:i:s');
        $query = "UPDATE  company  SET expirDate = adddate('".$createdAt."', INTERVAL '".$expiredAt."' day), 
                    updatedAt = '".$date."' WHERE id= '".$compID."'";
        #echo $query;
        mysqli_set_charset($conn, "utf8");

        if ($conn->query($query)) {

            $content->success = true;
            $content->response = "Мэдээлэл хадгалагдсан";

        }else{

            $content->success = false;
            $content->response = "";

        }

        echo json_encode($content);

    }

?>
