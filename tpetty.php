<?php
$serverName = "ec2-107-21-191-61.compute-1.amazonaws.com"; //serverName\instanceName
$connectionInfo = array( "Database"=>"roireseachDEV", "UID"=>"roiadmin", "PWD"=>"Fantas1a");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn ) {
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

//MSSQL Queries Here
$sql = array(
	//Run one time
	'init' => "
		DROP TABLE UsersAttributes
		DROP TABLE UsersOfPractice
		DELETE FROM Users WHERE LastActivityDate <= '01/01/2000'
		DELETE FROM Memberships WHERE LastActivityDate <= '01/01/2000'
		-- CREATE TABLE UsersOfPractice (uopID INT IDENTITY(1,1) PRIMARY KEY, PracticeID INT, UserID UNIQUEIDENTIFIER)
		-- CREATE TABLE UsersAttributes(UserID UNIQUEIDENTIFIER, UserAttributeName NVARCHAR(255), UserAttributeValue NVARCHAR(500))
	",
	//Get data to iterate over
	'iterator' => "
		SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME like 'tbl_Practice0%';
	",
	//Run each iteration
	'each' => "
	",
);
$params = array(
	'iterator' => array(),
	'init' => array(),
	'each' => array(),
);

//Run init sql
$stmt = sqlsrv_query( $conn, $sql['init'], $params['init']);
if ($stmt === FALSE) {
	die ('Error running init sql.');
}

//Get data to iterate over
$stmt = sqlsrv_query( $conn, $sql['iterator'], $params['iterator'], array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
if ($stmt === FALSE) {
	die ('Error running iterator sql.');
}
$row_count = sqlsrv_num_rows( $stmt );

//Iterate over each item and execute 'each' sql on it
for ($i = 0; $i < $row_count; $i++) {
	echo "Row " . $i . "\n";
	if( sqlsrv_fetch( $stmt ) === false) {
		die( print_r( sqlsrv_errors(), true));
	}
	$name = sqlsrv_get_field( $stmt, 0);
	echo "$name\n";
}
echo "done";
?>
