<?php
// Set title
$pageTitle = "MSU Dataset Search Feed List";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';

// Select page layout - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

// Include header
include '../meta/inc/header-admin.php';
?>

<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>

<?php
// Set basic SELECT statement variables
$select = 'SELECT relatedItem_originInfo_feed_identifier, relatedItem_originInfo_feed_publisher, status';
$from   = ' FROM feeds';
$where  = '';
$order  = '';

// Set table sort value for query to db 
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
if ($sort == 'id')
{
	$order  = ' ORDER BY relatedItem_originInfo_feed_identifier ASC';
}
if ($sort == 'title')
{
	$order  = ' ORDER BY relatedItem_originInfo_feed_publisher';
}
if ($sort == 'status')
{
	$order  = ' ORDER BY status';
}
?>		

<table id="results" cellspacing="0">
	<caption>All MSU Dataset Search Feeds</caption>
	<thead>
		<tr>
			<th><a href="listFeeds.php?sort=id">ID</a></th>
			<th><a href="listFeeds.php?sort=title">Feed</a>
			<th><a href="listFeeds.php?sort=status">Status</a>
			</th><th>Options</th>
		</tr>
 	</thead>
	<tbody>

<?php
		// Run query for feeds, store in $getFeeds variable
		$getFeeds = @mysql_query($select . $from . $where . $order);

		if (!$getFeeds) {
			echo "</table>\n";
			die('<p class="warn">Error retrieving metadata items from database!<br />'.'Error: '. mysql_error() .'</p>');
		}

		$numFeeds = mysql_num_rows($getFeeds);

		// Display message if no items are returned to the tab view
		if ($numFeeds == 0) {
?>

		</table>
		<p class="warn">There were no metadata items returned from the current request.</p>

<?php
		}
		else {
			while ($row = mysql_fetch_object($getFeeds)) {
?>

			<tr>
				<td><?php echo $row->relatedItem_originInfo_feed_identifier;?></td>
  				<td><?php echo $row->relatedItem_originInfo_feed_publisher;?></td>
				<td><?php echo (($row->status == 'a') ? "Active" : "Inactive") ?></td>
				<td><a class="add" href="editFeed.php?id=<?php echo $row->relatedItem_originInfo_feed_identifier;?>">Edit</a> | <a class="delete" href="deleteFeed.php?id=<?php echo $row->relatedItem_originInfo_feed_identifier;?>">Delete</a></td>
			</tr>

<?php
			}
		}
?>
	
	</tbody>			
</table>
<p><a href="./addFeed.php">Add Another Feed</a> | <a href="../">Return to MSU Dataset Search Home</a></p>

<?php
//bring footer code onto page
include '../meta/inc/footer-admin.php';
?>
