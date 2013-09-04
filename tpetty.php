<?php
$serverName = "ec2-107-21-191-61.compute-1.amazonaws.com"; //serverName\instanceName
$connectionInfo = array( "Database"=>"roireseachDEV", "UID"=>"roiadmin", "PWD"=>"Fantas1a");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

$sql = "
	DROP TABLE UsersAttributes
	DROP TABLE UsersOfPractice
	DELETE FROM Users WHERE LastActivityDate <= '01/01/2000'
	DELETE FROM Memberships WHERE LastActivityDate <= '01/01/2000'
";
$params = array(
	'tbl_Practice0001'
);
$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME like 'tbl_Practice0%';";
$stmt = sqlsrv_query( $conn, $sql, $params, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
if ($stmt === FALSE) die ('drap');
$row_count = sqlsrv_num_rows( $stmt );
if ($row_count === FALSE) die('crap');

for (var i in $row_count) {
	echo "Row " . $i;
	if( sqlsrv_fetch( $stmt ) === false) {
		die( print_r( sqlsrv_errors(), true));
	}
	$name = sqlsrv_get_field( $stmt, 0);
	echo "$name: ";
}
echo "done";
?>
