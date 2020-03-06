<?php

// Set Title, Description, and Keywords
$pageTitle = 'MSU Dataset Search - Autopopulate Database';

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/master.css';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$bodyClass = 'rightCol default';

// Load jQuery
$jQuery = "../meta/scripts/jquery-1.9.1.min.js";

// Include page header
include '../meta/inc/header-autoPop.php';

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

// Get feeds
$feedsQuery = "
	SELECT * from feeds
	WHERE status = 'a'
	ORDER BY relatedItem_originInfo_feed_identifier
";

$getFeeds = @mysql_query($feedsQuery);

if (!$getFeeds)
{
	die("<p>Error retrieving resources from database!<br/>" .
		"Error: " . mysql_error() . "</p></div></div>");
}

// Preset $idNumber
$idNumber = 0;

$feeds = Array();

// Use red to denote feeds that failed
$feedColor = Array();

// Display selected resource entry fields in a list
while ($row = mysql_fetch_object($getFeeds))
{
	$feeds[trim($row->relatedItem_originInfo_feed_publisher)] = trim($row->relatedItem_originInfo_feed_url) . '|' . $row->relatedItem_originInfo_feed_contentType;
}
?>

<script>

$(document).ready(function() 
{
	// Calculate available height for datasetDiv
    var datasetHeight = $(window).height() - $('#mastHead').outerHeight(true) - (($('#main .gutter').outerHeight(true) - $('#main .gutter').height())) - $('.mainHeading').outerHeight(true) - $('#footer').outerHeight(true);

	$('#datasetDiv').height(datasetHeight);

	// Calculate available height for configDiv
    var configHeight = $(window).height() - $('#mastHead').outerHeight(true) - (($('#sideBar .gutter').outerHeight(true) - $('#sideBar .gutter').height())) - $('#footer').outerHeight(true);

	$('#configDiv').height(configHeight);

	feedChange();
	adminChange();
	getNextFeedContent();
});

var feeds = {
<?php

	$continuation = "";
	foreach ($feeds as $feedPublisher => $feedUrl)
	{
		echo "$continuation'$feedPublisher':'$feedUrl'";
		$continuation = ",";
	}

?>
};

var feedIds = [
<?php

	$continuation = "";
	foreach ($feeds as $feedPublisher => $feedUrl)
	{
		echo "$continuation'" . str_replace(" ", "_", $feedPublisher) . "'";
		$continuation = ",";
	}

?>
];

function isChecked(checkbox)
{
	return $('#' + checkbox).prop('checked') ? true : false;
}

function enforceClassVisibility(checkbox)
{
	if (isChecked(checkbox))
	{
		$('.' + checkbox).show();
	}
	else
	{
		$('.' + checkbox).hide();
	}
}

function adminChange()
{
	enforceClassVisibility('Feed');
	enforceClassVisibility('ExtractedData');
	enforceClassVisibility('Status');

	if (isChecked('ExtractedData') || isChecked('Status'))
	{
		$('.separator').show();
	}
	else
	{
		$('.separator').hide();
	}

	// Make sure hidden lines remain hidden
	$('.hidden').hide();
}

function feedChange()
{
	for (i in feedIds)
	{
		enforceClassVisibility(feedIds[i]);
	}

	// Make sure hidden lines remain hidden
	$('.hidden').hide();
}

function setStatus(id, recordId, dbStatus)
{
	// Ajax call to set the status of a database record
	$.ajax({
		type: "POST",
		url:  "./setStatus.php",
		data:
		{
			id:       id,
			recordId: recordId,
			dbStatus: dbStatus
		},
		dataType: "json",
		success:  setStatusSuccess
	});
}

function setStatusSuccess(data)
{
	if (data != null)
	{
		var id = data.id;
		var status = data.status;

		if (status == "success")
		{
			$('#'+id).addClass('hidden');
			$('#'+id).hide();
		}
		else
		{
			alert(status);
		}
	}
}

function getNextFeedContent()
{
	if (Object.keys(feeds).length > 0)
	{
		var key = Object.keys(feeds)[0];
		var feedInfo = feeds[key].split('|');
		getFeedContent(key, feedInfo[0], feedInfo[1]);
		delete feeds[key];
	}
}

function getFeedContent(feedPublisher, feedUrl, feedContentType)
{
	// Show ajax spinner
	$("#selectFeeds").append("<dd id=\"spinner\"><img src=\"../meta/img/spinner.gif\">retrieving data from " + feedPublisher + "</dd>");

	// Ajax call to get feed content
	$.ajax({
		type: "POST",
		url:  "./getFeedContent.php",
		data:
		{
			feedPublisher: feedPublisher,
			feedUrl: feedUrl,
			feedContentType: feedContentType
		},
		dataType: "json",
		success:  getFeedContentSuccess
	});
}

function getFeedContentSuccess(data)
{
	if (data != null)
	{
		var feedPublisher = data.feedPublisher;
		var feedId = data.feedId;
		var feedStatus = data.feedStatus;
		var extractedData = data.extractedData;

		// Remove ajax spinner
		$("#spinner").remove();

		if (feedStatus == "success")
		{
			feedColor = "black";
			checked = "checked"
		}
		else
		{
			feedColor = "red";
			checked = ""
		}

		// Add entry to feed list
		$("#selectFeeds").append("<dd style=\"color:" + feedColor + "\"><input type=\"checkbox\" id=\"" + feedId + "\" onchange=\"feedChange()\" " + checked + "> " + feedPublisher + "</dd>");

		// Add feed div
		$("#datasetDiv").append("<div class=\"" + feedId + "\" style=\"display:" + (isChecked(feedId) ? 'block' : 'none') + "\" id=\"" + feedId + "_Div" + "\">");

		// Add feed delimeter to feed div
		$("#" + feedId + "_Div").append("<h2 class=\"Feed\" style=\"background-color:#D0D0FF; display:" + (isChecked('Feed') ? 'block' : 'none') + "\">" + feedPublisher);

		// extractedData.length will be 0 if feedStatus != "success"
		for (var i = 0; i < extractedData.length; i++)
		{
			// Add item div to feed div
			$("#" + feedId + "_Div").append("<div style=\"display:block" + "\" id=\"" + feedId + i + "\">");

			// Add extracted data item
			$("#" + feedId + i).append("<div class=\"ExtractedData\" style=\"background-color:#D0FFD0; display:" + (isChecked('ExtractedData') ? 'block' : 'none') + "\" id=\"" + feedId + "_ExtractedData_" + i + "\">");

			$.each(extractedData[i], function(key, value)
			{
				if (key != "status" && key != "recordId")
				{
					$("#" + feedId + "_ExtractedData_" + i).append("<b>" + key + ":</b> " + value + "<br>");
				}
			});

			$("#" + feedId + i).append("<div class=\"Status\" id=\"" + feedId + "_Status_" + i + "\" style=\"background-color:#FFD0D0;display:" + (isChecked('Status') ? 'block' : 'none') + "\">");

			$("#" + feedId + "_Status_" + i).append("<span>" + extractedData[i]['status'] + "</span>");

			if (extractedData[i]['status'] == "new dataset")
			{
				itemId = feedId + "_item_" + i;
				$("#" + feedId + i).append("<p id=\"" + itemId + "\">");
				$("#" + itemId).append("<button type='button' onclick='setStatus(\"" + itemId + "\", \"" + extractedData[i]['recordId'] + "\", \"p\")'>Add</button>&nbsp;&nbsp;");
				$("#" + itemId).append("<button type='button' onclick='setStatus(\"" + itemId + "\", \"" + extractedData[i]['recordId'] + "\", \"d\")'>Discard</button>&nbsp;&nbsp;");
				$("#" + itemId).append((extractedData[i]['creator'] != '' ? extractedData[i]['creator'] : '[ creator ]') + "  (" + (extractedData[i]['pubDate'] ? extractedData[i]['pubDate'] : '[ publishedDate ]') + ").  <a href=\"" + extractedData[i]['link'] + "\" target=\"_blank\">" + extractedData[i]['title'] + "</a>");
				$("#" + itemId).append("<hr class=\"separator\" style=\"display:" + (isChecked('ExtractedData') || isChecked('Status') ? 'block' : 'none') + "\">");
			}
		}
		getNextFeedContent();
	}
}
</script>

    <h2 class="mainHeading">Dataset Candidates</h2>
    <div id="datasetDiv">
    </div>
  </div><!-- end gutter -->
</div><!-- end main -->

<div id="wrapper" class="shadow">
  <div id="sideBar">
    <div class="gutter">
      <div id="configDiv">
        <dl id="note">
          <dt id="selectFeeds">Select Feeds</dt>
        </dl>

        <dl id="note">
          <dt>Admin</dt>
          <dd>
            <div style="background-color:#D0D0FF;">
              <input type="checkbox" id="Feed" onchange="adminChange()" checked > Show Feed Delimiters
            </div>
          </dd>
          <dd>
            <div style="background-color:#D0FFD0;">
              <input type="checkbox" id="ExtractedData" onchange="adminChange()" > Show Extracted Data
            </div>
          </dd>
          <dd>
            <div style="background-color:#FFD0D0;">
              <input type="checkbox" id="Status" onchange="adminChange()" > Show Item Status
            </div>
          </dd>
        </dl>
      </div>
    </div>
  </div>
</div>
<?php

include '../meta/inc/footer-autoPop.php';

?>
