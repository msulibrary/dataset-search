<?php
error_reporting(E_ALL);

// Set title
$pageTitle = "MSU Dataset Search Edit Feed";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';

// Assign a class to assign layout to page - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Bring header code onto page
include '../meta/inc/header-admin.php';

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

?>

<script>
  function testFeed(feedUrl)
  {
	// Get content type
	var feedContentType = 'j';
	if (document.getElementById('xml').checked)
	{
		feedContentType = 'x';
	}

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
			var win = window.open("", "_blank");
			win.document.title = feedUrl;
			win.document.body.innerHTML = xhttp.responseText;
        }
    }
    xhttp.open("POST", "invokeCurl.php", true);

    // To retrieve post data via $_POST, content type must be sent as application/x-www-form-urlencoded
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send('feedUrl=' + encodeURI(feedUrl) + '&feedContentType=' + feedContentType);
  }
</script>

<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>

<?php

if (isset($_POST['submit'])):
// The item entry has been edited using the form.

// Declare form variables for submitting the data

// Check if the $id variable was passed from form, escape the string for mysql,
// and validate that it is a numeric value - pass id value to hidden form field
if (isset($_POST['id']) and is_numeric($_POST['id'])) {
	$id = strip_tags(mysql_real_escape_string((int)$_POST['id']));
}
else {
	echo 'Query type not supported.';
	exit;
}

$relatedItem_originInfo_feed_publisher = $_POST['publisher'];
$relatedItem_originInfo_feed_url = $_POST['url'];
$relatedItem_originInfo_feed_contentType = $_POST['content-type'];
$status = $_POST['status'];

//escape special characters for submission to database - convert HTML
// special characters in database value for use in an HTML document.
$relatedItem_originInfo_feed_publisher = addslashes($relatedItem_originInfo_feed_publisher);
$relatedItem_originInfo_feed_url = addslashes($relatedItem_originInfo_feed_url);

// Validate title, description fields as containing data
if ($relatedItem_originInfo_feed_publisher == '') {
	die('<p>You must enter a publisher for this feed. Click "Back" and try again.</p>');
}	
if ($relatedItem_originInfo_feed_url == '') {
	die('<p>You must enter a url for this feed. Click "Back" and try again.</p>');
}

$editFeed = "
	UPDATE feeds
	SET
		relatedItem_originInfo_feed_publisher = \"$relatedItem_originInfo_feed_publisher\",
		relatedItem_originInfo_feed_url = \"$relatedItem_originInfo_feed_url\",
		relatedItem_originInfo_feed_contentType = \"$relatedItem_originInfo_feed_contentType\",
		status = \"$status\"
	WHERE relatedItem_originInfo_feed_identifier = \"$id\"
";

if (!@mysql_query($editFeed)) {
	die("<h2>Error updating feed: " . mysql_error() . "</h2>");
}

// Clear form variables if page is refreshed to avoid reposting data to database
unset($_POST);
?>

<h2>The <?php echo stripslashes(html_entity_decode($relatedItem_originInfo_feed_publisher));?> feed was edited.</h2>
<h2><a href="./">Return to Manage MSU Dataset Search Admin Home</a></h2>

<?php

else:
// Show form and allow the user to edit an existing datasets metadata item

// Check if the $id variable was passed in url, escape the string for mysql,
// and validate that it is a numeric value - pass id value to hidden form field
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
	$id = strip_tags(mysql_real_escape_string((int)$_GET['id']));
}
else {
	echo 'Query type not supported.';
	exit;
}

// Run and hold query data for populating editing form
$getFeed = @mysql_query("SELECT * FROM feeds WHERE relatedItem_originInfo_feed_identifier = '$id'");

if (!$getFeed) {
	die("<h2>Error fetching feed details: " . mysql_error() . "</h2>");
}

// Store query result in an array
$row = mysql_fetch_object($getFeed);

// Define form variables for printing guide table data in editing form
$relatedItem_originInfo_feed_publisher = $row->relatedItem_originInfo_feed_publisher;
$relatedItem_originInfo_feed_url = $row->relatedItem_originInfo_feed_url;
$relatedItem_originInfo_feed_contentType = $row->relatedItem_originInfo_feed_contentType;
$status = $row->status;
?>

<form id="adminForm" action="<?php echo basename(__FILE__); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<fieldset>

<legend>MSU Dataset Search Feed</legend>

<h3><label for="publisher">Publisher</label></h3>
<input class="text" type="text" id="publisher" name="publisher" size="40" maxlength="200" value="<?php echo $relatedItem_originInfo_feed_publisher; ?>" />

<span>
<h3><label for="url">URL (start with http:// or https://)</label></h3>
<textarea class="text" type="text" id="url" name="url" rows="8" cols="20"><?php echo $relatedItem_originInfo_feed_url; ?></textarea>
<span class="button" style="vertical-align:top"><input class="submit" type="submit" onClick=testFeed(document.getElementById("url").value);return(false); value="Test Feed" /></span>
</span>

<fieldset class="feedInfo">
<legend>Response Content Type</legend>
<ul class="block">
<li><input type="radio" name="content-type" value="j" <? echo ($relatedItem_originInfo_feed_contentType == "j" ? "checked" : "");?>>&nbsp;&nbsp;JSON</li>
<li><input type="radio" id="xml" name="content-type" value="x" <? echo ($relatedItem_originInfo_feed_contentType == "x" ? "checked" : "");?>>&nbsp;&nbsp;XML</li>
</ul>
</fieldset>

<fieldset class="feedInfo">
<legend>Status</legend>
<ul class="block">
<li><input type="radio" name="status" value="a" <? echo ($status == "a" ? "checked" : "");?>>&nbsp;&nbsp;Active</li>
<li><input type="radio" name="status" value="i" <? echo ($status == "i" ? "checked" : "");?>>&nbsp;&nbsp;Inactive</li>
</ul>
</fieldset>

</fieldset>

<p class="button"><input class="submit" type="submit" name="submit" value="Save Feed" /></p>

</form>

<?php
endif; // End if/else statement allowing datasets to see edit form and submit it

// Bring footer code onto page
include '../meta/inc/footer-admin.php';
?>
