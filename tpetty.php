<?php
$serverName = "ec2-107-21-191-61.compute-1.amazonaws.com"; //serverName\instanceName
$connectionInfo = array( "Database"=>"roireseachDEV", "UID"=>"roiadmin", "PWD"=>"Fantas1a");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( !$conn ) {
     echo "Connection could not be established.\n";
     printErrors();
}

//MSSQL Queries Here
$sql = array(
	//Run one time
	'init' => "
		DROP TABLE UsersAttributes
		DROP TABLE UsersOfPractice
		-- DELETE FROM Users WHERE LastActivityDate <= '01/01/2000'
		-- DELETE FROM Memberships WHERE LastActivityDate <= '01/01/2000'
		CREATE TABLE UsersOfPractice (uopID INT IDENTITY(1,1) PRIMARY KEY, PracticeID INT, UserID UNIQUEIDENTIFIER)
		CREATE TABLE UsersAttributes(UserID UNIQUEIDENTIFIER, UserAttributeName NVARCHAR(255), UserAttributeValue NVARCHAR(500))
	",
	//Get data to iterate over
	'iterator' => "
		SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME like 'tbl_Practice0%';
	",
	//Run each iteration
	'each' => "
		SELECT * FROM :{itval} WHERE id=':{cow}' AND id=':{itval}';
	",
);
$params = array(
	'iterator' => array(),
	'init' => array(),
	'each' => array('cow' => 1),
);

//Run init sql
$stmt = sqlsrv_query( $conn, $sql['init'], $params['init']);
if ($stmt === FALSE) {
	echo("Error running init sql.\n");
	printErrors();
}

//Get data to iterate over
$stmt = sqlsrv_query( $conn, $sql['iterator'], $params['iterator'], array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
if ($stmt === FALSE) {
	echo("Error running iterator sql.\n");
	printErrors();
}
$row_count = sqlsrv_num_rows( $stmt );

//Iterate over each item and execute 'each' sql on it
for ($i = 0; $i < $row_count; $i++) {
	//Get iterator value
	if( sqlsrv_fetch( $stmt ) === false) {
		echo("Unable to fetch row.\n");
		printErrors();
	}
	$val = sqlsrv_get_field( $stmt, 0);
	$params['each']['itval'] = $val;
	//Replace all named placeholders with array values
	$lsql = $sql['each'];
	echo "original sql: " . $lsql . "\n";
	foreach ($params['each'] as $pkey=>$pval) {
		$lsql = preg_replace("/:\{" . $pkey . "\}/g", $pval, $lsql);
		echo "Modified sql: " . $lsql . "\n";
	}
	echo("Final sql: " . $lsql . "\n");
	$stmt2 = sqlsrv_query( $conn, $lsql);
	if ($stmt2 === FALSE) {
		echo("Error running 'each' sql.\n");
		echo("Tried to execute:\n" . $lsql . "\n");
		printErrors();
	}
	unset($params['each']['itval']);
}
echo "done";

function printErrors(){
	if( ($errors = sqlsrv_errors() ) != null) {
		foreach( $errors as $error ) {
			echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
			echo "code: ".$error[ 'code']."\n";
			echo "message: ".$error[ 'message']."\n";
		}
	}
	die();
}
?>
