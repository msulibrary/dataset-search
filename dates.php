<?php
// Purpose: This code searches the database for all dates; results are displayed in a two column list to the user

// Set Title, Description, and Keywords
$pageTitle = 'MSU Dataset Search Publication Dates';
$pageDescription = 'Publication dates from the MSU Dataset Search database.';
$pageKeywords = 'MSU, data';

// Select  page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Include page header
include './meta/inc/header.php';
?>

<div id="main">
	<a name="mainContent"></a>
	<div class="gutter">
		<h2 class="mainHeading">Chronological List of Datasets by Year</h2>
    	<ul id="listColumns">

<?php
			// Get dates from database
			$query = "
				SELECT dataset_temporalCoverage
				FROM datasets
				WHERE status = 'a'
				GROUP BY dataset_temporalCoverage
				ORDER BY dataset_temporalCoverage ASC
			";
			$getDates = mysql_query($query);

			if ($getDates)
			{
				// Display dates from $getDates query
				while ($row = mysql_fetch_object($getDates))
				{
					$date = $row->dataset_temporalCoverage;

					// Get count of this date
					$query = "
						SELECT COUNT(*) AS matchCount
						FROM datasets
						WHERE status = 'a'
						AND MATCH (dataset_temporalCoverage) AGAINST ('\"$date\"' IN BOOLEAN MODE)
					";
					$countResult = mysql_query($query);
					$count = mysql_fetch_assoc($countResult);
					$dateCount = $count['matchCount'];

					// Display date to the user
					if ($date != "") {
						echo "<li><a href='./search.php?date=\"" . urlencode($date) . "\"'>$date ($dateCount Article" .
								(($dateCount == 1) ? "" : "s") . ")</a></li>\n";
					}
				}
			}
?>

		</ul><!-- end listColumns -->
		<p class="nav return"><a class="bck" href="./index.php">Back to search page</a></p>
	</div><!-- end gutter div -->
</div><!-- end main div -->

<?php
include './meta/inc/footer.php';
?>
