<?php 
$serverName = "DESKTOP-U2HDM7J\SQLEXPRESS";
$connectionInfo = array("Database" => "votingsystemdb", "UID" => "sa", "PWD" => "12345678");

$conn = sqlsrv_connect($serverName, $connectionInfo);
if($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>