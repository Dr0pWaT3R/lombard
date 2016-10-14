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

        if (isset($_POST['column'])) {

            foreach ($_REQUEST as $name => $value) {
                $_POST[$name] = $conn->real_escape_string($value);
            }

            $check = false;
            $query = "UPDATE `".$_POST['table']."` SET ";

            $postField = array();

            $fields = ['id', 'createdAt', 'updatedAt', 'column', 'table'];

            foreach ($_REQUEST as $name => $value) {

                $result = array_search($name, $fields);
                //              echo gettype($result). ": ". $name;
                if ("boolean" === gettype($result)) {

                    $check = true;
                    $query .= $name."='".$value."',";

                }

            }

            mysqli_set_charset($conn, "utf8");

            if ($check) {

                $query = substr( $query, 0, -1 );
                $query .=" WHERE id=".$_POST['id'];
                #echo $query;

                if ($conn->query($query)) {

                    $content->success = true;
                    $content->response = "Мэдээлэл хадгалагдсан";

                }else{

                    $content->success = false;
                    $content->response = "";

                }
            }
        }

        echo json_encode($content);

    }

?>
