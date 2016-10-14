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

        }elseif(isset($_GET['toTenderAndZarlagadah'])){

            $array = $_SESSION['lmAuth'];
            $compID = $array['compID'];
            $value = $_POST['value'];
            $invoiceId = $_POST['invoiceId'];
            $type = $_POST['type'];
            $money = $_POST['money'];
            $date = date('Y-m-d H:i:s');

            $query = "UPDATE material SET mode='0', invoicePrice = '1', updatedAt='".$date."' WHERE id='".$invoiceId."'";
            if($conn->query($query)){
                $query = "INSERT INTO transaction (companyID, value, type, date, invoiceID, money, notepad) 
                    VALUES('".$compID."', '".$value."', '".$type."', '".$date."', '".$invoiceId."', '".$money."', '')";
                if($conn->query($query)){

                    $template = $twig->loadTemplate('invoiceList.html');
                    echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                        'invoiceList' => InvoiceList($conn, $compID), 'section' => 'material', 'year' => date('Y')));

                }
            }


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

            $chckQuery = "SELECT * FROM employee WHERE email = '".$email."'";
            $result = $conn->query($chckQuery);
            if($result->num_rows > 0) {

                $template = $twig->loadTemplate('registerCompany.html');
                echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                    'section' => 'registerComp', 'notify' => 'warning', 'message' => 'Бүртгэлтэй хэрэглэгч', 'year' => date('Y')));

            }else{

                $query = "INSERT INTO company (name, phone, address, expirDate, createdAt,
                    updatedAt) VALUES ('".$compName."', '".$compPhone."',
                    '".$compAddress."', adddate(curdate(), INTERVAL '".$expire."' day), '".$date."', '".$date."')";

                if($conn->query($query)) {

                    $compID = $conn->insert_id;
                    $query = "INSERT INTO employee (firstname, lastname, gender,
                        phone, email, role, password, companyID, createdAt,
                        updatedAt) VALUES ('".$firstname."', '".$lastname."',
                        '".$gender."', '".$phone."', '".$email."', '".$role."',
                        '".$password."', '".$compID."', '".$date."', '".$date."')";
                    if($conn->query($query)){

                        $template = $twig->loadTemplate('companies.html');
                        echo $template->render(array('title' => 'Систем админ', 'systemLoged' => $_SESSION['lmAuth'], 
                            'section' => 'company', 'notify' => 'success',
                            'message' => 'Компани бүртгэлээ', 'compList' => CompanyList($conn), 'year' => date('Y')));
                    }

                }

            } #else end
            
        #companyName request end
        }elseif(isset($_GET['employee'])) { 

            $array = $_SESSION['lmAuth'];
            $compID = $array['compID'];

            $template = $twig->loadTemplate('employee.html');
            echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                'employeeList' => EmployeeList($conn, $compID), 
                'section' => 'employee', 'year' => date('Y')));

        }elseif(isset($_GET['invoiceRegister'])) {

            $template = $twig->loadTemplate('invoiceRegister.html');
            echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                'section' => 'invoiceRegister', 'year' => date('Y-m-d')));


        }elseif(isset($_GET['invoiceList'])) {

            $array = $_SESSION['lmAuth'];
            $compID = $array['compID'];

            $template = $twig->loadTemplate('invoiceList.html');
            echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                'invoiceList' => InvoiceList($conn, $compID), 'section' => 'material', 'year' => date('Y')));

        }elseif(isset($_POST['invoiceID'])) {

            $array = $_SESSION['lmAuth'];
            $compID = $array['compID'];
            $empID= $array['empId'];

            $fName = $_POST['firstname'];
            $lName = $_POST['lastname'];
            $phone = $_POST['phone'];
            $rd = $_POST['register'];
            $address = $_POST['address'];

            $invoiceID = $_POST['invoiceID'];
            $mName = $_POST['materialName'];
            $number = $_POST['number'];
            $gramm = $_POST['gramm'];
            $carat = $_POST['carat'];
            $sign = $_POST['shinjTemdeg'];
            $fQuotation = $_POST['anhniiUnelgee'];
            $mType = $_POST['materialType'];
            $expiredAt = $_POST['expiredAt'];
            $interest = $_POST['interest'];
            $loanMoney = $_POST['loanMoney'];
            $invPrice = $_POST['invoicePrice'];

            $query = "INSERT INTO client(companyID, firstname, lastname, registerNumber, address, phone, createdAt, updatedAt) 
                VALUES ('".$compID."', '".$fName."', '".$lName."', '".$rd."', '".$address."', '".$phone."',  
                '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
            #echo $query;

            if($conn->query($query)) {
                #echo "end orj irsen shd";

                $clientID = $conn->insert_id;
                $query = "INSERT INTO material(invoiceID, clientID, empID, companyID, name, number, gramm, carat, 
                        shinjTemdeg, anhnii_unelgee, materialType, interest, loanMoney, invoicePrice, mode, 
                        expiredAt, createdAt, updatedAt) VALUES('".$invoiceID."', '".$clientID."', '".$empID."', '".$compID."', '".$mName."',
                        '".$number."', '".$gramm."', '".$carat."', '".$sign."', '".$fQuotation."', '".$mType."', '".$interest."',
                        '".$loanMoney."', '".$invPrice."', '1', '".$expiredAt."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
                #echo $query;

                if($conn->query($query)) {

                    $template = $twig->loadTemplate('invoiceList.html');
                    echo $template->render(array('title' => 'Ломбард Админ', 'systemLoged' => $_SESSION['lmAuth'], 
                        'invoiceList' => InvoiceList($conn, $compID), 'section' => 'invoiceList', 'year' => date('Y')));

                }

            }

        }elseif(isset($_POST['email'])) {

            $array = $_SESSION['lmAuth'];
            $compID = $array['compID'];
            $fName = $_POST['firstname'];
            $lName = $_POST['lastname'];
            $gender = $_POST['gender'];
            $role = $_POST['role'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $pass = $_POST['password'];
            $date = date('Y-m-d H:i:s');

            $query = "INSERT INTO employee (firstname, lastname, gender, phone,
                email, role, password, companyID, createdAt, updatedAt) VALUES
                ('".$fName."', '".$lName."', '".$gender."', '".$phone."',
                '".$email."', '".$role."', '".$pass."', '".$compID."',
                '".$date."', '".$date."')";

            if($conn->query($query)){

                $template = $twig->loadTemplate('employee.html');
                echo $template->render(array('title' => 'Ломбард Админ',
                    'employeeList' => EmployeeList($conn, $compID), 'notify' => 'success',
                    'message' => 'Ажилтан бүртгэлээ', 'systemLoged' => $_SESSION['lmAuth'], 
                    'section' => 'employee', 'year' => date('Y')));

            }else{

                $template = $twig->loadTemplate('employee.html');
                echo $template->render(array('title' => 'Ломбард Админ',
                    'employeeList' => EmployeeList($conn, $compID), 'notify' => 'warning',
                    'message' => 'email бүртгэлтэй байна', 'systemLoged' => $_SESSION['lmAuth'], 
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

                $query = "SELECT employee.id AS empId, company.id AS compID, employee.email, employee.role 
                    FROM company LEFT JOIN employee ON company.id=employee.companyID 
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
                        echo "ajiltan template";
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
