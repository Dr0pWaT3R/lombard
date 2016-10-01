<?php 

$compList = array();
function CompanyList($conn) {

    $lstQuery = "SELECT company.id, company.name, company.phone AS compPhone, company.address, 
        company.createdAt, company.expirDate, company.updatedAt, 
        user.firstname, user.lastname, user.phone AS userPhone, user.gender, user.email, user.password 
        FROM `user` LEFT JOIN company on company.id =
        user.companyID AND user.role = 'admin'";
    $result = $conn->query($lstQuery);

    while($row = $result->fetch_assoc()){
        $compList[] = $row;
    }

    return $compList;

}

$empList = array();
function EmployeeList($conn, $compID) {

    $query = "SELECT * FROM user WHERE companyID = '".$compID."'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()){
        $empList[] = $row;
    }

    return $empList;
    
}

$compInfo = array();
function CompanyInfo($conn){

    #$query = "SELECT user.email, user.role FROM company LEFT JOIN user ON company.id=user.companyID 
    #                WHERE company.expirDate > CURDATE() AND email='".$email."' AND password='".$password."'";

}
