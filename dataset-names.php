<?php
// Purpose: This code searches the database for all titles; results are displayed in a two column list to the user

// Set Title, Description, and Keywords
$pageTitle = 'MSU Dataset Search Titles';
$pageDescription = 'This list gives the titles from the MSU Dataset Search database.';
$pageKeywords = 'MSU, data';

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
		<h2 class="mainHeading">Alphabetical List of MSU Dataset Search Entries</h2>
		<p>

<?php
		/**
		 * Groupings of subHeadings
		 *
		 * Note: To change the groupings, simply edit this array.  The last element
		 *       is "special".  It is for all titles that start with an ascii
		 *       character < 'a', most likely a number.
		 */
		$groupings = array(
			"abc",
			"def",
			"ghi",
			"jkl",
			"mno",
			"pqr",
			"stu",
			"vwx",
			"yz",
			"other"
		);

		$lastIndex = count($groupings) - 1;

		$subHeading = array();
		$href = array();
		$firstChar = array();
		$lastChar = array();

		// Populate the subHeading, href, firstChar, and lastChar arrays
		foreach ($groupings as $grouping) {
			$href[$grouping] = $grouping;

			if ($grouping == $groupings[$lastIndex]) {
				// Last (special) element in array
				$subHeading[$grouping] = ucwords(strtolower($grouping));
				$firstChar[$grouping] = null;
				$lastChar[$grouping] = chr(ord('a') - 1);
			}
			else {
				$subHeading[$grouping] = "";
				for ($i = 0; $i < strlen($grouping); $i++) {
					// Put a space in between each character
					$subHeading[$grouping] .= strtoupper($grouping[$i]) . " ";
				}

				$firstChar[$grouping] = $grouping[0];

				if ($grouping == $groupings[$lastIndex - 1]) {
					// Last alphabetic heading
					$lastChar[$grouping] = null;
				}
				else {
					$lastChar[$grouping] = substr($grouping, -1);
				}
			}
		}

		$continue = "";
		foreach ($groupings as $grouping) {
			echo "$continue<a href=\"#$href[$grouping]\">$subHeading[$grouping]</a>";
			$continue = " | ";
		}
?>

		</p>

<?php
		foreach ($groupings as $grouping) {
			echo "<h2 class=\"subHeading\"><a name=\"$href[$grouping]\"></a>$subHeading[$grouping]&nbsp;<a href=\"#mainContent\">[^]</a></h2>";
			echo "<ul class=\"list\">";

			// Request resources with requested letters in title
			$having = "HAVING ";
			if ($lastChar[$grouping] == null) {
				$having .= "datasets_title_sort >= '" . $firstChar[$grouping] . "'";
			}
			elseif ($firstChar[$grouping] == null) {
				$having .= "datasets_title_sort <= '" . chr(ord($lastChar[$grouping]) + 1) . "'";
			}
			else {
				$having .= "datasets_title_sort >= '" . $firstChar[$grouping] . "' AND datasets_title_sort <= '" . chr(ord($lastChar[$grouping]) + 1) . "'";
			}

			$getItems = @mysql_query(
				"SELECT dataset_name,
				COUNT(recordInfo_recordIdentifier) AS title_count,
					CASE WHEN SUBSTRING_INDEX(dataset_name, ' ', 1)
							IN ('a', 'an', 'the')
						THEN CONCAT(
							SUBSTRING(dataset_name, INSTR(dataset_name, ' ') + 1),
							', ',
							SUBSTRING_INDEX(dataset_name, ' ', 1)
						)
						ELSE dataset_name
					END AS datasets_title_sort
				FROM datasets
				WHERE status = 'a'
				GROUP BY datasets_title_sort" .
				" $having " .
				"ORDER BY datasets_title_sort ASC"
			);

			if (!$getItems) {
				die("<p>Error retrieving resources from database!<br/>".
					"Error: " . mysql_error() . "</p></div></div>");
			}

			// Display selected resource entry fields in a list
			while ($row = mysql_fetch_array($getItems)) {
				$item_title = stripslashes(html_entity_decode($row['dataset_name']));
				$titleCount = $row['title_count'];
				echo "<li><a href='./search.php?title=\"" . urlencode($item_title) . "\"'>$item_title</a></li>\n";
			}
			echo "</ul>";
		}
?>

		<p class="nav return">
			<a class="bck" href="./index.php">Back to Homepage</a>
		</p>
	</div><!-- end gutter div -->
</div><!-- end main div -->

<?php
include './meta/inc/footer.php';
?>
