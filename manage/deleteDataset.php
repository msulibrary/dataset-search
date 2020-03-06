<?php
// Set title
$pageTitle = "MSU Dataset Search Delete Object";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';

// Assign a class to assign layout to page - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Bring header code onto page
include '../meta/inc/header-admin.php';

// Get database parameters and connect to database
include '../meta/assets/dbconnect-admin.inc';

?>

<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>

<?php

// Logic based on hidden submit_check field in form
if (isset($_POST['submit_check'])) {
	process_form();
}
else {
	show_form();
}

// Do something when form is submitted
function process_form() {

	// Declare the variable for if/else control structures below
	$delete = $_REQUEST['delete'];

	// Get url of referer page
//	$referer = $_REQUEST['referer'];

	if ($delete == 'yes') {

		$id = $_POST['id'];

		// First delete affiliations, then delete creators, and finally delete the datasets entry

		// Set error flag to empty string
		$error = "";

		// Get creator_keys from creators table
		if (!($nameKeys =
			@mysql_query("SELECT creator_key FROM creators WHERE recordInfo_recordIdentifier = '$id'"))) {
			$error = mysql_error();
		}

		// Use creator_keys to delete affiliations
		if ($error == "") {
			while ($row = mysql_fetch_object($nameKeys)) {
				if (!@mysql_query("DELETE FROM affiliations WHERE creator_key = '" . $row->creator_key . "';")) {
					$error = mysql_error();
					break;
				}
			}
		}

		// Use recordInfo_recordIdentifier to delete creators
		if ($error == "") {
			if (!@mysql_query("DELETE FROM creators WHERE recordInfo_recordIdentifier = '$id';")) {
				$error = mysql_error();
			}
		}

		// Delete entry from datasets table
		if ($error == "") {
			if (@mysql_query("DELETE FROM datasets WHERE recordInfo_recordIdentifier='$id'")) {
		  		echo '<h2>Metadata Item deleted successfully!</h2>'."\n";
		  		echo '<h2><a href="./">Return to Manage MSU Dataset Search Home</a></h2>'."\n";
			}
			else {
				$error = mysql_error();
			}
		}

		if ($error != "") {
			echo "<h2>Error deleting metadata item from database!<br /> Error: $error</h2>\n";
			echo "<h2><a href=\"./\">Return to Manage MSU Dataset Search Home</a></h2>\n";
		}
	}
	else {
		echo "<h2>Metadata Item was not deleted.</h2>\n";
//		echo "<h2><a href=\"./\">Return to Manage MSU Dataset Search Home</a></h2>\n";

//		if ($referer != null) {
//			echo "<h2><a href='$referer'>Go back to previous page</a></h2>\n";
//		}

		// This method preserves input that may have occurred before the delete button was clicked
		echo "<h2><a href='#' onClick='window.history.go(-2)'>Go back</a></h2>\n";
	}
}

// Display the form
function show_form() {

	// Get id value from url
	$id = $_GET['id'];

	// Get referer url
//	$referer = htmlspecialchars($_SERVER['HTTP_REFERER']);

	// Query datasets table for dataset_name associated with id
	$getMetadata = @mysql_query("SELECT dataset_name FROM datasets WHERE recordInfo_recordIdentifier='$id'");
	if (!$getMetadata) {
		die("<h2>Error fetching item details: " . mysql_error() . "</h2>");
	}
	// Store resource data in an array
	$row = mysql_fetch_array($getMetadata);
	$dataset_name = stripslashes($row['dataset_name']);

	// Print out html form block
	echo "<h2>Are you sure you want to delete <strong>$dataset_name</strong>?</h2>\n";
	echo "<form id=\"adminForm\" action=\"" . basename(__FILE__) . "\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
	echo "<input type=\"hidden\" name=\"submit_check\" value=\"1\" />\n";
//	echo "<input type=\"hidden\" name=\"referer\" value=\"$referer\" />\n";
	echo "<fieldset>\n";
	echo "<h3><label>Yes, get rid of it.</label></h3>\n";
	echo "<input name=\"delete\" type=\"radio\" value=\"yes\">\n";
	echo "<h3><label>No, don't do it.</label></h3>\n";
	echo "<input name=\"delete\" type=\"radio\" value=\"no\">\n";
	echo "</fieldset>\n";
	echo "<p class=\"button\"><input class=\"submit\" type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" /></p>\n";
	echo "</form>\n";
}
//bring footer code onto page
include '../meta/inc/footer-admin.php';
?>
