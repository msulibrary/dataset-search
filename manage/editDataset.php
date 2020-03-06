<?php
error_reporting(E_ALL);

// Set title
$pageTitle = "MSU Dataset Search Edit Object";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';

// Assign a class to assign layout to page - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Use jQuery
$jQuery = true;

// Bring header code onto page
include '../meta/inc/header-admin.php';

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

// Get list of Google Scholar Categories
include './categories.php';

// CreatorData class
class CreatorData
{
	var $id;
	var $creatorKey;
	var $creator = array();
	var $affiliations = array();

	function CreatorData($id)
	{
		$this->id = $id;
	}

	function setCreatorName($creatorNum, $creator_name)
	{
		$this->creator[$creatorNum][0] = array();
		$this->creator[$creatorNum][0][0] = $creator_name;
	}

	function setCreatorOrcid($creatorNum, $creator_orcid)
	{
		$this->creator[$creatorNum][0][1] = $creator_orcid;
	}

	function setCreatorType($creatorNum, $creator_type)
	{
		$this->creator[$creatorNum][0][2] = $creator_type;
	}

	function setCreatorURL($creatorNum, $creator_url)
	{
		$this->creator[$creatorNum][0][3] = $creator_url;
	}

	function setCreatorContactPoint($creatorNum, $creator_contactPoint)
	{
		$this->creator[$creatorNum][0][4] = $creator_contactPoint;
	}

	function setAffiliation($creatorNum, $affiliationNum, $affiliationType, $affiliation)
	{
		if (!isset($this->affiliations[$creatorNum]))
		{
			$this->affiliations[$creatorNum] = array();
		}
		if (!isset($this->affiliations[$creatorNum][$affiliationNum]))
		{
			$this->affiliations[$creatorNum][$affiliationNum] = array();
			$this->affiliations[$creatorNum][$affiliationNum][0] = "";
			$this->affiliations[$creatorNum][$affiliationNum][1] = "";
			$this->affiliations[$creatorNum][$affiliationNum][2] = "";
		}
		$this->affiliations[$creatorNum][$affiliationNum][$affiliationType] = $affiliation;
	}

	function saveCreatorData() {
		foreach ($this->creator as $creatorNum => $creator) {

			// First, insert new creator into database
			$insertCreator = "INSERT INTO creators SET recordInfo_recordIdentifier = '$this->id', creator_name = \"" . addslashes($creator[0][0]) . "\", creator_orcid = \"" . addslashes($creator[0][1]) . "\", creator_type = \"" . addslashes($creator[0][2]) . "\", creator_url = \"" . addslashes($creator[0][3]) . "\", creator_contactPoint = \"" . addslashes($creator[0][4]) . "\"";

			if (@mysql_query($insertCreator)) {
				// Get creatorKey
				$this->creatorKey = mysql_insert_id();
			}
			else {
				die("<h2>Error inserting into datasets: " . mysql_error() . "</h2>");
			}
			
			// Then, insert affiliations into database
			if (isset($this->affiliations[$creatorNum])) {
				foreach ($this->affiliations[$creatorNum] as $affiliationNum => $affiliation) {
					if ((isset($affiliation[0]) && $affiliation[0] != "") ||
						(isset($affiliation[1]) && $affiliation[1] != "") ||
						(isset($affiliation[2]) && $affiliation[2] != "")) {
						$insertAffiliation = "
							INSERT INTO affiliations
							SET creator_key = \"$this->creatorKey\", name_affiliation_msuCollege = \"" . $affiliation[0] . "\", name_affiliation_msuDepartment = \"" . $affiliation[1] . "\", name_affiliation_otherAffiliation = \"" . $affiliation[2] . "\"";
						if (!@mysql_query($insertAffiliation)) {
							die("<h2>Error inserting into datasets: " . mysql_error() . "</h2>");
						}
					}
				}
			}
		}
	}
}
?>

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

// Declare form variables for submitting the data
$dataset_name = $_POST['dataset_name'];
$dataset_doi = $_POST['dataset_doi'];
$dataset_repositoryName = $_POST['dataset_repositoryName'];
$dataset_url = $_POST['dataset_url'];
$dataset_description = $_POST['dataset_description'];
$dataset_keywords = $_POST['dataset_keywords'];
$dataset_temporalCoverage = $_POST['dataset_temporalCoverage'];
$dataset_spatialCoverage = $_POST['dataset_spatialCoverage'];
$dataset_category1 = $_POST['dataset_category1'];
$dataset_category1_uri = $_POST['dataset_category1_uri'];
$dataset_category2 = $_POST['dataset_category2'];
$dataset_category2_uri = $_POST['dataset_category2_uri'];
$dataset_category3 = $_POST['dataset_category3'];
$dataset_category3_uri = $_POST['dataset_category3_uri'];
$dataset_category4 = $_POST['dataset_category4'];
$dataset_category4_uri = $_POST['dataset_category4_uri'];
$dataset_category5 = $_POST['dataset_category5'];
$dataset_category5_uri = $_POST['dataset_category5_uri'];
$dataset_encodingFormat = $_POST['dataset_encodingFormat'];
$dataset_license = $_POST['dataset_license'];
$dataset_version = $_POST['dataset_version'];
$dataset_sameAs = $_POST['dataset_sameAs'];
$status = $_POST['status'];

// Escape special characters for submission to database - convert HTML
// special characters in database value for use in an HTML document.

$dataset_name = addslashes($dataset_name);
$dataset_repositoryName = addslashes($dataset_repositoryName);
$dataset_description = addslashes($dataset_description);
$dataset_keywords = addslashes($dataset_keywords);

// Validate name, description fields as containing data
if ($dataset_name == '')
{
	die('<p>You must enter a title for this metadata item. Click "Back" and try again.</p>');
}
//if ($dataset_description == '')
//{
//  die('<p>You must enter a description or abstract for this metadata item. Click "Back" and try again.</p>');
//}

$editDataset = "UPDATE datasets SET
	dataset_name = \"$dataset_name\",
	dataset_doi = \"$dataset_doi\",
	dataset_repositoryName = \"$dataset_repositoryName\",
	dataset_url = \"$dataset_url\",
	dataset_description = \"$dataset_description\",
	dataset_keywords = \"$dataset_keywords\",
	dataset_temporalCoverage = \"$dataset_temporalCoverage\",
	dataset_spatialCoverage = \"$dataset_spatialCoverage\",
	dataset_category1 = \"$dataset_category1\",
	dataset_category1_uri = \"$dataset_category1_uri\",
	dataset_category2 = \"$dataset_category2\",
	dataset_category2_uri = \"$dataset_category2_uri\",
	dataset_category3 = \"$dataset_category3\",
	dataset_category3_uri = \"$dataset_category3_uri\",
	dataset_category4 = \"$dataset_category4\",
	dataset_category4_uri = \"$dataset_category4_uri\",
	dataset_category5 = \"$dataset_category5\",
	dataset_category5_uri = \"$dataset_category5_uri\",
	dataset_encodingFormat = \"$dataset_encodingFormat\",
	dataset_license = \"$dataset_license\",
	dataset_version = \"$dataset_version\",
	dataset_sameAs = \"$dataset_sameAs\",
	status = \"$status\"
	WHERE recordInfo_recordIdentifier=\"$id\"";

//	relatedItem_originInfo_publisher = \"$relatedItem_originInfo_publisher\",
//	relatedItem_originInfo_dateCreated = \"$relatedItem_originInfo_dateCreated\",

if (!@mysql_query($editDataset)) {
	die("<h2>Error updating datasets: " . mysql_error() . "</h2>");
}

// Now delete previous creators and affiliations

// Get creator_keys from creators table
if (!($creatorKeys = @mysql_query("SELECT creator_key FROM creators WHERE recordInfo_recordIdentifier = '$id'"))) {
	die("<h2>Error selecting datasets: " . mysql_error() . "</h2>");
}

// Use creator_keys to delete affiliations
while ($row = mysql_fetch_object($creatorKeys)) {
	if (!@mysql_query("DELETE FROM affiliations WHERE creator_key = \"" . $row->creator_key . "\";")) {
		die("<h2>Error deleting from datasets: " . mysql_error() . "</h2>");
	}
}

// Use recordInfo_recordIdentifier to delete creators
if (!@mysql_query("DELETE FROM creators WHERE recordInfo_recordIdentifier = '$id';")) {
	die("<h2>Error deleting from datasets: " . mysql_error() . "</h2>");
}

// Now store creators and affiliations
$creatorData = new CreatorData($id);

foreach ($_POST as $key => $value)
{
	if (strstr($key, "creator_name"))
	{
		$creatorNum = str_replace("creator_name", "", $key);
		$creatorData->setCreatorName($creatorNum, addslashes($value));
	}

	if (strstr($key, "creator_orcid"))
	{
		$creatorNum = str_replace("creator_orcid", "", $key);
		$creatorData->setCreatorOrcid($creatorNum, addslashes($value));
	}

	if (strstr($key, "creator_type"))
	{
		$creatorNum = str_replace("creator_type", "", $key);
		$creatorData->setCreatorType($creatorNum, addslashes($value));
	}

	if (strstr($key, "creator_url"))
	{
		$creatorNum = str_replace("creator_url", "", $key);
		$creatorData->setCreatorURL($creatorNum, addslashes($value));
	}

	if (strstr($key, "creator_contactPoint"))
	{
		$creatorNum = str_replace("creator_contactPoint", "", $key);
		$creatorData->setCreatorContactPoint($creatorNum, addslashes($value));
	}

	if (strstr($key, "affiliation")) {
		preg_match("/affiliation(\d+)-(\d+)-(\d+)/", $key, $matches);

		$creatorData->setAffiliation($matches[1], $matches[2], $matches[3], addslashes($value));
	}
}

$creatorData->saveCreatorData();

// Clear form variables if page is refreshed to avoid reposting data to database
unset($_POST);

?>

<h2>The Dataset metadata item was edited and given a title of <?php echo stripslashes(html_entity_decode($dataset_name)); ?>.</h2>
<h2><a href="./">Return to Manage DAWS Collection &amp; Metadata Home</a></h2>

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
$getMetadataItem = @mysql_query("SELECT * FROM datasets WHERE recordInfo_recordIdentifier='$id'");

if (!$getMetadataItem) {
	die("<h2>Error fetching Metadata Item details: " . mysql_error() . "</h2>");
}

// Store query result in an array
$row = mysql_fetch_object($getMetadataItem);

// Define form variables for printing guide table data in editing form
$dataset_name = $row->dataset_name;
$dataset_doi = $row->dataset_doi;
$dataset_repositoryName = $row->dataset_repositoryName;
$dataset_url = $row->dataset_url;
$dataset_description = $row->dataset_description;
$dataset_keywords = $row->dataset_keywords;
$dataset_temporalCoverage = $row->dataset_temporalCoverage;
$dataset_spatialCoverage = $row->dataset_spatialCoverage;
$dataset_category1 = $row->dataset_category1;
$dataset_category1_uri = $row->dataset_category1_uri;
$dataset_category2 = $row->dataset_category2;
$dataset_category2_uri = $row->dataset_category2_uri;
$dataset_category3 = $row->dataset_category3;
$dataset_category3_uri = $row->dataset_category3_uri;
$dataset_category4 = $row->dataset_category4;
$dataset_category4_uri = $row->dataset_category4_uri;
$dataset_category5 = $row->dataset_category5;
$dataset_category5_uri = $row->dataset_category5_uri;
$dataset_encodingFormat = $row->dataset_encodingFormat;
$dataset_license = $row->dataset_license;
$dataset_version = $row->dataset_version;
$dataset_sameAs = $row->dataset_sameAs;
$status = $row->status;

?>

<script>

<?php

// Get creators and affiliations
$query = "
	SELECT creators.creator_name, creators.creator_orcid, creators.creator_type, creators.creator_url, creators.creator_contactPoint, affiliations.name_affiliation_msuCollege, affiliations.name_affiliation_msuDepartment, affiliations.name_affiliation_otherAffiliation
	FROM creators LEFT JOIN affiliations
	ON creators.creator_key = affiliations.creator_key
	WHERE creators.recordInfo_recordIdentifier='$id'
	ORDER BY creators.creator_key, affiliations.affiliation_key;
";
$getMetadataCreatorInfo = @mysql_query($query);

if (!$getMetadataCreatorInfo) {
	die("<h2>Error fetching Metadata creator info: " . mysql_error() . "</h2>");
}

$creatorNum = -1;
$prev_creator = "";
echo "var creators = new Array();\n";
while ($row = mysql_fetch_object($getMetadataCreatorInfo)) {
	$creator = $row->creator_name;

	if ($creator != $prev_creator) {
		// New Creator
		echo "creators[" . ++$creatorNum . "] = new Array();\n";
		echo "creators[$creatorNum][0] = new Array();\n";
		echo "creators[$creatorNum][0][0] = \"" . str_replace("\"", "&quot;", $row->creator_name) . "\";\n";
		echo "creators[$creatorNum][0][1] = \"" . str_replace("\"", "&quot;", $row->creator_orcid) . "\";\n";
		echo "creators[$creatorNum][0][2] = \"" . str_replace("\"", "&quot;", $row->creator_type) . "\";\n";
		echo "creators[$creatorNum][0][3] = \"" . str_replace("\"", "&quot;", $row->creator_url) . "\";\n";
		echo "creators[$creatorNum][0][4] = \"" . str_replace("\"", "&quot;", $row->creator_contactPoint) . "\";\n";
//		echo "creators[$creatorNum][0] = \"" . htmlentities($creator, ENT_COMPAT, ini_get("default_charset"), false) . "\";\n";
		$prev_creator = $creator;
		$affiliationNum = 0;
	}
	if ($row->name_affiliation_msuCollege != "" || $row->name_affiliation_msuDepartment != "" || $row->name_affiliation_otherAffiliation != "") {
		echo "creators[$creatorNum][" . ++$affiliationNum . "] = new Array();\n";
		echo "creators[$creatorNum][$affiliationNum][0] = \"" . str_replace("\"", "&quot;", $row->name_affiliation_msuCollege) . "\";\n";
//		echo "creators[$creatorNum][$affiliationNum][0] = \"" . htmlentities($row->name_affiliation_msuCollege, ENT_COMPAT, ini_get("default_charset"), false) . "\";\n";
		echo "creators[$creatorNum][$affiliationNum][1] = \"" . str_replace("\"", "&quot;", $row->name_affiliation_msuDepartment) . "\";\n";
//		echo "creators[$creatorNum][$affiliationNum][1] = \"" . htmlentities($row->name_affiliation_msuDepartment, ENT_COMPAT, ini_get("default_charset"), false) . "\";\n";
		echo "creators[$creatorNum][$affiliationNum][2] = \"" . str_replace("\"", "&quot;", $row->name_affiliation_otherAffiliation) . "\";\n";
//		echo "creators[$creatorNum][$affiliationNum][2] = \"" . htmlentities($row->name_affiliation_otherAffiliation, ENT_COMPAT, ini_get("default_charset"), false) . "\";\n";
	}
}

?>

$(document).ready(function () {
	displayCreators();
});

function confirmDelete(id)
{
//	if (confirm("Do you really want to delete this object?")) {
		window.location.assign("./deleteDataset.php?id=" + id);
//	}
}

</script>

<script src='../meta/scripts/creator-util.js'></script>

<form id="adminForm" action="<?php echo basename(__FILE__); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<fieldset>

<legend>MSU Dataset Search Metadata</legend>

<h3><label for="dataset_name" title="dataset_name">Dataset Name</label></h3>
<input class="text" type="text" id="dataset_name" name="dataset_name" size="40" maxlength="300" value="<?php echo $dataset_name; ?>" />

<div id="creatorDiv">
</div>

<h3><label for="dataset_doi" title="dataset_doi">Dataset DOI</label></h3>
<input class="text" type="text" id="dataset_doi" name="dataset_doi" size="40" maxlength="300" value="<?php echo $dataset_doi; ?>" />

<h3><label for="dataset_repositoryName" title="dataset_repositoryName">Dataset Repository Name</label></h3>
<input class="text" type="text" id="dataset_repositoryName" name="dataset_repositoryName" size="40" maxlength="255" value="<?php echo $dataset_repositoryName; ?>" />

<h3><label for="dataset_url" title="dataset_url">Dataset URL</label></h3>
<input class="text" type="text" id="dataset_url" name="dataset_url" size="40" maxlength="300" value="<?php echo $dataset_url; ?>" />

<h3><label for="dataset_sameAs" title="dataset_sameAs">Dataset Other URL (if dataset in multiple repositories)</label></h3>
<input class="text" type="text" id="dataset_sameAs" name="dataset_sameAs" size="40" maxlength="300" value="<?php echo $dataset_sameAs; ?>" />

<h3><label for="dataset_description" title="dataset_description">Dataset Description</label></h3>
<textarea class="text" type="text" id="dataset_description" name="dataset_description" rows="15" cols="20"><?php echo $dataset_description; ?></textarea>

<h3><label for="dataset_keywords" title="dataset_keywords">Dataset Keywords (comma delimited)</label></h3>
<input class="text" type="text" id="dataset_keywords" name="dataset_keywords" size="40" maxlength="255" value="<?php echo $dataset_keywords; ?>" />

<h3><label for="dataset_temporalCoverage" title="dataset_temporalCoverage">Dataset Publication Date (YYYY-MM-DD)</label></h3>
<input class="text" type="text" id="dataset_temporalCoverage" name="dataset_temporalCoverage" size="40" maxlength="30" value="<?php echo $dataset_temporalCoverage; ?>" />

<h3><label for="dataset_spatialCoverage" title="dataset_spatialCoverage">Dataset GeoShape Box Coordinates or Latitude / Longitude</label></h3>
<input class="text" type="text" id="dataset_spatialCoverage" name="dataset_spatialCoverage" size="40" maxlength="30" value="<?php echo $dataset_spatialCoverage; ?>" />
<?php

for ($i = 1; $i <= 5; $i++)
{
/******************************************************************************/

/*
    // Uncomment the following two lines and comment out the following group to use freeform input for categories
    echo "<h3><label for=\"dataset_category$i\" title=\"dataset_category$i\">Dataset Linked Data Category $i</label></h3>\n";
    echo "<input class=\"text\" type=\"text\" id=\"dataset_category$i\" name=\"dataset_category$i\" size=\"40\" maxlength=\"255\" value=\"${'dataset_category' . $i}\" />\n";

*/

/******************************************************************************/

    echo "<h3><label for=\"dataset_category$i\" title=\"dataset_category$i\">Dataset Linked Data Category $i (Select one)</label></h3>\n";
    echo "<select id=\"dataset_category$i\" name=\"dataset_category$i\" size=\"1\">\n";

    foreach ($categories as $category)
    {
        echo "<option value=\"$category\"";

        // The next line uses the variable variables feature of PHP
        if (${"dataset_category" . $i} == $category) {
            echo " selected";
        }
        echo ">$category</option>\n";
    }

    echo "</select>\n\n";

/******************************************************************************/

    echo "<h3><label for=\"dataset_category${i}_uri\" title=\"dataset_category${i}_uri\">Dataset Linked Data Category $i URI</label></h3>\n";
    echo "<input class=\"text\" type=\"text\" id=\"dataset_category${i}_uri\" name=\"dataset_category${i}_uri\" size=\"40\" maxlength=\"255\" value=\"${'dataset_category' . $i . '_uri'}\" />\n";
}

?>
<h3><label for="dataset_encodingFormat" title="dataset_encodingFormat">Dataset Encoding Format (e.g. CSV)</label></h3>
<input class="text" type="text" id="dataset_encodingFormat" name="dataset_encodingFormat" size="40" maxlength="30" value="<?php echo $dataset_encodingFormat; ?>" />

<h3><label for="dataset_license" title="dataset_license">Dataset Copyright Conditions</label></h3>
<input class="text" type="text" id="dataset_license" name="dataset_license" size="40" maxlength="255" value="<?php echo $dataset_license; ?>" />

<h3><label for="dataset_version" title="dataset_version">Dataset Version Number</label></h3>
<input class="text" type="text" id="dataset_version" name="dataset_version" size="40" maxlength="30" value="<?php echo $dataset_version; ?>" />

<h3><label for="status" title="status">Object Status</label></h3>
<p class="adminNote"><span>Object's current status is <?php echo($status); ?>. Edit object's status below.</span></p>
<ul class="block">
<?php

	echo '<li><input type="radio" name="status" value="a" ' .
		(($status == 'a') ? 'checked="checked"' : '') . ' /> a = Active</li>'."\n";
	echo '<li><input type="radio" name="status" value="r" ' .
		(($status == 'r') ? 'checked="checked"' : '') . ' /> r = Review</li>'."\n";
	echo '<li><input type="radio" name="status" value="p" ' .
		(($status == 'p') ? 'checked="checked"' : '') . ' /> p = Pending</li>'."\n";
	echo '<li><input type="radio" name="status" value="i" ' .
		(($status == 'i') ? 'checked="checked"' : '') . ' /> i = Inactive</li>'."\n";
	echo '<li><input type="radio" name="status" value="d" ' .
		(($status == 'd') ? 'checked="checked"' : '') . ' /> d = Discarded</li>'."\n";
	echo '<li><input type="radio" name="status" value="u" ' .
		(($status == 'u') ? 'checked="checked"' : '') . ' /> u = Unprocessed</li>'."\n";

?>
</ul>
</fieldset>
<p class="button"><input class="submit" type="submit" name="submit" value="Submit Metadata Edits" /></p>
</form>

<?php

endif; // End if/else statement allowing datasets to see edit form and submit it

// Bring footer code onto page
include '../meta/inc/footer-admin.php';

?>
