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
                $dataset_url = $row['dataset_url'];
                $doi = $row['dataset_doi'];
                $date = $row['dataset_datePublished'];
                $range = $row['dataset_temporalCoverage'];
                $description = $row['dataset_description'];
                $language = $row['recordInfo_languageOfCataloging'];
                $keywords = strtolower($row['dataset_keywords']);
                $access_condition = $row['dataset_conditionsOfAccess'];
                $access_condition_status = $row['dataset_conditionsOfAccess_status'];
//              $identifier = $row['identifier']; //Article object id
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
//include './meta/inc/header.php';
?>
<!DOCTYPE html>
<html lang="en" vocab="http://schema.org/" typeof="WebPage">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Item - MSU Dataset Search, Montana State University (MSU) Library</title>
  <meta name="description" content="MSU Dataset Search holds a set of curated datasets from creators affiliated with Montana State University."/>
  <link rel="stylesheet" href="./meta/styles/global.css" media="print" onload="this.media='all'"/>
  <link rel="canonical" href="http://arc.lib.montana.edu/msu-dataset-search/"/>
  <link rel="manifest" href="./manifest.json"/>
  <link rel="preconnect" href="https://arc.lib.montana.edu" crossorigin/>
  <link rel="dns-prefetch" href="https://arc.lib.montana.edu"/>
  <link rel="icon" sizes="192x192" type="image/png" href="./meta/img/msu-icon.png"/>
  <meta name="theme-color" content="#213c69"/>
  <link rel="apple-touch-icon" href="./img/icons/icon-144x144.png"/>
  <meta name="mobile-web-app-capable" content="yes"/>
  <meta name="mobile-web-app-status-bar-style" content="black-translucent"/>
  <meta name="mobile-web-app-title" content="DatasetSearch"/>
  <meta property="og:title" content="MSU Dataset Search - Montana State University (MSU) Library"/>
  <meta property="og:description" content="MSU Dataset Search holds a set of curated datasets from creators affiliated with Montana State University."/>
  <meta property="og:image" content="https://arc.lib.montana.edu/msu-dataset-search/meta/img/msu-dataset-search-icon-192x192.png"/>
  <meta property="og:url" content="https://arc.lib.montana.edu/msu-dataset-search/"/>
  <meta property="og:type" content="website"/>
  <meta name="twitter:creator" property="og:site_name" content="@msulibrary"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:site" content="https://www.lib.montana.edu"/>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-2733436-2', 'auto', {'allowLinker': true});
    ga('require', 'linker');
    ga('linker:autoLink', ['lib.montana.edu']);
    ga('send', 'pageview');
  </script>
</head>
<body>
  <header role="banner">
  <img src="./meta/img/MSU-horiz-reverse-web-header.svg" alt="Montana State University in Bozeman" title="Montana State University in Bozeman" height="44" width="174"/>
  <h1 class="offscreen" property="name">Item - Dataset Search, Montana State University (MSU)</h1>  
</header>
  <nav id="nav" role="navigation">
    <a href="./browse.php">Browse</a>
    <a href="./dashboard.php">Dashboard</a>
    <a href="./about.php">About</a>
  </nav>
  <div class="alert" role="alert" hidden>Your browser does not support ServiceWorker. The app will not be available offline.</div>
  <nav aria-label="breadcrumb" class="breadcrumb">
    <ol>
      <li><a href="index.php">Home</a></li>
      <li><a href="./">Dataset View</a></li>
    </ol>
  </nav>
  <main class="column--two-right" role="main" vocab="http://schema.org" typeof="ItemPage">
    <section class="content" typeof="Dataset dcat:Dataset"><!--Thing > CreativeWork > Dataset-->
        <meta property="thumbnailUrl" content="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/objects/thumb.jpg";?>"/>
<?php
        if ($date != '')
        {
?>
        <time property="temporal datePublished"><?php echo $date; ?></time>
<?php
        }
?>
        <h2 id="content" property="name dc:title"><?php echo $dataset_name; ?></h2>
<?php
          echo "<ul class=\"creatorList\">\n";
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
            die("<p>Error fetching Metadata creators info: " . mysql_error() . "</p>");
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


if ($access_condition_status == 'r') 
{
?>
<a class="button" title="<?php echo $access_condition; ?>" href="./item.php?id=<?php echo $id; ?>" onClick = "javascript:alert('<?php echo $access_condition; ?>')">Request dataset</a>
<?php
} 
else 
{
?>
<a class="button" property="identifier" href="https://doi.org/<?php echo $doi; ?>">Access dataset</a>
<?php
}
          if ($range != '')
          {
?>
          <p><span property="temporal dateCollected"><?php echo $range; ?></span>(date collected)</p>
<?php
          }
?>
          <p><span property="description dc:description"><?php echo $description; ?></span></p>

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
            <p><a class="permalink" title="permalink for <?php echo $dataset_name; ?>" href="<?php echo 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']); ?>/item/<?php echo $id; ?>">Persistent Link</a></p>
            <p class="nav"><a class="bck" href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/index.php">Back to Home page</a></p>
    </section>
    <section class="details" role="complementary">
    <h2 id="details">Details</h2>
      <dl>
<?php
if ($access_condition_status == 'r')
{
?>
        <dt class="restricted">Restricted Access</dt>
<?php
} else {
?>
        <dt>DOI:</dt>
        <dd><a property="identifier" href="https://doi.org/<?php echo $doi; ?>"><?php echo $doi; ?></a></dd>
<?php
}
?>
        <dt>Keywords:</dt>
<?php
        if ($keywords != '')
        {
?>
        <dd property="keywords"><?php echo $keywords; ?></dd>
<?php
        }
        if ($category1 != '' || $category2 != '' || $category3 != '')
        {
?>
        <dt>Categories:</dt>
        <dd><a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php?category=<?php echo urlencode($category1); ?>"><span property="about"><?php echo $category1; ?></span></a></dd>
        <dd><a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php?category=<?php echo urlencode($category2); ?>"><span property="about"><?php echo $category2; ?></span></a></dd>
        <dd><a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php?category=<?php echo urlencode($category3); ?>"><span property="about"><?php echo $category3; ?></span></a></dd>
<?php
                                                }
?>
      </dl>
    </section>
  </main>
<?php
        } // End while loop
} // End original if/else statement

//include './meta/inc/footer.php';
?>
  <footer role="contentinfo">
    <p>© Copyright Montana State University (MSU) Library</p>
  </footer>
  <script src="./meta/scripts/global.js" defer="defer"></script>
</body>
</html>
