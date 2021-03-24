<?php
// Purpose: This code searches the database for the user specified search terms and displays matching items

// Set Title, Description, and Keywords
$pageTitle = 'Browse - MSU Dataset Search';
$pageDescription = 'Browse results from the MSU Dataset Search database.';
$pageKeywords = 'MSU, data';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Include page header
//include './meta/inc/header.php';
?>

<!DOCTYPE html>
<html lang="en" vocab="http://schema.org/" typeof="WebPage">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Browse - MSU Dataset Search, Montana State University (MSU) Library</title>
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
  <img src="./meta/img/MSU-horiz-reverse-web-header.svg" alt="Montana State University in Bozeman" title="Montana State University in Bozeman" height="44" width="174">
  <h1 class="offscreen" property="name">Browse - Dataset Search, Montana State University (MSU)</h1>
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
      <li><a href="browse.php">Browse</a></li>
    </ol>
  </nav>
  <main role="main" class="column--two-left">
    <section class="details" role="complementary">
    <h2 id="content">Browse</h2>
    <p><a href="browse.php">Alphabetical</a></p>
    <p><a href="browse.php?view=date">Date</a></p>    
    <p><a href="browse.php?view=keywords">Keywords</a></p>    
    </section>
    <section class="content">
<?php
$view = isset($_GET['view']) ? htmlentities(strip_tags($_GET['view'])) : null;

switch(true) {
  case ($view == 'date'):
    echo '<h2>Date</h2>';
    echo '<ul id="listColumns">';
                        // Get dates from database
                        $query = "
                                SELECT dataset_temporalCoverage
                                FROM datasets
                                WHERE status = 'a'
                                GROUP BY dataset_temporalCoverage
                                ORDER BY dataset_temporalCoverage ASC
                        ";
                        $getDates = mysql_query($query);

                        if ($getDates)
                        {
                                // Display dates from $getDates query
                                while ($row = mysql_fetch_object($getDates))
                                {
                                        $date = $row->dataset_temporalCoverage;

                                        // Get count of this date
                                        $query = "
                                                SELECT COUNT(*) AS matchCount
                                                FROM datasets
                                                WHERE status = 'a'
                                                AND MATCH (dataset_temporalCoverage) AGAINST ('\"$date\"' IN BOOLEAN MODE)
                                        ";
                                        $countResult = mysql_query($query);
                                        $count = mysql_fetch_assoc($countResult);
                                        $dateCount = $count['matchCount'];

                                        // Display date to the user
                                        if ($date != "") {
                                                echo "<li><a href='./search.php?date=\"" . urlencode($date) . "\"'>$date ($dateCount Article" .
                                                                (($dateCount == 1) ? "" : "s") . ")</a></li>\n";
                                        }
                                }
                        }
    echo '</ul><!-- end listColumns -->';
    break;
  case ($view == 'keywords'):
    echo '<h2>Keywords</h2>';
    echo '<ul id="block">';
                // Get keywords from database
                $query = "
                        SELECT dataset_keywords
                        FROM datasets
                        WHERE status = 'a'
                        GROUP BY dataset_keywords
                        ORDER BY dataset_keywords ASC
                ";
                $getKeywords = mysql_query($query);

                // Extract $keywords into an array
                $keywords = array();
                while ($row = mysql_fetch_assoc($getKeywords)) {
                        $rawKeywords = explode(',', $row['dataset_keywords']);
                        $keywords = array_merge($keywords, $rawKeywords);
                }

                $keywords = array_map('trim', $keywords); // Remove whitespace
                $keywords = array_filter($keywords); // Remove empties
                $keywords = array_unique($keywords); // Remove duplicates
                natcasesort($keywords);

                // Display keywords to user
                foreach ($keywords as $value) {
                        $keyword = strtolower(trim($value));
                        echo "<li><a href='./search.php?keyword=\"" . urlencode($keyword) . "\"'>$keyword</a></li>\n";
                }
    echo '</ul><!-- end block -->';
    break;
  default:
    echo '<h2>Alphabetical</h2>';
    echo '<p>';
                /**
                 * Groupings of subHeadings
                 *
                 * Note: To change the groupings, simply edit this array.  The last element
                 *       is "special".  It is for all titles that start with an ascii
                 *       character < 'a', most likely a number.
                 */
                $groupings = array(
                        "abc",
                        "def",
                        "ghi",
                        "jkl",
                        "mno",
                        "pqr",
                        "stu",
                        "vwx",
                        "yz",
                        "other"
                );

                $lastIndex = count($groupings) - 1;

                $subHeading = array();
                $href = array();
                $firstChar = array();
                $lastChar = array();

                // Populate the subHeading, href, firstChar, and lastChar arrays
                foreach ($groupings as $grouping) {
                        $href[$grouping] = $grouping;

                        if ($grouping == $groupings[$lastIndex]) {
                                // Last (special) element in array
                                $subHeading[$grouping] = ucwords(strtolower($grouping));
                                $firstChar[$grouping] = null;
                                $lastChar[$grouping] = chr(ord('a') - 1);
                        }
                        else {
                                $subHeading[$grouping] = "";
                                for ($i = 0; $i < strlen($grouping); $i++) {
                                        // Put a space in between each character
                                        $subHeading[$grouping] .= strtoupper($grouping[$i]) . " ";
                                }

                                $firstChar[$grouping] = $grouping[0];

                                if ($grouping == $groupings[$lastIndex - 1]) {
                                        // Last alphabetic heading
                                        $lastChar[$grouping] = null;
                                }
                                else {
                                        $lastChar[$grouping] = substr($grouping, -1);
                                }
                        }
                }

                $continue = "";
                foreach ($groupings as $grouping) {
                        echo "$continue<a href=\"#$href[$grouping]\">$subHeading[$grouping]</a>";
                        $continue = " | ";
                }
echo '</p>';
                foreach ($groupings as $grouping) {
                        echo "<p><a id=\"$href[$grouping]\"></a>$subHeading[$grouping]&nbsp;<a href=\"#mainContent\">[^]</a></p>";
                        echo "<ul class=\"list\">";

                        // Request resources with requested letters in title
                        $having = "HAVING ";
                        if ($lastChar[$grouping] == null) {
                                $having .= "datasets_title_sort >= '" . $firstChar[$grouping] . "'";
                        }
                        elseif ($firstChar[$grouping] == null) {
                                $having .= "datasets_title_sort <= '" . chr(ord($lastChar[$grouping]) + 1) . "'";
                        }
                        else {
                                $having .= "datasets_title_sort >= '" . $firstChar[$grouping] . "' AND datasets_title_sort <= '" . chr(ord($lastChar[$grouping]) + 1) . "'";
                        }

                        $getItems = @mysql_query(
                                "SELECT dataset_name, recordInfo_recordIdentifier,
                                COUNT(recordInfo_recordIdentifier) AS title_count,
                                        CASE WHEN SUBSTRING_INDEX(dataset_name, ' ', 1)
                                                        IN ('a', 'an', 'the')
                                                THEN CONCAT(
                                                        SUBSTRING(dataset_name, INSTR(dataset_name, ' ') + 1),
                                                        ', ',
                                                        SUBSTRING_INDEX(dataset_name, ' ', 1)
                                                )
                                                ELSE dataset_name
                                        END AS datasets_title_sort
                                FROM datasets
                                WHERE status = 'a'
                                GROUP BY datasets_title_sort" .
                                " $having " .
                                "ORDER BY datasets_title_sort ASC"
                        );

                        if (!$getItems) {
                                die("<p>Error retrieving resources from database!<br/>".
                                        "Error: " . mysql_error() . "</p></div></div>");
                        }

                        // Display selected resource entry fields in a list
                        while ($row = mysql_fetch_array($getItems)) {
                                $item_id = $row['recordInfo_recordIdentifier'];
                                $item_title = stripslashes(html_entity_decode($row['dataset_name']));
                                $titleCount = $row['title_count'];
                                //echo "<li><a href='./search.php?title=\"" . urlencode($item_title) . "\"'>$item_title</a></li>\n";
                                echo "<li><a href=\"./item.php?id=$item_id\">$item_title</a></li>\n";
                        }
                        echo "</ul>";
                }
    break;
}
?>

<?php
/*
$view = isset($_GET['view']) ? htmlentities(strip_tags($_GET['view'])) : null;

switch(true) {
    case ($view == 'date'):
        echo "foo";
        break;
    case ($view == 'keywords'):
        echo "bar";
        break;
    default:
        echo "default";
        break;
}
*/
?>
<p class="nav return">
<a class="bck" href="./index.php">Back to Homepage</a>
</p>
    </section>
  </main>
  <footer role="contentinfo">
    <p>© Copyright Montana State University (MSU) Library</p>
  </footer>
  <script src="./meta/scripts/global.js" defer="defer"></script>
</body>
</html>
