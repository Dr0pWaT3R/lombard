<?php

    session_start();

    require_once 'includes/Twig/Autoloader.php';
    require_once 'config/dbconnection.php';

    Twig_Autoloader::register();
    $loader = new Twig_Loader_Filesystem('templates');
    $twig = new Twig_Environment($loader, array(
        'cache' => 'cache',
    ));
    $twig->setCache(false);

    $conn = getConnection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    mysqli_set_charset($conn, "utf8");

    Class Event{};
    $content = new Event();
    $content->expiredAt = false;

    if(isset($_POST['table'])){

        if($_POST['table'] == 'company') {

            $table = $_POST['table'];
            $compId = $_POST['id'];

            $query = "SELECT name, createdAt, expirDate FROM
                ".$table." WHERE id=".$compId;
            #echo $query;

            $results = $conn->query($query);
            $date = date('Y-m-d');
            $data = array();
            while($row = $results->fetch_assoc()){

                $content->compName = $row['name'];
                $content-> createdAt = date('Y-m-d',strtotime($row['createdAt']));
                $content->expireDate = date('Y-m-d',strtotime($row['expirDate']));
                if($row['expirDate'] < $date) {
                    $content->expiredAt = true; #hugatsaa duusaagui
                    $content->todayDate = $date;
                }
                $data[] = $content;
            }

            header('Content-Type: application/json');
            echo json_encode($data);

        }else{

            $table = $_POST['table'];
            $id = $_POST['id'];
            $data = array();

            if($table == 'material') {

                $query = "SELECT material.id, client.firstname, client.lastname, client.phone, client.registerNumber, client.address, material.name,
                            material.number, material.gramm, material.carat, material.shinjTemdeg, material.anhnii_unelgee, material.loanMoney, 
                            material.interest, material.createdAt, material.expiredAt, (SELECT lastname FROM employee WHERE id=(SELECT empID FROM
                            material WHERE id ='2')) AS empID,
                            (SELECT TO_DAYS(material.expiredAt) - TO_DAYS(material.createdAt)) AS usedTime, invoicePrice,
                            (SELECT TO_DAYS(CURDATE())- TO_DAYS(material.expiredAt)) AS expiredDay FROM material LEFT JOIN client ON
                            client.id=material.clientID WHERE material.id = '".$id."'";
                #echo $query;

                $results = $conn->query($query);
                while($row = $results->fetch_assoc()){
                    $data[] = $row;
                }

            }else {

                $query = "SELECT * FROM
                    ".$table." WHERE id='".$id."'";
                #echo $query;

                $results = $conn->query($query);
                while($row = $results->fetch_assoc()){
                    $data[] = $row;
                }

            }

            header('Content-Type: application/json');
            echo json_encode($data);
        }
    #table request end
    }

?>
