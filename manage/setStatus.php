<?php

/*
 * AJAX action to set the status of a record in the database
 */

// Connect to database
include_once '../meta/assets/dbconnect-admin.inc';

// Initialize status flag
$status = "success";

// Get POST data
$id       = isset($_POST['id'])       ? $_POST['id']       : '';
$recordId = isset($_POST['recordId']) ? $_POST['recordId'] : '';
$dbStatus = isset($_POST['dbStatus']) ? $_POST['dbStatus'] : 'u';

$update = "
	UPDATE datasets
	SET status = \"$dbStatus\"
	WHERE recordInfo_recordIdentifier = $recordId
";
	
if (@!mysql_query($update)) {
	$status = "update datasets failure";
}

echo json_encode(
	array(
		"id"     => $id,
		"status" => $status
	)
);

?>
