<?php

/*
**	AJAX action to get feed content
*/

// Get POST data
$feedPublisher = isset($_POST['feedPublisher']) ? $_POST['feedPublisher'] : '';
$feedUrl = isset($_POST['feedUrl']) ? $_POST['feedUrl'] : '';
$feedContentType = isset($_POST['feedContentType']) ? $_POST['feedContentType'] : '';

/********************************************************************************/

// Load the structure.xml file that shows how to parse the feeds
$feedStructureXml = simplexml_load_file(__DIR__ . '/feedStructure.xml') or die('feedStructure file not loading');

/********************************************************************************/

// Initialize return variables
$extractedData = Array();
$feedStatus = "success";

// Initialize variable that holds path to each structure element
$structure = '';

// Get path to each feed element of interest
foreach ($feedStructureXml->feed as $feed)
{
	if ($feed->publisher == $feedPublisher)
	{
		$entryTag = trim($feed->entryTag);

		$caseSensitiveTags = "true";
		if (isset($feed->caseSensitiveTags))
		{
			$caseSensitiveTags = trim($feed->caseSensitiveTags);
		}

		// Get entry path for this feed
		$entryPath = get_entry_path($feed->feedStructure, ".", $entryTag);

		if ($entryPath == '.')
		{
			$entryXPath = $feed->xpath("feedStructure" . "/" . $entryTag)[0];
		}
		else
		{
			$entryXPath = $feed->xpath("feedStructure" . $entryPath . "/" . $entryTag)[0];
		}

		$structure['title']       = get_element_path($entryXPath, 'title');
		$structure['link']        = get_element_path($entryXPath, 'link');
		$structure['description'] = get_element_path($entryXPath, 'description');
		$structure['creator']     = get_element_path($entryXPath, 'creator');
		$structure['pubDate']     = get_element_path($entryXPath, 'pubDate');
		$structure['uid']         = get_element_path($entryXPath, 'uid');

		break;
	}
}

if ($feedUrl == ''  || $entryPath == '')
{
	$feedXml = false;
}
else
{
	// Retrieve contents from API or RSS feed using curl
	$curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, $feedUrl);
	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

	if ($feedContentType == 'x')
	{
		curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('accept: application/xml'));
	}
	else
	{
		curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('content-type: application/json, accept: application/json'));
	}

	$feedContents = curl_exec($curlSession);
	curl_close($curlSession);

/*
	// The following (simpler) code would also work to get contents from an RSS feed
	$feedContents = file_get_contents($feedUrl);
*/

	/***********************************************************************************************/

	// If JSON is returned, transform it into XML

	if ($feedContentType == 'j')
	{
		$xmlString = '<root>';

		convert_json_to_xml(json_decode($feedContents, true), "", $xmlString);

		$xmlString .= '</root>';

		$feedContents = $xmlString;
	}

	if ($caseSensitiveTags === "false")
	{
		/***********************************************************************************************/

		// Transform feed contents xml to all lowercase tags using XSL

		$xml = new DOMDocument;
		$xml->loadXML($feedContents);
		$xsl = new DOMDocument;
		$xsl->load('lowercase_tags.xsl');
		$xsltProcessor = new XSLTProcessor();
		$xsltProcessor->importStyleSheet($xsl);
		$feedContents = $xsltProcessor->transformToXML($xml);

		// Load feed into feedXml object
		$feedXml = simplexml_load_string($feedContents);

		/************************************************************************************************

		// Transform the feed contents xml to all lowercase tags without using XSL

		// Load feed into feedXml object
		$feedXml = simplexml_load_string($feedContents, 'SimpleXMLIterator');

		// Create iterator
		$iterator = new RecursiveIteratorIterator($feedXml, RecursiveIteratorIterator::CHILD_FIRST);

		// Use iterator to save list of all nodes (because replaceChild affects the iterator)
		$nodes = array();
		foreach ($iterator as $node)
		{
			$nodes[] = $node;
		}

		// Now iterate through saved list of all nodes
		foreach ($nodes as $node)
		{
			// Get current node as DOMElement
			$currentNode = dom_import_simplexml($node);

			// Save all children of current node
			$childNodes = array();
			foreach ($currentNode->childNodes as $child)
			{
				$childNodes[] = $child;
			}

			// Create new node with lowercase key
			$newNode = $currentNode->ownerDocument->createElement(strtolower($currentNode->nodeName));

			// Append children of current node to new node
			foreach ($childNodes as $child)
			{
				$newNode->appendChild($currentNode->ownerDocument->importNode($child, true));
			}

			// Copy attributes of current node to new node
			foreach ($currentNode->attributes as $attribute)
			{
				$newNode->setAttribute($attribute->name, $attribute->value);
			}

			// Replace current node with new node
			$currentNode->parentNode->replaceChild($newNode, $currentNode);
		}

		************************************************************************************************/
	}
	else
	{
		// Load feed into feedXml object
		$feedXml = simplexml_load_string($feedContents);
	}
}

if ($feedXml === false)
{
	$feedStatus = "failure";
/*
	// Debug
	echo "Failed loading XML: ";

	foreach (libxml_get_errors() as $error)
	{
		echo $error->message;
	}
	libxml_clear_errors();
*/
}
else
{
	// Initialize item id
	$feedItemId = 0;

	// Trim leading slash (/) and then follow entryPath
	$entryPathTags = explode('/', ltrim($entryPath, '/'));

	// Skip the first tag since that is where you already are
	foreach (array_slice($entryPathTags, 1) as $entryPathTag)
	{
		$feedXml = $feedXml->$entryPathTag;
	}

	// Now extract data from each entry
	foreach ($feedXml->$entryTag as $feedItem)
	{
		// Create an object to hold the extracted data
		$entry = new stdClass;

		// Check each tagged field in feedItem
		foreach ($feedItem->children() as $feedField)
		{
			$feedTag = $feedField->getName();

			$value = (string)$feedField->__toString();

			// Check for tag match from feedStructure.xml
			foreach ($structure as $structureElement => $structureTag)
			{
				// Trim off leading '/' from structureTag
				$structureTag = ltrim($structureTag, '/');

				// find_match returns true if a match is found
				find_match($feedField, $feedTag, $value, $structureElement, $structureTag, $entry);
			}
		}

		// Normalize the date
		if (isset($entry->pubDate))
		{
			// Use php strtotime to normalize date
			$entry->pubDate = date('d M Y', strtotime($entry->pubDate));

			// Check for invalid date (some dates cannot be converted by strtotime)
			if ($entry->pubDate == '31 Dec 1969')
			{
				unset($entry->pubDate);
			}
		}

		$extractedData[] = $entry;

	}
}


// Encode return parameter as a json array
echo json_encode(
	array(
		"feedPublisher" => $feedPublisher,
		"feedId"        => str_replace(" ", "_", $feedPublisher),
		"feedStatus"    => $feedStatus,
		"extractedData" => $extractedData
	)
);


/*
**	find_match - recursive function that returns true if structure tag is found
*/
function find_match($feedField, $feedTag, $value, $structureElement, $structureTag, $entry)
{
	// Check for simple match
	if ($feedTag == $structureTag)
	{
		// Found matching tag
		// echo "<br>Found Match!  $structureElement = $value<br>";
		$entry->$structureElement = $value;
		return true;;
	}

	// Check for attribute
	if (strpos($structureTag, '{') !== false)
	{
		// Separate target tag and attribute
		$targetTag = explode('{', $structureTag)[0];
		$attribute = explode('{', rtrim($structureTag, '}'))[1];

		if ($feedTag == $targetTag)
		{
			// Return attribute value
			// echo "<br>Found Match!  $structureElement = $feedField[$attribute]<br>";
			$entry->$structureElement = (string)$feedField[$attribute];
			return true;
		}
	}

	// Look for multi-level match
	if (($feedField->count()) > 0 && (strpos($structureTag, '/') !== false))
	{
		// echo "<br>Looking for multi-level...<br>";

		if ((string)$feedField->getName() == substr($structureTag, 0, strpos($structureTag, '/')))
		{
			foreach ($feedField->children() as $feedChild)
			{
				$newStructureTag = substr($structureTag, strpos($structureTag, '/') + 1);

				// Recursive call to look for match with child tag
				if (find_match($feedChild, $feedChild->getName(), (string)$feedChild->__toString(), $structureElement, $newStructureTag, $entry))
				{
					return true;
				}
			}
		}
	}
	return false;
}


/*
**	get_entry_path - returns path to entry tag
*/
function get_entry_path($feedStructure, $feedPath, $entryTag)
{
	foreach ($feedStructure->xpath($feedPath)[0]->children() as $child)
	{
		if ($child->getName() == $entryTag)
		{
			return($feedPath);
		}

		// Look deeper
		$entryPath = get_entry_path($child, '.', $entryTag);
		if ($entryPath != null)
		{
			if ($entryPath == '.')
			{
				return("/" . $child->getName());
			}
			else
			{
				return("/" . $child->getName() . $entryPath);
			}
		}
	}
	return(null);
}


/*
**	get_element_path - recursive function that returns the path to the given element tag
*/
function get_element_path($elementPath, $elementTag)
{
	if (trim($elementPath->__toString()) == $elementTag)
	{
		return(trim($elementPath->__toString()));
	}

	foreach ($elementPath->children() as $child)
	{
		// __toString() returns text content within this element (use trim to remove leading and trailing whitespace)
		if (trim($child->__toString()) == $elementTag)
		{
			return("/" . $child->getName());
		}
		else
		{
			foreach($child->attributes() as $type => $value)
			{
				if ($value == $elementTag)
				{
					return(trim($elementPath->__toString()) . "/" . $child->getName() . "{" . $type . "}");
				}
			}
		}

		// Looking deeper ...
		$elementPathString = get_element_path($child, $elementTag);
		if ($elementPathString != null)
		{
			return("/" . $child->getName() . $elementPathString);
		}
	}
	return(null);
}


/*
**	convert_json_to_xml - converts json array to xml
*/
function convert_json_to_xml($array, $parent_tag, &$xml)
{
	foreach($array as $tag => $value)
	{
		if (is_int($tag))
		{
			$tag = $parent_tag;
		}

		if (!is_array($value) || is_associative($value))
		{
			$xml .= ("<" .$tag. ">");
		}

		if (is_array($value))
		{
			convert_json_to_xml($value, $tag, $xml);
		}
		else
		{
			$xml .= htmlspecialchars($value);
		}

		if (!is_array($value) || is_associative($value))
		{
			$xml .= ("</" .$tag. ">");
		}
	}
}


/*
**	is_associative - returns true if array is an associative array
*/
function is_associative($array)
{
	return array_keys($array) !== range(0, count($array) - 1);
}
