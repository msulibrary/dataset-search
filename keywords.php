<?php
// Purpose: This code searches the database for all keywords; results are displayed in a three column list to the user

// Set Title, Description, and Keywords
$pageTitle = 'MSU Dataset Search Keywords';
$pageDescription = 'Alphabetical list of keywords from the MSU Dataset Search database.';
$pageKeywords = 'MSU, dataset, search';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Include page header
include './meta/inc/header.php';
?>

<div id="main">
	<a name="mainContent"></a>
	<div class="gutter">
		<h2 class="mainHeading">Alphabetical List of Keywords</h2>
		<ul id="block">

<?php
		// Get keywords from database
		$query = "
			SELECT dataset_keywords
			FROM datasets
			WHERE status = 'a'
			GROUP BY dataset_keywords
			ORDER BY dataset_keywords ASC
		";
		$getKeywords = mysql_query($query);

		// Extract $keywords into an array
		$keywords = array();
		while ($row = mysql_fetch_assoc($getKeywords)) {
			$rawKeywords = explode(',', $row['dataset_keywords']);
			$keywords = array_merge($keywords, $rawKeywords);
		}

		$keywords = array_map('trim', $keywords); // Remove whitespace
		$keywords = array_filter($keywords); // Remove empties
		$keywords = array_unique($keywords); // Remove duplicates
		natcasesort($keywords);

		// Display keywords to user
		foreach ($keywords as $value) {
			$keyword = strtolower(trim($value));
			echo "<li><a href='./search.php?keyword=\"" . urlencode($keyword) . "\"'>$keyword</a></li>\n";
		}
?>

		</ul><!-- end listbox -->
		<p class="nav return"><a class="bck" href="./index.php">Back to search page</a></p>
	</div><!-- end gutter div -->
</div><!-- end main div -->

<?php
include './meta/inc/footer.php';
?>
