<?php 

$compList = array();
function CompanyList($conn) {

    $lstQuery = "SELECT company.id, company.name, email, company.phone AS compPhone, 
        company.address, company.createdAt, company.expirDate, company.updatedAt, 
        employee.firstname, employee.lastname, employee.phone AS empPhone, employee.role FROM `employee` 
        LEFT JOIN company on company.id = employee.companyID WHERE employee.role = 'admin'";
    $result = $conn->query($lstQuery);

    while($row = $result->fetch_assoc()){
        $compList[] = $row;
    }

    return $compList;

}

$empList = array();
function EmployeeList($conn, $compID) {

    $query = "SELECT * FROM employee WHERE companyID = '".$compID."'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()){
        $empList[] = $row;
    }

    return $empList;
    
}

$invoiceList = array();
function InvoiceList($conn, $compID){

    $query = "SELECT client.firstname, client.lastname, client.phone, material.id, 
        material.invoiceID, material.name, material.number, material.loanMoney, material.createdAt, material.expiredAt 
        FROM client LEFT JOIN material ON client.id=material.clientID WHERE material.companyID = '".$compID."'";

    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
        $invoiceList[] = $row;
    }
    
    return $invoiceList;

}
