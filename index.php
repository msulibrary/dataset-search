<?php

// Set Title, Description, and Keywords
$pageTitle = 'Montana State University Dataset Search';
$pageDescription = 'Montana State University Dataset Search database.';
$pageKeywords = 'MSU, data';

// Create an array with filepaths for multiple page scripts - default is meta/scripts/thickbox.js
$customScript[0] = './meta/scripts/jquery-compressed.js';
$customScript[1] = './meta/scripts/thickbox.js';

// Declare filename and filepath for screen/projection stylesheet variable - default is common/styles/master.css
$customCSS[0] = './meta/styles/thickbox.css';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Include page header
include './meta/inc/header.php';

// Include functions to get creators from database
include './getCreators.php';
?>

<div id="main">
	<a name="mainContent"></a>
	<div class="gutter">
		<h2 class="mainHeading"><?php echo $pageTitle; ?></h2>
		<ul id="tabs">
			<li id="tab1"><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?view=default">Quick Search</a></li>
			<li id="tab2"><a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?view=advanced">Advanced Search</a></li>
		</ul><!-- end tabs unordered list -->
		<div id="tabContents">
			<form class="search" action="search.php" method="post">
				<fieldset id="searchForm">

<?php
				// Set open value for section of search form to display
				$params = (count($_POST)) ? $_POST : $_GET;
				$view = (empty($params['view'])) ? null : $params['view'];
				// Set switch control structure to shift view of form section based on value in url
				switch($view) {
					default:
					{
						echo '<p><label for="q"><strong>Search:</strong></label>&nbsp; <input class="text" type="text" id="q" name="q" maxlength="75" size="45" value="keyword, name, title..." onclick="if (this.value == \'keyword, name, title...\') { this.value = \'\'; }" onblur="if (this.value == \'\') { this.value = \'keyword, name, title...\'; }" />'."\n";
					}
					break;

					case 'advanced':
					{
						echo '<p><label for="keyword"><strong>Keyword:</strong></label>&nbsp; <input class="text" type="text" id="keyword" name="keyword" maxlength="100" size="100" /></p>'."\n";
						echo '<p><label for="creator"><strong>Creator:</strong></label>'. ' ' .'<input class="text" type="text" id="creator" name="creator" maxlength="75" size="45" /></p>'."\n";
						echo '<p><label for="title"><strong>Title:</strong></label>'. ' ' .'<input class="text" type="text" id="title" name="title" maxlength="200" size="200" />'."\n";
					}
					break;
				}
?>

				<input type="submit" id="submit" class="submit" value="Search" /></p>
				</fieldset>
			</form>
		</div><!-- end tabContents div -->
		<ul class="boxes">
			<li>
				<h2 class="mainHeading">Browse</h2>
				<p><a href="./keywords.php" title="List of keywords from the MSU datasets">Keywords</a></p>
				<p><a href="./categories.php" title="List of categories from the MSU datasets">Categories</a></p>
				<p><a href="./dataset-names.php" title="List of dataset names">Dataset Names</a></p>
			</li>
			<li>
				<h2 class="mainHeading">About</h2>
				<p><a class="expand" href="./about.php" title="about <?php echo $pageTitle; ?> database">About the MSU Dataset Search Online Database</a></p></br>
			</li>
			<li>
				<h2 class="mainHeading">Latest Feeds Tool</h2>
				<p>Check on <a href="./feeds.php">latest datasets from MSU</a></p>
			</li>
		</ul>

<?php

		// Select random record/item(s) from table
		$result = mysql_query("SELECT * FROM datasets WHERE status = 'a' ORDER BY RAND() LIMIT 3");

?>
		<h2 class="mainHeading"><span id="sample">Sample Datasets (Refresh page to cycle through samples.)</span></h2>
		<ul class="boxes">
<?php

		// Format individual fields/rows for display from $result query, set up while loop
		while ($row = mysql_fetch_array($result))
		{
			$id = $row['recordInfo_recordIdentifier'];
			$name = $row['dataset_name'];
			$doi = $row['dataset_doi'];
			$url = $row['dataset_url'];

			// Display selected record/item(s) to the user
			echo "<li>\n";
			echo "<p><a href='./item.php?id=$id' title='View full record and complete details'>$name</a></p>\n";
			$creators = getCreators($id);
			if ($creators != '')
			{
				echo "<p>Creator" . ((substr_count($creators, ",") == 1)? ": " : "s: ") . getCreators($id) . "</p>\n";
			}
			if ($doi != '')
			{
				echo "<p>DOI: $doi</p>\n";
			}
			if ($url != '')
			{
				echo "<p>URL: <a href='$url' title='Content URL'>$url</a></p>\n";
			}
			echo "</li>\n";
		}

?>
	    </ul>
	</div><!-- end gutter div -->
</div><!-- end main div -->
<?php

include './meta/inc/footer.php';

?>
