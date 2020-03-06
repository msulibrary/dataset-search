<?php

// Set title
$pageTitle = "MSU Dataset Search Add Feed";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';

// Assign a class to assign layout to page - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Bring header code onto page
include '../meta/inc/header-admin.php';

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

?>
<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>
<?php

if (isset($_POST['submit'])):
// The item entry has been edited using the form.

// Declare form variables for submitting the data
$relatedItem_originInfo_feed_publisher = $_POST['publisher'];
$relatedItem_originInfo_feed_url = $_POST['url'];
$relatedItem_originInfo_feed_content_type = $_POST['content-type'];
$status = $_POST['status'];

// Escape special characters for submission to database - convert HTML
// special characters in database value for use in an HTML document.

$relatedItem_originInfo_feed_publisher = addslashes($relatedItem_originInfo_feed_publisher);
$relatedItem_originInfo_feed_url = addslashes($relatedItem_originInfo_feed_url);

// Validate title, description fields as containing data
if ($relatedItem_originInfo_feed_publisher == '')
{
	die('<p>You must enter a publisher for this feed. Click "Back" and try again.</p>');
}
if ($relatedItem_originInfo_feed_url == '')
{
	die('<p>You must enter a url for this feed. Click "Back" and try again.</p>');
}

// Store SQL Query for inserting data into database
$addFeed = "
	INSERT INTO feeds
	SET
		relatedItem_originInfo_feed_publisher = \"$relatedItem_originInfo_feed_publisher\",
		relatedItem_originInfo_feed_url = \"$relatedItem_originInfo_feed_url\",
		status = \"$status\"
";

if (!@mysql_query($addFeed))
{
	die("<h2>Error adding feed: " . mysql_error() . "</h2>");
}
else
{
	echo('<h2>Feed was added successfully.</h2>');
}

// Clear form variables if page is refreshed to avoid reposting data to database
unset($_POST);

?>
<h2>A new feed with a publisher of "<?php echo stripslashes(html_entity_decode($relatedItem_originInfo_feed_publisher)); ?>" was added.</h2>
<h2><a href="./addFeed.php">Add another MSU Dataset Search feed</a></h2>
<h2><a href="./">Return to Manage MSU Dataset Search Home</a></h2>
<?php

else:
// Show form and allow the user to add a new feed

?>
<form id="adminForm" action="<?php echo basename(__FILE__); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<fieldset>

<legend>MSU Dataset Search Feed</legend>

<h3><label for="publisher">Publisher</label></h3>
<input class="text" type="text" id="publisher" name="publisher" size="40" maxlength="200" value="" />

<span>
<h3><label for="url">URL (start with http:// or https://)</label></h3>
<textarea class="text" type="text" id="url" name="url" rows="8" cols="20"></textarea>
<span class="button" style="vertical-align:top"><input class="submit" type="submit" onClick=window.open(document.getElementById("url").value);return(false); value="Test Feed" /></span>
</span>

<fieldset class="feedInfo">
<legend>Response Content Type</legend>
<ul class="block">
<li><input type="radio" name="content-type" value="j" checked>&nbsp;&nbsp;JSON</li>
<li><input type="radio" name="content-type" value="x">&nbsp;&nbsp;XML</li>
</ul>
</fieldset>

<fieldset class="feedInfo">
<legend>Status</legend>
<ul class='block'>
<li><input type='radio' name='status' value='a' checked>&nbsp;&nbsp;a = Active</li>
<li><input type='radio' name='status' value='i'>&nbsp;&nbsp;i = Inactive</li>
</ul>
</fieldset>

</fieldset>
<p class="button"><input class="submit" type="submit" name="submit" value="Save Feed" /></p>
</form>
<?php 

endif; // End if/else statement allowing users to see edit form and submit it

// Bring footer code onto page
include '../meta/inc/footer-admin.php';
?>
