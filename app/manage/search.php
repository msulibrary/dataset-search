<?php
/*TODO need to label download pdf; need to place error logic for empty identifier field  <?php if (strlen($description) > 3) {echo '<p><strong>Contents:</strong> '.$description.'</p>'."\n"; }?>*/

// Set title of page
$pageTitle = "MSU Dataset Search Search Results";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css';

// Assign a class to assign layout to page - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Include header
include '../meta/inc/header-admin.php';

// Connect to database
include_once '../meta/assets/dbconnect-admin.inc';

?>
<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>
<?php

// Set SELECT statement variables
$select = 'SELECT recordInfo_recordIdentifier, dataset_name, status';
$from   = ' FROM datasets';
$where  = ' WHERE 1=1';
$order  = ' GROUP BY recordInfo_recordIdentifier ORDER BY dataset_name ASC';

// Set default value for search term, make string safe for mysql query
$q = isset($_GET['q']) ? strip_tags(mysql_real_escape_string($_GET['q'])) : null;
if ($q != '')
{
	// Search text was specified
	$where .= " AND dataset_name LIKE '%$q%' OR recordInfo_recordIdentifier LIKE '%$q%'";
}

// Set table sort value for query to db 
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';

$order = ' GROUP BY recordInfo_recordIdentifier';

switch ($sort)
{
	case 'id':
		$order .= ' ORDER BY recordInfo_recordIdentifier ASC';
		break;

	case 'status':
		$order .= ' ORDER BY status ASC';
		break;

	case 'title':
		$order .= ' ORDER BY dataset_name ASC';
		break;
}

?>		
<table id="results" cellspacing="0">
  <caption>Titles List</caption>
  <thead>
    <tr>
      <th><a href="./search.php?sort=id&q=<?php echo $q; ?>">ID</a></th>
      <th><a href="./search.php?sort=status&q=<?php echo $q; ?>">Status</a></th>
      <th><a href="./search.php?sort=title&q=<?php echo $q; ?>">Title</a></th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody>	
<?php

$getMetadata = @mysql_query($select . $from . $where . $order);
$num_rows = mysql_num_rows($getMetadata);

if (!$getMetadata)
{
	echo "  </tbody>\n</table>\n";
	exit('<p class="warn">Error retrieving metadata items from database!<br />'.'Error: '. mysql_error() .'</p>');
}
elseif ($num_rows == 0)
{
	// Show user no search results message
	echo "  </tbody>\n</table>\n";
	echo '<p class="warn">There were no metadata items returned from the current request. Try a different search.</p>'."\n"; 
}
else
{
	while ($row = mysql_fetch_array($getMetadata))
	{
		echo '<tr valign="top">'."\n";
		$id = $row['recordInfo_recordIdentifier'];
		$status = $row['status'];
		$title = stripslashes(html_entity_decode($row['dataset_name']));
		echo "<td>$id</td>\n";
		echo "<td>$status</td>\n";
		echo "<td>$title</td>\n";
		echo '<td><a class="add" href="./editDataset.php?id='.$id.'">Edit</a> | <a class="delete" href="./deleteDataset.php?id='.$id.'">Delete</a></td>'."\n";
		echo "</tr>\n";
	}
}
?>
  </tbody>	
</table>
<p><a href="./addDataset.php">Add Another Metadata Object</a> | <a href="./">Return to Collection Admin Home</a></p>
<?php

// Include footer
include '../meta/inc/footer-admin.php';

?>
