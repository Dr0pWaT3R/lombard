<?php

    session_start();

    require_once 'includes/Twig/Autoloader.php';
    require_once 'config/dbconnection.php';
    require_once 'functions.php';

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

    if (isset($_SESSION['lmAuth'])) {

        if (isset($_GET['logout'])) {

            session_unset();
            session_destroy();

            $template = $twig->loadTemplate('login.html');
            echo $template->render(array('title' => 'Нэвтрэх хуудас', 'year' => date('Y')));

        }elseif(isset($_GET['registerComp'])) {

            $template = $twig->loadTemplate('registerCompany.html');
            echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                'section' => 'registerComp', 'year' => date('Y')));

        }elseif(isset($_GET['companies'])){
            
            $template = $twig->loadTemplate('companies.html');
            echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                'section' => 'company', 'compList' => CompanyList($conn), 'year' => date('Y')));


        }elseif(isset($_POST['companyName'])) {

            $compName = $_POST['companyName'];
            $compPhone = $_POST['companyNumber'];
            $compAddress = $_POST['companyAddress'];
            $expire = $_POST['expire'];

            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $gender = $_POST['gender'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = $_POST['password'];
            $date = date('Y-m-d H:i:s');

            $chckQuery = "SELECT * FROM user WHERE email = '".$email."'";
            $result = $conn->query($chckQuery);
            if($result->num_rows > 0) {

                $template = $twig->loadTemplate('registerCompany.html');
                echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                    'section' => 'registerComp', 'message' => 'Бүртгэлтэй хэрэглэгч', 'year' => date('Y')));

            }else{

                $query = "INSERT INTO company (name, phone, address, expirDate, createdAt,
                    updatedAt) VALUES ('".$compName."', '".$compPhone."',
                    '".$compAddress."', adddate(curdate(), INTERVAL '".$expire."' day), '".$date."', '".$date."')";

                if($conn->query($query)) {

                    $compID = $conn->insert_id;
                    $query = "INSERT INTO user (firstname, lastname, gender,
                        phone, email, role, password, companyID, createdAt,
                        updatedAt) VALUES ('".$firstname."', '".$lastname."',
                        '".$gender."', '".$phone."', '".$email."', '".$role."',
                        '".$password."', '".$compID."', '".$date."', '".$date."')";
                    if($conn->query($query)){

                        $template = $twig->loadTemplate('companies.html');
                        echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                            'section' => 'company', 'compList' =>
                    CompanyList($conn), 'year' => date('Y')));
                    }

                }

            } #else end
            
        #companyName request end
        }elseif(isset($_GET['employee'])) { 

            $array = $_SESSION['lmAuth'];
            $compID = $array['id'];

            $template = $twig->loadTemplate('employee.html');
            echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                'employeeList' => EmployeeList($conn, $compID), 
                'section' => 'employee', 'year' => date('Y')));

        }elseif(isset($_POST['email'])) {

            $array = $_SESSION['lmAuth'];
            $compID = $array['id'];
            $fName = $_POST['firstname'];
            $lName = $_POST['lastname'];
            $gender = $_POST['gender'];
            $role = $_POST['role'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $pass = $_POST['password'];
            $date = date('Y-m-d H:i:s');

            $query = "INSERT INTO user(firstname, lastname, gender, phone,
                email, role, password, companyID, createdAt, updatedAt) VALUES
                ('".$fName."', '".$lName."', '".$gender."', '".$phone."',
                '".$email."', '".$role."', '".$pass."', '".$compID."',
                '".$date."', '".$date."')";

            if($conn->query($query)){
                $template = $twig->loadTemplate('employee.html');
                echo $template->render(array('title' => 'Ломбард Админ',
                    'employeeList' => EmployeeList($conn, $compID),
                    'message' => 'Ажилтан бүртгэлээ', 'systemLoged' => $_SESSION['lmAuth'], 
                    'section' => 'employee', 'year' => date('Y')));
            }
        #email request end
        } 
        else{

            $logged = $_SESSION['lmAuth'];
            #print_r ($_SESSION['lmAuth']); 
            if($logged['role'] == 'admin') {
                $template = $twig->loadTemplate('baseAdmin.html');
                echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                    'section' => 'company', 'year' => date('Y')));
            }else{
                $template = $twig->loadTemplate('companies.html');
                echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                    'compList' => CompanyList($conn), 'section' => 'company', 'year' => date('Y')));
            }

        } 
    #session auth end
    }else{ 

        if(isset($_POST['email'])){

            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            $query = "SELECT * FROM systemadmin WHERE email = '".$email."' AND password = '".$password."'";
            $result = $conn->query($query);
            #systemAdmin check
            if($result->num_rows > 0) {

                $row = $result->fetch_assoc();
                #echo $row['email'];
                $_SESSION['lmAuth'] = $row;

                $template = $twig->loadTemplate('companies.html');
                echo $template->render(array('title' => 'систем админ',
                    'systemLoged' => $_SESSION['lmAuth'], 'compList' =>
                    CompanyList($conn), 'section' => 'company', 'year' => date('Y')));

            }elseif(true){

                $query = "SELECT company.id, user.email, user.role FROM company LEFT JOIN user ON company.id=user.companyID 
                    WHERE company.expirDate > CURDATE() AND email='".$email."' AND password='".$password."'";
                $result = $conn->query($query);
                if($result->num_rows > 0) {

                    $row = $result->fetch_assoc();
                    $_SESSION['lmAuth'] = $row;

                    #echo $row['role'];
                    if($row['role'] == 'admin') {

                        $template = $twig->loadTemplate('baseAdmin.html');
                        echo $template->render(array('title' => 'Lombard админ',
                            'systemLoged' => $_SESSION['lmAuth'], 'section' => '', 'year' => date('Y')));

                    }else{
                        #ajiltan template hewleh
                    }

                }else {

                    $template = $twig->loadTemplate('login.html');
                    echo $template->render(array('title' => 'Нэвтрэх хуудас', 'message' => 'Мэйл эсвэл Нууц үг буруу',
                        'year' => date('Y')));
                }

            #else end
            }else {

                $template = $twig->loadTemplate('login.html');
                echo $template->render(array('title' => 'Нэвтрэх хуудас', 'message' => 'Мэйл эсвэл Нууц үг буруу',
                    'year' => date('Y')));
            }


        #email request(newtreh) end
        }else{

            $template = $twig->loadTemplate('login.html');
            echo $template->render(array('title' => 'Нэвтрэх хуудас', 'year' => date('Y')));

        }
    }

?>
