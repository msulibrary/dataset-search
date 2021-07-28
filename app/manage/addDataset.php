<?php
// Set title
$pageTitle = "MSU Dataset Search Add Object";

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

//Declare id earlier in the file
$id = 0;

// CreatorData class
class CreatorData
{
	var $id;
	var $creatorKey;
	var $creator = array();
	var $affiliations = array();

	function CreatorData($id) {
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

	function setAffiliation($creatorNum, $affiliationNum, $affiliationType, $affiliation) {
		if (!isset($this->affiliations[$creatorNum])) {
			$this->affiliations[$creatorNum] = array();
		}
		if (!isset($this->affiliations[$creatorNum][$affiliationNum])) {
			$this->affiliations[$creatorNum][$affiliationNum] = array();
			$this->affiliations[$creatorNum][$affiliationNum][0] = "";
			$this->affiliations[$creatorNum][$affiliationNum][1] = "";
			$this->affiliations[$creatorNum][$affiliationNum][2] = "";
			$this->affiliations[$creatorNum][$affiliationNum][3] = "";
			$this->affiliations[$creatorNum][$affiliationNum][4] = "";
		}
		$this->affiliations[$creatorNum][$affiliationNum][$affiliationType] = $affiliation;
	}

	function saveCreatorData($dbConn)
	{
		//$this->$dbConn = $dbConn;
		//print_r($this->creator);
		foreach ($this->creator as $creatorNum => $creator)
		{
			// First, insert new creator into database
			$insertCreator = "INSERT INTO creators SET recordInfo_recordIdentifier = '$this->id', creator_name = \"" . addslashes($creator[0][0]) . "\", creator_orcid = \"" . addslashes($creator[0][1]) . "\", creator_type = \"" . addslashes($creator[0][2]) . "\", creator_url = \"" . addslashes($creator[0][3]) . "\", creator_contactPoint = \"" . addslashes($creator[0][4]) . "\"";
			//echo $insertCreator;
			//echo("Insert Creator: " . $insertCreator . "</br>");

			if ($dbConn->query($insertCreator))
			{
				// Get creatorKey
				$this->creatorKey = $dbConn->insert_id;
			}
			else
			{
				die("<h2>Error inserting MODS: " . $dbConn->error . "</h2>");
			}
			
			// Then, insert affiliations into database
			if (isset($this->affiliations[$creatorNum]))
			{
				foreach ($this->affiliations[$creatorNum] as $affiliationNum => $affiliation)
				{
					if ((isset($affiliation[0]) && $affiliation[0] != "") ||
						(isset($affiliation[1]) && $affiliation[1] != "") ||
						(isset($affiliation[2]) && $affiliation[2] != "") ||
						(isset($affiliation[3]) && $affiliation[3] != "") ||
						(isset($affiliation[4]) && $affiliation[4] != ""))
					{
						$insertAffiliation = "
                            INSERT INTO affiliations
                            SET creator_key = \"$this->creatorKey\", name_affiliation_msuCollege = \"" . $affiliation[0] . "\", name_affiliation_msuCollege_abbr = \"" . $affiliation[1] . "\", name_affiliation_msuDepartment = \"" . $affiliation[2] . "\", name_affiliation_msuDepartment_abbr = \"" . $affiliation[3] . "\", name_affiliation_otherAffiliation = \"" . $affiliation[4] . "\"";
							
							//echo("Insert Affiliation: " . $insertAffiliation . "</br>");

						if (!$dbConn->query($insertAffiliation))
						{
							die("<h2>Error inserting MODS: " . $dbConn->error . "</h2>");
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
$dataset_name = $_POST['dataset_name'];
$dataset_doi = $_POST['dataset_doi'];
$dataset_repositoryName = $_POST['dataset_repositoryName'];
$dataset_funder = $_POST['dataset_funder'];
$dataset_funder_identifier = $_POST['dataset_funder_identifier'];
$dataset_grant_identifier = $_POST['dataset_grant_identifier'];
$dataset_url = $_POST['dataset_url'];
$dataset_description = $_POST['dataset_description'];
$dataset_keywords = $_POST['dataset_keywords'];
$dataset_temporalCoverage = $_POST['dataset_temporalCoverage'];
$dataset_datePublished = $_POST['dataset_datePublished'];
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
$dataset_conditionsOfAccess = $_POST['dataset_conditionsOfAccess'];
$dataset_conditionsOfAccess_status = $_POST['dataset_conditionsOfAccess_status'];
$dataset_version = $_POST['dataset_version'];
$dataset_sameAs = $_POST['dataset_sameAs'];
$dataset_relatedMaterial = $_POST['dataset_relatedMaterial'];
$status = $_POST['status'];

// Escape special characters for submission to database - convert HTML
// special characters in database value for use in an HTML document.

$dataset_name = addslashes($dataset_name);
$dataset_description = addslashes($dataset_description);
$dataset_keywords = addslashes($dataset_keywords);
$dataset_funder = addslashes($dataset_funder);
$dataset_funder_identifier = addslashes($dataset_funder_identifier);
$dataset_grant_identifier = addslashes($dataset_grant_identifier);
$dataset_conditionsOfAccess = addslashes($dataset_conditionsOfAccess);
$dataset_relatedMaterial = addslashes($dataset_relatedMaterial);

// Validate name, description fields as containing data
if ($dataset_name == '') {
	die('<p>You must enter a title for this metadata item. Click "Back" and try again.</p>');
}
//if ($dataset_description == '') {
//  die('<p>You must enter a description or abstract for this metadata item. Click "Back" and try again.</p>');
//}

// Store SQL Query for inserting data into database
$addDataset = "INSERT INTO datasets SET
	dataset_name = \"$dataset_name\",
	dataset_doi = \"$dataset_doi\",
	dataset_repositoryName = \"$dataset_repositoryName\",
	dataset_funder = \"$dataset_funder\",
	dataset_funder_identifier = \"$dataset_funder_identifier\",
	dataset_grant_identifier = \"$dataset_grant_identifier\",
	dataset_url = \"$dataset_url\",
	dataset_description = \"$dataset_description\",
	dataset_keywords = \"$dataset_keywords\",
	dataset_temporalCoverage = \"$dataset_temporalCoverage\",
	dataset_datePublished = \"$dataset_datePublished\",
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
	dataset_conditionsOfAccess = \"$dataset_conditionsOfAccess\",
	dataset_conditionsOfAccess_status = \"$dataset_conditionsOfAccess_status\",
	dataset_version = \"$dataset_version\",
	dataset_sameAs = \"$dataset_sameAs\",
	dataset_relatedMaterial = \"$dataset_relatedMaterial\",
	status = \"$status\"";

if ($dbConn->query($addDataset))
{
	// Get recordInfo_recordIdentifier
	$id = $dbConn->insert_id;
}
else
{
	die("<h2>Error adding MODS: " . $dbConn->error . "</h2>");
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

	if (strstr($key, "affiliation"))
	{
		preg_match("/affiliation(\d+)-(\d+)-(\d+)/", $key, $matches);
		
		$creatorData->setAffiliation($matches[1], $matches[2], $matches[3], addslashes($value));

	}
}

$creatorData->saveCreatorData($dbConn);

echo('<h2>Dataset metadata item added successfully.</h2>');

// Clear form variables if page is refreshed to avoid reposting data to database
unset($_POST);

?>

<h2>A new MSU Dataset Search metadata item with title of "<?php echo stripslashes(html_entity_decode($dataset_name)); ?>" was added.</h2>
<h2><a href="./addDataset.php">Add another MSU Dataset Search metadata item</a></h2>
<h2><a href="./">Return to Manage MSU Dataset Search Home</a></h2>

<?php

else:
// Show form and allow the user to add a new datasets metadata item

?>

<script>

// Create creators array
var creators = new Array();

// Create first creators entry
creators[0] = new Array();

// Create first creator
creators[0][0] = new Array();
creators[0][0][0] = "";
creators[0][0][1] = "";
creators[0][0][2] = "";
creators[0][0][3] = "";
creators[0][0][4] = "";

// Create first affiliation for first creator
creators[0][1] = new Array();
creators[0][1][0] = "";
creators[0][1][1] = "";
creators[0][1][2] = "";
creators[0][1][3] = "";
creators[0][1][4] = "";

$(document).ready(function () {
    displayCreators();
});

</script>

<script src='../meta/scripts/creator-util.js'></script>

<form id="adminForm" action="<?php echo basename(__FILE__); ?>" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<fieldset>

<legend>MSU Dataset Search Metadata</legend>

<h3><label for="dataset_name" title="dataset_name">Dataset Name</label></h3>
<input class="text" type="text" id="dataset_name" name="dataset_name" size="40" maxlength="300" value="" />

<div id="creatorDiv">
</div>

<h3><label for="dataset_doi" title="dataset_doi">Dataset DOI</label></h3>
<input class="text" type="text" id="dataset_doi" name="dataset_doi" size="40" maxlength="300" value="" />

<h3><label for="dataset_repositoryName" title="dataset_repositoryName">Dataset Repository Name</label></h3>
<input class="text" type="text" id="dataset_repositoryName" name="dataset_repositoryName" size="40" maxlength="255" value="" />

<h3><label for="dataset_funder" title="dataset_funder">Dataset Funder</label></h3>
<input class="text" type="text" id="dataset_funder" name="dataset_funder" size="40" maxlength="255" value="" />

<h3><label for="dataset_funder_identifier" title="dataset_funder_identifier">Dataset Funder Identifier</label></h3>
<input class="text" type="text" id="dataset_funder_identifier" name="dataset_funder_identifier" size="40" maxlength="255" value="" />

<h3><label for="dataset_grant_identifier" title="dataset_grant_identifier">Dataset Grant Identifier</label></h3>
<input class="text" type="text" id="dataset_grant_identifier" name="dataset_grant_identifier" size="40" maxlength="255" value="" />

<h3><label for="dataset_url" title="dataset_url">Dataset URL</label></h3>
<input class="text" type="text" id="dataset_url" name="dataset_url" size="40" maxlength="300" value="" />

<h3><label for="dataset_sameAs" title="dataset_sameAs">Dataset Other URL (if dataset in multiple repositories)</label></h3>
<input class="text" type="text" id="dataset_sameAs" name="dataset_sameAs" size="40" maxlength="300" value="" />

<h3><label for="dataset_description" title="dataset_description">Dataset Description</label></h3>
<textarea class="text" type="text" id="dataset_description" name="dataset_description" rows="15" cols="20"></textarea>

<h3><label for="dataset_keywords" title="dataset_keywords">Dataset Keywords (comma delimited)</label></h3>
<input class="text" type="text" id="dataset_keywords" name="dataset_keywords" size="40" maxlength="255" value="" />

<h3><label for="dataset_datePublished" title="dataset_datePublished">Dataset Publication Date (YYYY-MM-DD)</label></h3>
<input class="text" type="text" id="dataset_datePublished" name="dataset_datePublished" size="40" maxlength="30" value="" />

<h3><label for="dataset_temporalCoverage" title="dataset_temporalCoverage">Date range for data collection</label></h3>
<input class="text" type="text" id="dataset_temporalCoverage" name="dataset_temporalCoverage" size="40" maxlength="30" value="" />



<h3><label for="dataset_spatialCoverage" title="dataset_spatialCoverage">Dataset GeoShape Box Coordinates or Latitude / Longitude</label></h3>
<input class="text" type="text" id="dataset_spatialCoverage" name="dataset_spatialCoverage" size="40" maxlength="255" value="" />
<?php

for ($i = 1; $i <= 5; $i++)
{
/******************************************************************************/

/*  
	// Uncomment the following two lines and comment out the following group to use freeform input for categories
	echo "<h3><label for=\"dataset_category$i\" title=\"dataset_category$i\">Dataset Linked Data Category $i</label></h3>\n";
	echo "<input class=\"text\" type=\"text\" id=\"dataset_category$i\" name=\"dataset_category$i\" size=\"40\" maxlength=\"255\" value=\"\" />\n";

*/

/******************************************************************************/

	echo "<h3><label for=\"dataset_category$i\" title=\"dataset_category$i\">Dataset Linked Data Category $i (Select one)</label></h3>\n";
    echo "<select id=\"dataset_category$i\" name=\"dataset_category$i\" size=\"1\">\n";

    foreach ($categories as $category)
	{
		echo "<option value=\"$category\">$category</option>\n";
    }

    echo "</select>\n\n";

/******************************************************************************/

	echo "<h3><label for=\"dataset_category${i}_uri\" title=\"dataset_category${i}_uri\">Dataset Linked Data Category $i URI</label></h3>\n";
	echo "<input class=\"text\" type=\"text\" id=\"dataset_category${i}_uri\" name=\"dataset_category${i}_uri\" size=\"40\" maxlength=\"255\" value=\"\" />\n";
}

?>
<h3><label for="dataset_encodingFormat" title="dataset_encodingFormat">Dataset Encoding Format (e.g. CSV)</label></h3>
<input class="text" type="text" id="dataset_encodingFormat" name="dataset_encodingFormat" size="40" maxlength="30" value="" />

<h3><label for="dataset_license" title="dataset_license">Dataset Copyright Conditions</label></h3>
<input class="text" type="text" id="dataset_license" name="dataset_license" size="40" maxlength="255" value="" />

<h3><label for="dataset_conditionsOfAccess" title="dataset_conditionsOfAccess">Dataset Conditions of Access</label></h3>
<input class="text" type="text" id="dataset_conditionsOfAccess" name="dataset_conditionsOfAccess" size="40" maxlength="255" value="" />
<h3><label for="dataset_conditionsOfAccess_status" title="dataset_conditionsOfAccess_status">Conditions of Access Status</label></h3>
<ul class="block">
	<li><input type="radio" name="dataset_conditionsOfAccess_status" value="o" checked="checked"/> o = Open</li>
	<li><input type="radio" name="dataset_conditionsOfAccess_status" value="r" /> r = Restricted</li>
</ul>
<h3><label for="dataset_version" title="dataset_version">Dataset Version Number</label></h3>
<input class="text" type="text" id="dataset_version" name="dataset_version" size="40" maxlength="30" value="" />

<h3><label for="dataset_relatedMaterial" title="dataset_relatedMaterial">Dataset Related Material(Include DOIs when available)</label></h3>
<input class="text" type="text" id="dataset_relatedMaterial" name="dataset_relatedMaterial" size="40" maxlength="255" value="" />

<h3><label for="status" title="status">Object's Status</label></h3>
<ul class="block">
	<li><input type="radio" name="status" value="a" /> a = Active</li>
	<li><input type="radio" name="status" value="r" checked="checked" /> r = Review</li>
	<li><input type="radio" name="status" value="p" /> p = Pending</li>
	<li><input type="radio" name="status" value="i" /> i = Inactive</li>
	<li><input type="radio" name="status" value="d" /> d = Discarded</li>
</ul>
</fieldset>
<p class="button"><input class="submit" type="submit" name="submit" value="Add Metadata Item" /></p>
</form>

<?php 
endif; // End if/else statement allowing datasets to see edit form and submit it (line 114)

// Bring footer code onto page
include '../meta/inc/footer-admin.php';
?>
