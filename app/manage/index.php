<?php

// Set title
$pageTitle = "MSU Dataset Search";

// Declare filename and filepath for screen/projection stylesheet variable - default is meta/styles/master.css
$customCSS = '../meta/styles/admin.css?v=1';

// Select page layout - default is fullWidth (1 column, header and footer)
$bodyClass = 'fullWidth';

// Include header
include '../meta/inc/header-admin.php';

// Get database parameters and connect to database
include_once '../meta/assets/dbconnect-admin.inc';

?>
<script type="text/javascript">
function doExport()
{
  var format = document.getElementById("format").value;

  if (format == 'txt')
  {
    document.exportForm.action = "./dataset-batch.txt";
  }
  else if (format == 'csv')
  {
    document.exportForm.action = "./dataset-batch.csv";
  }
  else if (format == 'xml')
  {
    document.exportForm.action = "../api.php";
  }
  else if (format == 'json')
  {
    document.exportForm.action = "../api.php";
  }

  return true;
}
</script>

<h2 class="mainHeading"><?php echo($pageTitle); ?></h2>
<div id="manage">
  <dl id="feed">
    <dt>RSS Feeds</dt>
    <dd><a class="expand" href="./listFeeds.php">Manage Feeds</a></dd>
    <dd><a class="expand" href="./autoPop.php">Auto-populate Database</a></dd>
  </dl>
  <dl id="export">
    <dt>Export Metadata Records</dt>
    <form id="exportForm" name="exportForm" onsubmit="return doExport();" method="get" target="_blank">
      <input type="hidden" name="v" value="1"/>
      <dd>
        <label class="expand" for="date">Publication Month (Select one)</label>
        <select id="date" name="date" size="1">
<?php

	$thisYear = date("Y");
	$thisMonth = date("m");

	echo "          <option value=\"$thisYear-$thisMonth\" selected>$thisYear-$thisMonth</option>\n";

	$month = $thisMonth - 1;
	$year = $thisYear;
	while ($year >= "2014") {
		if ($month == "0") {
			$month = "12";
			$year--;
		}
		$month = sprintf("%02d", $month);
		echo "          <option value=\"$year-$month\">$year-$month</option>\n";
		if ($year == '2014' && $month == '10') {
			break;
		}
		$month--;
	}

?>
        </select>
      </dd>
      <dd>
        <label class="expand" for="format">Export Type (Select one)</label>
        <select id="format" name="format" size="1">
          <option value="txt" selected>text (.txt)</option>
          <option value="csv">csv (.csv)</option>
          <option value="xml">xml (.xml)</option>
          <option value="json">json (.json)</option>
        </select>
      </dd>
      <p class="button"><input class="submit" type="submit" value="Export Metadata Records"/></p>
    </form>
<!--
	<dd><a class="expand" href="./dataset-batch.txt?date=<?php echo date("Y").'-10';?>">Get October 2014 batch as text (.txt) file</a></dd>
	<dd><a class="expand" href="./dataset-batch.txt?date=<?php echo date("Y-m");?>">Get latest batch as text (.txt) file</a></dd>
	<dd><a class="expand" href="./dataset-batch.csv?date=<?php echo date("Y-m");?>">Get latest batch as csv (.csv) file</a></dd>
	<dd><a class="expand" href="../api.php?v=1&amp;date=<?php echo date("Y-m");?>&amp;format=json">Get latest batch as .json file</a></dd>
-->
  </dl>
  <dl id="create">
    <dt>Create Metadata Records</dt>
    <dd><a class="expand" href="./addDataset.php">Add New Metadata Object</a></dd>
  </dl>
  <dl id="edit">
    <dt>Edit Metadata Records</dt>
    <dd><a class="expand" href="./view.php?status=all">View All Metadata Objects</a></dd>
    <dl class="indent">
      <dd><a class="expand" href="./view.php?status=a">View Active Objects</a></dd>
      <dd><a class="expand" href="./view.php?status=r">View Objects Needing Review</a></dd>
      <dd><a class="expand" href="./view.php?status=p">View Pending Objects</a></dd>
      <dd><a class="expand" href="./view.php?status=i">View Inactive Objects</a></dd>
      <dd><a class="expand" href="./view.php?status=d">View Discarded Objects</a></dd>
      <dd><a class="expand" href="./view.php?status=u">View Unprocessed Objects</a></dd>
    </dl>
    <form id="adminForm" action="./search.php" method="get">
      <fieldset>
        <h3><label class="search" for="submit">Search for Metadata Object by Title or Object ID</label></h3>
        <input class="text" type="text" name="q" id="q" size="36" maxlength="200" value="" />
        <input class="submit" type="submit" name="submit" value="Search" />
      </fieldset>
    </form>
  </dl>
</div> <!-- end manage div -->
<?php

// Include footer
include '../meta/inc/footer-admin.php';

?>
