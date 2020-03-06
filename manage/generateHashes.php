<?php
// Handle command line switches
$refresh = $verbose = $testMode = false;

foreach ($argv as $argument) {
	switch ($argument) {
		case "-refresh":
			$refresh = true;
			break;
		case "-verbose":
			$verbose = true;
			break;
		case "-test":
			$testMode = $verbose = true;
			break;
	}
}

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

// Get identifiers, titles, and hashes from database
$getInfo = mysql_query("SELECT recordInfo_recordIdentifier, titleInfo_title, relatedItem_physicalDescription_hash FROM datasets;");

while ($row = mysql_fetch_object($getInfo)) {
	$recordId = $row->recordInfo_recordIdentifier;
	$title = $row->titleInfo_title;
	$hash = $row->relatedItem_physicalDescription_hash;

	if ($refresh || $hash == null) {

		// Normalize the the title

		// Convert to all lower case
		$title = strtolower($title);

		// Replace & with and
		$title = str_replace('&', 'and', $title);

		// Replace any non-alphanumeric characters with underscores
		$title = preg_replace('/[^a-z0-9]+/i', "_", $title);
		
		// Generate the sha1 hash of the normalized title
		$hash = sha1($title);

		if ($verbose) {
			echo "$recordId $title $hash\n";
		}

		// Check for existence of this hash in the database
		$hashCheck = mysql_query("
			SELECT recordInfo_recordIdentifier
			FROM datasets
			WHERE dataset_urlHash = '$hash';
		");

		if (!$hashCheck) {
			echo "Record $recordId: hash check query failed\n";
		}
		elseif (mysql_num_rows($hashCheck) != 0) {

			// Clear the duplicates array
			$duplicates = array();
			$duplicates[] = $recordId;

			while ($dupRow = mysql_fetch_object($hashCheck)) {
				if ($recordId < $dupRow->recordInfo_recordIdentifier) {
					$duplicates[] = $dupRow->recordInfo_recordIdentifier;
				}
			}

			if (count($duplicates) > 1) {
				echo "Duplicates detected: ";

				foreach ($duplicates as $duplicate) {
					echo "$duplicate ";
				}

				echo "\n";
			}
		}

		// Update the database
		$update = "
			UPDATE datasets
			SET dataset_urlHash = '$hash'
			WHERE recordInfo_recordIdentifier = '$recordId';
		";

		if ($verbose) {
			echo str_replace(array("\r", "\t"), " ", $update) . "\n\n";
		}

		if (!$testMode) {
			if (!mysql_query($update)) {
				echo "Record $recordId: database update failed\n";
			}
			elseif ($verbose) {
				echo "Record $recordId: successfully updated\n";
			}
		}
	}
}
?>
