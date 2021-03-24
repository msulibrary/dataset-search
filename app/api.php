<?php
//sample API calls
//api.php?v=1&type=item&id=1&format=json
//api.php?v=1&type=item&id=1&format=xml
//api.php?v=1&type=search&q=renee&limit=10&format=xml
//api.php?v=1&date=2014-10&format=json

//set value for API version
$v = isset($_GET['v']) ? strip_tags((int)$_GET['v']) : null;
//set value for page length (number of items to display)
$limit = isset($_GET['limit']) ? strip_tags(intval($_GET['limit'])) : 10; 
//set value for result format
$format = strip_tags(htmlentities(strtolower($_GET['format']))) == 'json' ? 'json' : 'xml'; 
//set value for id
$id = isset($_GET['id']) ? strip_tags((int)$_GET['id']) : null;
//type of of API query - options: search, item
$type = isset($_GET['type']) ? strip_tags(htmlentities($_GET['type'])) : null;
//set batch $date variable, escape the string for mysql, and validate that it is a numeric value
$date = isset($_GET['date']) ? strip_tags($_GET['date']) : null;
//set query variable, trim whitespaces/tabs, strip html tags
$q = isset($_GET['q']) ? trim(strip_tags(urlencode($_GET['q']))) : null;

//bring database parameters and functions onto page
include_once './meta/assets/dbconnect.inc';

//get items from database
if ($type == 'search')
{
	$query = "
		SELECT datasets.*
		FROM datasets natural join creators
		WHERE MATCH (dataset_name, dataset_doi, dataset_repositoryName, dataset_description, dataset_temporalCoverage, creator_name, creator_orcid)
		AGAINST ('$q' IN BOOLEAN MODE)
		AND status = 'a'
		GROUP BY recordInfo_recordIdentifier
		ORDER BY recordInfo_recordIdentifier
		LIMIT $limit
	";
}
elseif ($type == 'item')
{
	$query = "
		SELECT *
		FROM datasets
		WHERE recordInfo_recordIdentifier = $id
		AND status = 'a'
	";
}
elseif (!is_null($date))
{
	$query = "
		SELECT *
		FROM datasets
		WHERE dataset_temporalCoverage LIKE '$date%'
		AND status = 'a'
		GROUP BY recordInfo_recordIdentifier
		ORDER BY recordInfo_recordIdentifier
		LIMIT $limit
	";
}
else
{
	writeHTML();
/*$query = "
		SELECT *
		FROM datasets
		WHERE recordInfo_recordIdentifier = $id
		AND status = 'a'
	";*/
}

$result = mysql_query($query) or die('Errant query:  '.$query);

//create a master array of the records
$items = array();
if (mysql_num_rows($result))
{
	$count = 0;
	while($item = mysql_fetch_assoc($result))
	{
		$items[] = array('item'=>$item);
		// echo var_dump($items);
		$id = $items[$count]['item']['recordInfo_recordIdentifier'];

		$creatorQuery = "
			SELECT creator_key, creator_name
			FROM creators
			WHERE recordInfo_recordIdentifier = $id
			ORDER BY creator_key
		";

		$getCreators = mysql_query($creatorQuery);

		$creatorCount = 0;
		while ($creator = mysql_fetch_assoc($getCreators))
		{
			$creator_names[] = array('creator_name'=>$creator['creator_name']);

			$creatorKey = $creator['creator_key'];

//			$items[$count]['item']['creators'] = $creator_names;

			$affiliationsQuery = "
				SELECT affiliation_key, name_affiliation_msuCollege, name_affiliation_msuDepartment, name_affiliation_otherAffiliation
				FROM affiliations
				WHERE creator_key = $creatorKey
				ORDER BY affiliation_key;
			";

			$getAffiliations = mysql_query($affiliationsQuery);

			while ($affiliation = mysql_fetch_assoc($getAffiliations))
			{
					if (isset($affiliation['name_affiliation_msuCollege']) && $affiliation['name_affiliation_msuCollege'] != '')
					{
						$affiliations[] = array('college'=>$affiliation['name_affiliation_msuCollege']);
					}

					if (isset($affiliation['name_affiliation_msuDepartment']) && $affiliation['name_affiliation_msuDepartment'] != '')
					{
						$affiliations[] = array('department'=>$affiliation['name_affiliation_msuDepartment']);
					}

					if (isset($affiliation['name_affiliation_otherAffiliation']) && $affiliation['name_affiliation_otherAffiliation'] != '')
					{
						$affiliations[] = array('other_affiliation'=>$affiliation['name_affiliation_otherAffiliation']);
					}

					$creator_names[] = array('affiliation'=>$affiliations);

					unset($affiliations);
			}

			$items[$count]['item']['creators'][$creatorCount] = array('creator'=>$creator_names);
			unset($creator_names);
			$creatorCount++;
		}

//		var_dump($items[$count]['item']['creators']);
// 		echo var_dump($creator_names);

		// Done with $creator_names array
//		unset($creator_names);

		$count++;	
	}
}

//output in requested format
if ($format == 'json')
{
	header('Content-type: application/json');
	header("access-control-allow-origin: *");
	echo json_encode(array('items'=>$items));	
}
else
{
	header('Content-type: text/xml');
	echo '<?xml version="1.0"?>';
	echo '<items>';
	foreach($items as $tag => $value)
	{
		writeXML($tag, $value);
	}
	echo '</items>';
}

function writeXML($tag, $value)
{
	if (!is_int($tag))
	{
		echo '<',$tag,'>';
	}

	if (!is_array($value))
	{
		echo htmlspecialchars($value);
	}
	else
	{
		foreach ($value as $tag2 => $value2)
		{
			writeXML($tag2, $value2);
		}
	}

	if (!is_int($tag))
	{
		echo '</',$tag,'>';
	}
}

function writeHTML ()
{
	echo '<h2>Welcome to our prototype read-only API.</h2>';
	echo '<h3>You can use the sample API calls below to get started.</h3>';
	echo '<p><strong>Retrieve an item:</strong> <code>api.php?v=1&type=item&id=1&format=json</code> OR <code>api.php?v=1&type=item&id=1&format=xml</code></p>';
	echo '<p><strong>Search for items:</strong> <code>api.php?v=1&type=search&q=renee&limit=10&format=xml</code></p>';
	echo '<p><strong>Retrieve items by date:</strong> <code>api.php?v=1&date=2014-10&format=json</code></p>';
}
// Disconnect from database
// mysql_close($link);
