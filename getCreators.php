<?php

function reverseNames($name) {
	$commaPosition = strpos($name, ',');

	if (!$commaPosition) {
		return $name;
	}
	else {
		return trim(substr($name, $commaPosition + 1)) . ' ' . trim(substr($name, 0, $commaPosition));
	}
}

function getCreators($id) {

	// Get creators
	$creatorQuery = "
		SELECT creator_key, creator_name
		FROM creators
		WHERE recordInfo_recordIdentifier = $id
		ORDER BY creator_key
	";

	$getCreators = @mysql_query($creatorQuery);

	if (!$getCreators) {
		die('Error retrieving metadata items from database! ' . 'Error: ' .  mysql_error() . '');
	}

	$numCreators = mysql_num_rows($getCreators);

	$creatorCount = 0;
	$creators = '';
	while ($row = mysql_fetch_object($getCreators)) {
		$creatorCount++;
		$creator = stripslashes($row->creator_name);
		switch ($creatorCount) {
			case 1:
				$creators = $creator;
				break;
			case $numCreators:
				$creators .= ', and ' . reverseNames($creator);
				break;
			default:
				$creators .= ', ' . reverseNames($creator);
		}
	}

	return $creators;
}

?>
