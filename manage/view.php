<?php
//assign value for title of page
$pageTitle = "MSU Dataset Search List by Status";
//declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';
//assign a class to assign layout to page - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';
//bring header code onto page
include '../meta/inc/header-admin.php';
//pass database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';
?>
<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>
<?php
//the basic SELECT statement variables, using GROUP BY clause to avoid duplicates - SELECT DISTINCT is another option, but can have significant query load overhead

// AND status=r
$select = 'SELECT recordInfo_recordIdentifier, dataset_name, status';
$from   = ' FROM datasets';
$where  = ' WHERE 1=1';
$order  = ' GROUP BY recordInfo_recordIdentifier ORDER BY recordInfo_recordIdentifier ASC';

//set table status value for query to db 
$status = isset($_GET['status']) ? $_GET['status'] : 'r';
if ($status == 'a') { $where .= " AND status ='a'"; }
if ($status == 'r') { $where .= " AND status ='r'"; }
if ($status == 'p') { $where .= " AND status ='p'"; }
if ($status == 'i') { $where .= " AND status ='i'"; }
if ($status == 'd') { $where .= " AND status ='d'"; }
if ($status == 'u') { $where .= " AND status ='u'"; }

//set table sort value for query to db 
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
if ($sort == 'id') { $order  = ' GROUP BY recordInfo_recordIdentifier ORDER BY recordInfo_recordIdentifier ASC'; }
if ($sort == 'status') { $order  = ' GROUP BY recordInfo_recordIdentifier ORDER BY status'; }
if ($sort == 'title') { $order  = ' GROUP BY recordInfo_recordIdentifier ORDER BY dataset_name'; }

?>		
<table id="results" cellspacing="0">
<?php
if ($status == 'r') { echo '<caption>Metadata Objects Needing Review</caption>'; }
if ($status == 'i') { echo '<caption>Inactive Metadata Objects</caption>'; }
if ($status == 'a') { echo '<caption>Active Metadata Objects</caption>'; }
if ($status == 'd') { echo '<caption>Discarded Metadata Objects</caption>'; }
if ($status == 'u') { echo '<caption>Unprocessed Metadata Objects</caption>'; }
?>
 <thead>
 <tr><th><a href="<?php echo basename(__FILE__).'?status='.$status.''; ?>&sort=id">ID</a></th><th><a href="<?php echo basename(__FILE__).'?status='.$status.''; ?>&sort=status">Status</a></th><th><a href="<?php echo basename(__FILE__).'?status='.$status.''; ?>&sort=title">Title</a></th><th>Options</th></tr>
 </thead>
 <tbody>
<?php

// Run query for metadata items, store in $getMetadata variable
$num_rows = 0;
$getMetadata = @mysql_query($select . $from . $where . $order);
if ($getMetadata)
{
	$num_rows = mysql_num_rows($getMetadata);
}
//display message if no items are returned to the tab view
if ($num_rows == 0)
{
	echo '</table>'."\n";
	echo '<p class="warn">There were no metadata items returned from the current request.</p>'."\n"; 
}
elseif (!$getMetadata)
{
	echo '</table>'."\n";
	die('<p class="warn">Error retrieving metadata items from database!<br />'.'Error: '. mysql_error() .'</p>');
}
else
{
	while ($row = mysql_fetch_array($getMetadata))
	{
	  	echo '<tr>'."\n";
	  	$id = $row['recordInfo_recordIdentifier'];
	  	$status = $row['status'];
	  	$title = stripslashes(html_entity_decode($row['dataset_name']));
	  	echo '<td>'.$id.'</td>'."\n";
	  	echo '<td>'.$status.'</td>'."\n";
  	  	echo '<td>'.$title.'</td>'."\n";
	  	echo '<td><a class="add" href="./editDataset.php?id='.$id.'">Edit</a> | <a class="delete" href="./deleteDataset.php?id='.$id.'">Delete</a></td>'."\n";
	  	echo '</tr>'."\n";
	}
}
?>
 </tbody>			
</table>
<p><a href="./addDataset.php">Add Another Metadata Object</a> | <a href="./">Return to Dataset Search Admin Home</a></p>
<?php
//bring footer code onto page
include '../meta/inc/footer-admin.php';
?>
