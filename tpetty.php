<?php
$serverName = "ec2-107-21-191-61.compute-1.amazonaws.com"; //serverName\instanceName
$connectionInfo = array( "Database"=>"roireseachDEV", "UID"=>"roiadmin", "PWD"=>"Fantas1a");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

$sql = "
	DROP VIEW ?
	DROP TABLE UsersAttributes
	DROP TABLE UsersOfPractice
	DELETE FROM Users WHERE LastActivityDate <= '01/01/2000'
	DELETE FROM Memberships WHERE LastActivityDate <= '01/01/2000'
";
$params = array(
	'tbl_Practice0001'
);
$stmt = sqlsrv_query( $conn, $sql, $params);
if( $stmt === false ) {
     die( print_r( sqlsrv_errors(), true));
}
echo "done";

// Initialize parameters and prepare the statement.
// Variables $qty and $id are bound to the statement, $stmt.
/*
$qty = 0; $id = 0;
$stmt = sqlsrv_prepare( $conn, $sql, array( &$qty, &$id));
if( !$stmt ) {
    die( print_r( sqlsrv_errors(), true));
}

// Set up the SalesOrderDetailID and OrderQty information.
// This array maps the order ID to order quantity in key=>value pairs.
$orders = array( 1=>10, 2=>20, 3=>30);

// Execute the statement for each order.
foreach( $orders as $id => $qty) {
    // Because $id and $qty are bound to $stmt1, their updated
    // values are used with each execution of the statement.
    if( sqlsrv_execute( $stmt ) === false ) {
          die( print_r( sqlsrv_errors(), true));
    }
}
*/
?>
