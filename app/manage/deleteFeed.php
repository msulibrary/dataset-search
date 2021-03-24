<?php
// Set title
$pageTitle = "MSU Dataset Search Delete Feed";

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

	// Make sure function has access to db $connection variable brought in with _db_props_admin.php

	if ($delete == 'yes') {

		$id = $_POST['id'];

		// Delete entry from feeds table
		if (@mysql_query("DELETE FROM feeds WHERE relatedItem_originInfo_feed_identifier = '$id'")) {
	  		echo '<h2>Feed deleted successfully!</h2>'."\n";
	  		echo '<h2><a href="./">Return to Manage MSU Dataset Search Home</a></h2>'."\n";
		}
		else {
			$error = mysql_error();
		}

		if ($error != "") {
			echo "<h2>Error deleting feed!<br /> Error: $error</h2>\n";
			echo "<h2><a href=\"./\">Return to Manage MSU Dataset Search Home</a></h2>\n";
		}
	}
	else {
	  echo "<h2>Feed was not deleted.</h2>\n";
	  echo "<h2><a href=\"./\">Return to Manage MSU Dataset Search Home</a></h2>\n";
	}
}

// Display the form
function show_form() {

	// Get id value from url
	$id = $_GET['id'];

	// Query feeds table for publisher associated with id
	$getPublisher = @mysql_query("SELECT relatedItem_originInfo_feed_publisher FROM feeds WHERE relatedItem_originInfo_feed_identifier = '$id'");
	if (!$getPublisher) {
		die("<h2>Error fetching feed details: " . mysql_error() . "</h2>");
	}
	// Store resource data in an array
	$row = mysql_fetch_object($getPublisher);
	$publisher = stripslashes($row->relatedItem_originInfo_feed_publisher);

	// Print out html form block
	echo "<h2>Are you sure you want to delete <strong>$publisher</strong>?</h2>\n";
	echo "<form id=\"adminForm\" action=\"" . basename(__FILE__) . "\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
	echo "<input type=\"hidden\" name=\"submit_check\" value=\"1\" />\n";
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
