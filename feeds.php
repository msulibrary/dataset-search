<?php
// Set Title, Description, and Keywords
$pageTitle = 'Latest Montana State University Dataset Search Submissions';

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = './meta/styles/master.css';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$bodyClass = 'rightCol default';

// Load jQuery
$jQuery = "./meta/scripts/jquery-1.9.1.min.js";

// Include page header
include './meta/inc/header-feeds.php';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

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

function feedChange(feedId)
{
	if (feedId == 'all')
	{
		for (i in feedIds)
		{
			$('#' + feedIds[i] + '_Div').show();
		}
	}
	else
	{
		for (i in feedIds)
		{
			if (feedId == feedIds[i])
			{
				$('#' + feedId + '_Div').show();
			}
			else
			{
				$('#' + feedIds[i] + '_Div').hide();
			}
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
	else
	{
		$("#selectFeeds").append("<dd><br/></dd>");
		$("#selectFeeds").append("<dd><a id=\"AllFeeds\">Show Datasets from All Feeds</a></dd>");
		$("#selectFeeds").append("<dd><br/></dd>");
		$("#selectFeeds").append("<dd><a href=\"./manage/autoPop.php\">Auto-populate Database</a></dd>");
		$("#selectFeeds").append("<dd><br/></dd>");
		$("#selectFeeds").append("<dd><a href=\"./manage/listFeeds.php\">Manage Feeds</a></dd>");

		$("#AllFeeds").click(function(){ feedChange("all"); return false; });
	}
}

function getFeedContent(feedPublisher, feedUrl, feedContentType)
{
	// Show ajax spinner
	$("#selectFeeds").append("<dd id=\"spinner\"><img src=\"./meta/img/spinner.gif\">retrieving data from " + feedPublisher + "</dd>");

	// Ajax call to get feed content
	$.ajax({
		type: "POST",
		url:  "./getSimpleFeedContent.php",
		data:
		{
			feedPublisher: feedPublisher,
			feedUrl: feedUrl,
			feedContentType: feedContentType
		},
		dataType: "json",
		success:  getSimpleFeedContentSuccess
	});
}

function getSimpleFeedContentSuccess(data)
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
			// Add entry to feed list
			$("#selectFeeds").append("<dd><a id=\"" + feedId + "\"> " + feedPublisher + "</dd>");

			// Add feed div
			$("#datasetDiv").append("<div class=\"" + feedId + "\" id=\"" + feedId + "_Div" + "\">");

			// Add feed delimeter to feed div
			$("#" + feedId + "_Div").append("<h2 class=\"Feed\" style=\"background-color:#D0D0FF\">" + feedPublisher);

			// extractedData.length will be 0 if feedStatus != "success"
			for (var i = 0; i < extractedData.length; i++)
			{
				// Add item div to feed div
				$("#" + feedId + "_Div").append("<div id=\"" + feedId + i + "\">");

				itemId = feedId + "_item_" + i;
				$("#" + feedId + i).append("<p id=\"" + itemId + "\">");
				$("#" + itemId).append(
					(extractedData[i]['creator'] ? extractedData[i]['creator'] : '[ creator ]') + 
					" (" + (extractedData[i]['pubDate'] ? extractedData[i]['pubDate'] : ' [ publishedDate ] ') + ")" +
					" <a href=\"" + extractedData[i]['link'] + "\" target=\"_blank\">" + extractedData[i]['title'] + "</a>");
			}

			$('#' + feedId).click(function(){ feedChange(feedId); return false; });
//			$('#' + feedId).click(function(e) {e.preventDefault(); alert(feedId); return false; });
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
          <dt id="selectFeeds">Select a Feed</dt>
        </dl>
      </div>
    </div>
  </div>
</div>
<?php

include './meta/inc/footer-autoPop.php';

?>
