<?php
// Purpose: This code searches database for the user specified search terms and displays single matching item and full details

// Set Title, Description, and Keywords
$pageTitle = 'MSU Dataset Search Record';
$pageDescription = 'Dataset metadata';
$pageKeywords = '';

// Include global functions and special actions
include_once './meta/inc/global.php';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Check if the $id variable was passed in url, escape the string for mysql, and validate that it is a numeric value - pass id value to select record
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
	$id = strip_tags(mysql_real_escape_string((int)$_GET['id']));
} else {
	echo 'Query type not supported.';
	exit;
}

// Request selected record or item from table
$result = mysql_query("
			SELECT *
			FROM datasets
			WHERE recordInfo_recordIdentifier='$id' AND status = 'a'
		  ");
$num_rows = mysql_num_rows($result);
if($num_rows == 0) {
	noMatches();
}
else {
	// Format individual fields/rows for display from $result query, set up while loop
	while ($row = mysql_fetch_array($result)) {
		$id = $row['recordInfo_recordIdentifier'];
		$dataset_name = trim($row['dataset_name']);
		$doi = $row['dataset_doi'];
		$date = $row['dataset_temporalCoverage'];
		$description = $row['dataset_description'];
		$language = $row['recordInfo_languageOfCataloging'];
		$keywords = strtolower($row['dataset_keywords']);
//		$identifier = $row['identifier']; //Article object id
		$identifier = "";
		$proxy = "";
		$category1 = $row['dataset_category1'];
		$category2 = $row['dataset_category2'];
		$category3 = $row['dataset_category3'];
		// Get object number
		$path = explode('/', $identifier);
		$filename = $path[count($path)-1];
		$object = strtok($filename, ".");

		// Create array to hold google scholar meta tags
		$gsMetaTags = array();

		if ($dataset_name != "") {
			array_push($gsMetaTags, "<meta name=\"dataset_name\" content=\"$dataset_name\">\n");
		}

		// Include all creators from creators table
		$query = "
			SELECT creator_name
			FROM creators
			WHERE recordInfo_recordIdentifier='$id'
			ORDER BY creator_key;
		";
		$getMetadataAuthorInfo = @mysql_query($query);

		if (!$getMetadataAuthorInfo) {
			die("<h2>Error fetching Metadata creator info: " . mysql_error() . "</h2>");
		}

		$prev_creator = "";
		while ($row = mysql_fetch_array($getMetadataAuthorInfo)) {
			$creator = $row['creator_name'];

			if ($creator != $prev_creator) {
				array_push($gsMetaTags, "<meta name=\"dataset_creator\" content=\"$creator\">\n");
				$prev_creator = $creator;
			}
		}

		if ($date != "") {
			array_push($gsMetaTags, "<meta name=\"dataset_publish_date\" content=\"$date\">\n");
		}

		if ($id != "") {
			array_push($gsMetaTags, "<meta name=\"dataset_url\" content=\"" . "http" .
						((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "s" : "") . "://" .
						$_SERVER['SERVER_NAME'] .  dirname($_SERVER['PHP_SELF']) .
						"/objects/$identifier\">\n");
		}

		// Display selected item/record to the user

// Include page header
include './meta/inc/header.php';
?>

<div id="main" vocab="http://schema.org" typeof="ItemPage">
	<a name="mainContent"></a> <!--thing/creativework/webpage/itempage-->
	<meta property="thumbnailUrl" content="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/objects/thumb.jpg";?>"/>
	<div class="gutter">
		<h2 class="mainHeading"><span property="name"><?php echo $dataset_name; ?></span></h2>
		<ul class="item" typeof="Dataset dcat:Dataset"> <!--Thing > CreativeWork > Dataset-->
			<li>
				<ul class="metadata">
					<li class="describe">
						<p><strong>Dataset Name:</strong> <span property="name dc:title"><?php echo $dataset_name; ?></span></p>
						<p><strong>Creators:</strong>

<?php
						echo "</p><ul class=\"creatorList\">\n";
						// Include all creators from creators table
						$query = "
							SELECT creator_name, name_affiliation_msuCollege, name_affiliation_msuDepartment, name_affiliation_otherAffiliation
							FROM creators left join affiliations
							ON creators.creator_key = affiliations.creator_key
							WHERE recordInfo_recordIdentifier='$id'
							ORDER BY creators.creator_key, affiliation_key;
						";
						$getMetadataAuthorInfo = @mysql_query($query);

						if (!$getMetadataAuthorInfo) {
							die("<h2>Error fetching Metadata creators info: " . mysql_error() . "</h2>");
						}

						$prev_creator = "";
						while ($row = mysql_fetch_array($getMetadataAuthorInfo)) {
							$creator = $row['creator_name'];
							$affiliation = $row['name_affiliation_otherAffiliation'];

							if ($creator != $prev_creator) {
								if ($prev_creator != "") {
									echo "</li>\n";
								}
								echo "<li><span property=\"creator\">" . stripslashes($creator) . "</span>";
								$prev_creator = $creator;
							}
							if ($affiliation != "") {
								echo "<span property=\"affiliation\">&nbsp;&nbsp;[ $affiliation ]</span>";
							}
						}
						echo "</li>\n</ul>\n";
?>

<?php
						if ($date != '')
						{
?>
		                <p><strong>Date:</strong> <span property="temporal datePublished"><?php echo $date; ?></span></p>
<?php
						}
?>
       		         	<p><strong>Description:</strong> <span property="description dc:description"><?php echo $description; ?></span></p>
       		         	<p><strong>DOI:</strong> <span property="identifier"><a href="<?php echo $doi; ?>"><?php echo $doi; ?></a></span></p>
<?php
						if ($keywords != '')
						{
?>
                		<p><strong>Keywords:</strong> <span property="keywords"><?php echo $keywords; ?></span></p>
<?php
						}
						if ($category1 != '' || $category2 != '' || $category3 != '')
						{
?>
                		<p><strong>Categories:</strong>
                		<a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php?category=<?php echo urlencode($category1); ?>"><span property="about"><?php echo $category1; ?></span></a>
                		<a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php?category=<?php echo urlencode($category2); ?>"><span property="about"><?php echo $category2; ?></span></a>
                		<a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php?category=<?php echo urlencode($category3); ?>"><span property="about"><?php echo $category3; ?></span></a></p>
<?php
						}
?>
					</li>
            		<li class="action">

<?php
						//logic to check for digital article identifier
						if (strlen($identifier) > 3):
?>

							<p><a class="quality" title="Get Digital Article" - <?php echo $dataset_name; ?>" href="<?php echo dirname($_SERVER['PHP_SELF']).'/objects/'. $identifier; ?>" alt="<?php echo $dataset_name; ?>"><strong>Get Full Article</strong></a></p>

<?php
						else:
?>

<!--
							<p>Digital Article Not Available</p>
-->

<?php
						endif;

						// Logic to check for digital abstract relatedItem_relatedItem_identifier
						if (strlen($proxy) > 3):
?>

							<p><a class="quality" title="Get Digital Abstract" - <?php echo $dataset_name; ?>" href="<?php echo dirname($_SERVER['PHP_SELF']).'/objects/'. $proxy; ?>" alt="<?php echo $dataset_name; ?>"><strong>Get Full Abstract</strong></a></p>

<?php
						else:
?>

<!--
							<p>Digital Abstract Not Available</p>
-->

<?php
						endif;
?>

						<p>
							<a class="permalink" title="permalink for <?php echo $dataset_name; ?>" href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']); ?>/item/<?php echo $id; ?>">Persistent Link</a>
						</p>
						<p>
							<!-- AddThis Button BEGIN -->
							<script type="text/javascript">var addthis_pub = "jaclark"; var addthis_options = "favorites,delicious,twitter,facebook,myspace,google,yahoobkm,friendfeed,more";var addthis_offset_top = -15;addthis_caption_share="Bookmark and share";</script>
							<a class="share" href="http://www.addthis.com/bookmark.php" onclick="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()">Bookmark and Share</a>
							<script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
							<!-- AddThis Button END -->
						</p>
						<p>
							<form action="" name="embedForm" id="embedForm">
								<label class="embed" for="embed">Get Embed Code</label>
								<input id="embed" name="embed" type="text" onClick="this.select();" onFocus="this.select();" readonly="readonly" value="&lt;a title=&quot;<?php echo $dataset_name; ?>, Montana State University Library&quot; href=&quot;<?php echo 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']); ?>/objects/<?php echo $identifier; ?>&quot;&lt;/a&gt;
" />
							</form>
						</p>
					</li>
				</ul><!-- end metadata <ul> -->
			</li>
		</ul><!-- end item <ul> -->

		<p class="nav"><a class="bck" href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/index.php">Back to Home page</a></p>
	</div><!-- end gutter div -->
</div><!-- end main div -->

<?php
	} // End while loop
} // End original if/else statement

include './meta/inc/footer.php';
?>
