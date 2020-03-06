<?php
// Purpose: This code searches the database for the user specified search terms and displays matching items

// Set Title, Description, and Keywords
$pageTitle = 'MSU Dataset Search Search';
$pageDescription = 'Search results from the MSU Dataset Search database.';
$pageKeywords = 'MSU, data';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Include page header
include './meta/inc/header.php';

// Number of records to display per page
$recordsPerPage = 15;

// Maximum length of description to be displayed
$maxLengthDescription = 150;

/**
 * Mapping of possible query elements to their corresponding datasets fields
 *
 * Note: To add a new query element type, simply add another line to this array.
 *       Each query element type can map to one or more comma-separated datasets fields.
 */
$queryToDatasetsMap = array (
	"q"          => "creator_name, dataset_name, dataset_keywords, dataset_description",
	"keyword"    => "dataset_keywords",
	"date"       => "dataset_temporalCoverage",
	"creator"    => "creator_name",
	"title"      => "dataset_name",
	"category"   => "dataset_category1, dataset_category2, dataset_category3, dataset_category4, dataset_category5",
	"abstract"   => "dataset_description",
	"identifier" => "recordInfo_recordIdentifier"
);

// QueryData Class
class QueryData
{
	// Mapping of query elements to datasets fields
	var $queryToDatasetsMap;

	// Number of records to display per page
	var $recordsPerPage;

	// Array that holds the query elements and corresponding values
	var $queryArray = Array();

	// Array that holds exploded (separated) datasets fields and their
	// corresponding query values used to highlight matched query values
	var $highlightArray;

	// Starting record to pull on the next query
	var $start;

	// Number of records to pull on the next query
	var $limit;

	/**
	 * QueryData Constructor
	 *
	 * Parameters:
	 *  $queryToDatasetsMap - mapping of query elements to datasets fields
	 *  $recordsPerPage - number of records to display per page
	 */
	function QueryData($queryToDatasetsMap, $recordsPerPage)
	{
		$this->queryToDatasetsMap = $queryToDatasetsMap;
		$this->recordsPerPage = $recordsPerPage;
	}

	/**
	 * add - Add query element and corresponding query value to QueryData object
	 *
	 * Parameters:
	 *  $element - element from query string
	 *  $value - corresponding value from query string
	 */
	function add($element, $value)
	{
		// Save value in queryArray
		if (!isset($this->queryArray[$element])) {
			$this->queryArray[$element] = $value;
		}
		else {
			$this->queryArray[$element] .= " $value";
		}

		// Separate comma-separated datasets fields into individual datasets fields
		$datasetsFields = explode(",", $this->queryToDatasetsMap[$element]);

		// Save query value for each datasets field
		foreach ($datasetsFields as $field) {
			// Remove quote marks and leading + or -
			$value = preg_replace("/(?:[\"\'])|(?:^[-+])/", "", $value);

			// Save value for highlighting
			$this->highlightArray[trim($field)][] = $value;
		}
	}

	/**
	 * setStart - Set value of start property
	 *
	 * Parameter:
	 *   $start - value for start property
	 */
	function setStart($start)
	{
		// Check for valid starting row variable
		if (is_numeric($start)) {
			// Escape it for mysql
			$start = mysql_real_escape_string((int)$start);

			if ($start < 0) {
				// Use default
				$start = 0;
			}
		}
		else {
			// Default value
			$start = '0';
		}

		// Store in class property
		$this->start = $start;
	}

	/**
	 * getStart - Get value of start property
	 *
	 * Returns:
	 *   start property value
	 */
	function getStart()
	{
		return $this->start;
	}

	/**
	 * setLimit - Set value of limit property
	 *
	 * Parameter:
	 *   $limit - value for limit property
	 */
	function setLimit($limit)
	{
		// Check for valid limit result set variable
		if (is_numeric($limit)) {
			// Escape it for mysql
			$limit = mysql_real_escape_string((int)$limit);

			if ($limit <= 0) {
				// Use default
				$limit = $this->recordsPerPage;
			}
		}
		else {
			// Default value
			$limit = $this->recordsPerPage;
		}

		// Store in class property
		$this->limit = $limit;
	}

	/**
	 * getLimit - Get value of limit property
	 *
	 * Returns:
	 *   limit property value
	 */
	function getLimit()
	{
		return $this->limit;
	}

	/**
	 * getSqlQueryString - Get the sql query string
	 *
	 * Returns:
	 *   sql query string
	 */
	function getSqlQueryString()
	{
		$scoreCounter = 0;

		$queryString = 'SELECT *, ';

		$continuation = "";
		foreach ($this->queryArray as $element => $value) {
			// Get datasets field(s) that correspond(s) to search element
			$datasetsField = $this->queryToDatasetsMap[$element];

			// Prepare value for use by mysql
			$value = mysql_real_escape_string($value);

			$queryString .= "$continuation MATCH ($datasetsField) AGAINST ('$value' IN BOOLEAN MODE) AS score" . $scoreCounter++;
			$continuation = ", ";
		}

		$queryString .= " FROM datasets NATURAL JOIN creators WHERE (status = 'a') ";

		foreach ($this->queryArray as $element => $value) {
			// Get datasets field(s) that correspond(s) to search element
			$datasetsField = $this->queryToDatasetsMap[$element];

			// Prepare value for use by mysql
			$value = mysql_real_escape_string($value);

			$queryString .= "AND MATCH ($datasetsField) AGAINST ('$value' IN BOOLEAN MODE) ";
		}

		// Finish the query string
		$queryString .= "GROUP BY datasets.recordInfo_recordIdentifier ORDER BY ";

		$continuation = "";
		for ($score = 0; $score < $scoreCounter; $score++) {
			$queryString .= "{$continuation}score$score";
			$continuation = "+";
		}

		$queryString .= " DESC LIMIT $this->start, $this->limit;";
		return $queryString;
	}

	/**
	 * getSqlQueryCountString - Get the sql query count string
	 *
	 * Returns:
	 *   sql query count string
	 */
	function getSqlQueryCountString()
	{
		$queryCountString = "SELECT COUNT(*) AS matchCount FROM datasets NATURAL JOIN creators WHERE (status = 'a') ";

		foreach ($this->queryArray as $element => $value) {
			// Get datasets field(s) that correspond(s) to search element
			$datasetsField = $this->queryToDatasetsMap[$element];

			// Prepare value for use by mysql
			$value = mysql_real_escape_string($value);

			$queryCountString .= "AND MATCH ($datasetsField) AGAINST ('$value' IN BOOLEAN MODE) ";
		}

		// Finish the query count string
		$queryCountString .= "GROUP BY datasets.recordInfo_recordIdentifier;";

		return $queryCountString;
	}

	/**
	 * getQueryString - Get the query string for previous and next links
	 *
	 * Returns:
	 *   query string used by previous and next links
	 */
	function getQueryString()
	{
		$queryString = "";
		$continuation = "";

		foreach ($this->queryArray as $element => $value) {
			$queryString .= "$continuation$element=" . urlencode($value);
			$continuation = "&amp;";
		}

		return $queryString;
	}

	/**
	 * getSearchTerms - Gets the search terms
	 *
	 * Returns:
	 *   string of search terms
	 */
	function getSearchTerms()
	{
		$searchTerms = "";
		$continuation = "";

		foreach ($this->queryArray as $element => $value) {
			$searchTerms .= $continuation . (($element != "q") ? (ucfirst($element) . " ") : "") . "<strong>$value</strong>";
			$continuation = " and ";
		}

		return $searchTerms;
	}

	/**
	 * getFieldWithHighlight - Gets field value and highlights matching query values
	 *
	 * Parameters:
	 *   $record - mysql record
	 *   $datasetsField - datasets field to extract from the record
	 *
	 * Returns:
	 *   highlighted field value
	 */
	function getFieldWithHighlight($record, $datasetsField)
	{
		// Get the field value
		$fieldValue = $record->{$datasetsField};

		// Check if search includes value(s) for this datasets field
		if (isset($this->highlightArray[$datasetsField]))
		{
			foreach ($this->highlightArray[$datasetsField] as $matchValue)
			{
				// Quote regular expression characters that appear in matchValue
				$matchValue = preg_quote($matchValue, "/");

				// Add the span class around all matches that occur outside of html tags
				$fieldValue = preg_replace("/(?![^<]+>)$matchValue/i", "<span class=\"match\">$0</span>", $fieldValue);
			}
		}
		return $fieldValue;
	}
} // End QueryData class
?>

<div id="main">
	<a name="mainContent"></a>
	<div class="gutter">

<?php
		// True if validation fails
		$bValidationFailure = FALSE;

		// Create the QueryData object
		$queryData = new QueryData($queryToDatasetsMap, $recordsPerPage);

		foreach ($_REQUEST as $queryElement => $queryValue) {
			// Check if valid query element
			if (isset($queryToDatasetsMap[$queryElement])) {

				// String of allowed symbols
				// Don't allow <, &#60, \u003c or >, &#62, \u003e
				$allowedSymbols = preg_quote("\"',.?&:;*+-()[]/", "/");

				// Check for invalid characters
				if ($invalidCount = preg_match_all("/[^a-zé0-9 $allowedSymbols]/i", $queryValue, $matches)) {

/*
 *					$queryValue = htmlspecialchars($queryValue);
 *
 *					foreach ($matches[0] as $invalid) {
 *						$invalid = htmlspecialchars($invalid);
 *						if (!isset($replaced[$invalid])) {
 *							$queryValue = str_replace("$invalid", "<strong>$invalid</strong>", $queryValue);
 *							$replaced[$invalid] = TRUE;
 *						}
 *					}
 */

					$bValidationFailure = TRUE;
					break;
				}

				// Separate the search entries -- keep quoted text together
				preg_match_all("/\"[^\"]+\"|[^\" ]+/", $queryValue, $separatedValuesArray);

/*
 *				// Filter out commas
 *				$queryValuesArray = preg_replace("/[,]/", "", $separatedValuesArray[0]);
 */

				// Trim results and remove empty values
				foreach($separatedValuesArray[0] as $queryValue) {
					$queryValue = trim($queryValue);
					if ($queryValue != "") {
						// Add queryValue to the queryData object
						$queryData->add($queryElement, $queryValue);
					}
				}
			}
		}

		if ($bValidationFailure) {
			// Invalid characters detected
			echo "<h2><strong>Your search contains invalid characters</strong></h2>";
			echo "<hr />";
			/*
			 * Do not reflect user's input back to the browser -- This is a security risk -- 2014-03-10 JE
			 *
			 * echo "<h3>The search string \"$queryValue\" contains " . (($invalidCount == 1) ? "an " : "") .
			 *      "invalid character" . (($invalidCount == 1) ? "" : "s") . "!<br/><br/>
			 *      Please use your browser's <strong>BACK</strong> button and fix the error. Then resubmit your request.</h3>";
             */
            // Here is a secure response
            echo "For a list of valid characters, see the <a href='./help.php'>Help Searching Page</a>.<br/><br/>";
            echo "Please use your browser's <strong>BACK</strong> button and change your input. Then resubmit your request.</h3>";
		}
		elseif ($queryData->getQueryString() == "") {
			// No search element found
			echo "<h2><strong>There is missing information in your request!</strong></h2>";
			echo "<hr />";
			echo "<h3>Please enter a value to search for.<br />";
			echo "<br />Use your browsers <strong>BACK</strong> button and enter the missing information.</h3>";
		}
		else {
			// Valid request

			// Set the start and limit parameters
			$queryData->setStart(isSet($_REQUEST['start']) ? $_REQUEST['start'] : 0);
			$queryData->setLimit(isSet($_REQUEST['limit']) ? $_REQUEST['limit'] : $recordsPerPage);

			// Perform the query
// echo $queryData->getSqlQueryString() . "<br/>";
			$result = @mysql_query($queryData->getSqlQueryString());

            // This only returns the number of records in the actual query which will be a maximum of $recordsPerPage
//          $numRecords = mysql_num_rows($result);

// echo $queryData->getSqlQueryCountString() . "<br/>";

            $countResult = @mysql_query($queryData->getSqlQueryCountString());
			$numRecords = mysql_num_rows($countResult);

			if ($numRecords == 0) {
				echo "<h2><strong>There are no resulting matches</strong></h2>\n";
				echo "<hr />\n";
				echo "<h3>There are no items in the database that match your search value(s): " . $queryData->getSearchTerms() .
				     "<br /><br />Please try again!!!</h3>\n";
			}
			else {
				echo "<h2 class=\"mainHeading\">Your search for " . $queryData->getSearchTerms() .
					"resulted in <strong>$numRecords</strong> record" .
					(($numRecords == 1) ? "" : "s") . ".</h2>\n";
				echo "<p class=\"nav\"><a class=\"bck\" href=\"./index.php\">Back to Home page</a></p>";

				$start = $queryData->getStart();
				$limit = $queryData->getLimit();

				// Create links to more items if there are more than $limit items
				$prevLink = "";
				$nextLink = "";
				if ($numRecords > $limit) {
					// Create a link to the previous items if there are previous items to display
					if ($start > 0) {
						$prevStart = $start - $limit;
						$prevGroup = $limit;

						if ($prevStart < 0) {
							// This condition should only be hit if the url is altered manually
							$prevGroup += $prevStart;
							$prevStart = 0;
						}

						$prevLink = "<a class=\"bck\" href=\"./search.php?{$queryData->getQueryString()}&start=$prevStart&limit=$prevGroup\">" .
									"View previous $prevGroup result" . (($prevGroup == 1) ? "" : "s") . "</a>&nbsp;&nbsp;&nbsp;";
					}

					// Create a link to the next items if there are more items to display
					$nextStart = $start + $limit;
					if ($nextStart < $numRecords) {
						$remainingRecords = $numRecords - $nextStart;
						$nextGroup = ($remainingRecords < $limit) ? $remainingRecords : $limit;
						$nextLink = "<a class=\"fwd\" href=\"./search.php?{$queryData->getQueryString()}&start=$nextStart&limit=$limit\">" .
								"View next $nextGroup result" . (($nextGroup == 1) ? "" : "s") . "</a>";
					}
				}
				else {
					// Set the max number of items to the number of records if less than $limit
					$limit = $numRecords;
				}

				// Display links to more items at top of results page if there are more than $limit items
				if ($prevLink != "" || $nextLink != "") {
					echo "<p class=\"nav\">";
					echo "$prevLink$nextLink";
					echo "</p>";
				}

				// Display all summary information about matching item(s)
				while ($record = mysql_fetch_object($result)) {
					$id = $record->recordInfo_recordIdentifier;
					$title = $queryData->getFieldWithHighlight($record, "dataset_name");
					$date = $queryData->getFieldWithHighlight($record, "dataset_temporalCoverage");
//					$creator = html_entity_decode($queryData->getFieldWithHighlight($record, "creator_name"));

					$description = substr($queryData->getFieldWithHighlight($record, "dataset_description"), 0, $maxLengthDescription);
					$keywords = strtolower($queryData->getFieldWithHighlight($record, "dataset_keywords"));

					// Get object number
					$path = explode('/', $record->recordInfo_recordIdentifier);
					$filename = $path[count($path)-1];
					$object = strtok($filename, ".");

					// Display the info to the user
					echo "<dl>";
					echo "<dt><strong>Title:</strong> <a href=\"item.php?id=$id\">$title</a></dt>";
					echo "<dd><strong>Date:</strong> $date</dd>";
					echo "<dd><strong>Creators:</strong> ";

					// Include all creators from creators table
					$creatorQuery = "
						SELECT creator_name
						FROM creators
						WHERE recordInfo_recordIdentifier='$id' 
						ORDER BY creator_key;
					";

					$getAuthors = @mysql_query($creatorQuery);

					if (!$getAuthors) {
						die("<h2>Error fetching creators: " . mysql_error() . "</h2>");
					}

					$separator = "";
					while ($creator = mysql_fetch_array($getAuthors)) {
						echo $separator . stripslashes($creator['creator_name']);
						$separator = ", ";
					}

					echo "</dd>";
					echo "<dd><strong>Description:</strong> $description" . ((strlen($description) >= $maxLengthDescription) ? "..." : "") . "</dd>";
					echo "<dd><strong>Object ID:</strong> $object</dd>";
					echo "<dd><strong>Keywords:</strong> $keywords</dd>";
					echo "<dd><a class=\"expand\" href=\"./item.php?id=$id\">View additional details about $record->dataset_name</a></dd>";
					echo "</dl>";
					echo "<hr />";
				}

				// Display links to more items at bottom of results page if there are more than $limit items
				if ($prevLink != "" || $nextLink != "") {
					echo "<p class=\"nav\">";
					echo "$prevLink$nextLink";
					echo "</p>";
				}

			} // End - if ($numRecords == 0) else

			echo "<p class=\"nav\"><a class=\"bck\" href=\"./index.php\">Back to Home page</a></p>\n";

		} // End - else // Valid request
?>

	</div><!-- End gutter div -->
</div><!-- End main div -->

<?php
include './meta/inc/footer.php';
?>
