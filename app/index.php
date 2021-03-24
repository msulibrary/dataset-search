<?php

// Set Title, Description, and Keywords
$pageTitle = 'Montana State University Dataset Search';
$pageDescription = 'Montana State University Dataset Search database.';
$pageKeywords = 'MSU, data';

// Create an array with filepaths for multiple page scripts - default is meta/scripts/thickbox.js
//$customScript[0] = './meta/scripts/jquery-compressed.js';
//$customScript[1] = './meta/scripts/thickbox.js';

// Declare filename and filepath for screen/projection stylesheet variable - default is common/styles/master.css
//$customCSS[0] = './meta/styles/thickbox.css';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
//$pageLayout = 'fullWidth';

// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';

// Include page header
//include './meta/inc/header.php';

// Include functions to get creators from database
include './getCreators.php';
?>

<!DOCTYPE html>
<html lang="en" vocab="http://schema.org/" typeof="WebPage">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>MSU Dataset Search - Montana State University (MSU) Library</title>
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
  <meta name="msapplication-starturl" content="/msu-dataset-search/?utm_source=homescreen"/>
  <meta property="og:title" content="MSU Dataset Search - Montana State University (MSU) Library"/>
  <meta property="og:description" content="MSU Dataset Search holds a set of curated datasets from creators affiliated with Montana State University."/>
  <meta property="og:image" content="https://arc.lib.montana.edu/msu-dataset-search/meta/img/msu-dataset-search-icon-192x192.png"/>
  <meta property="og:url" content="https://arc.lib.montana.edu/msu-dataset-search/"/>
  <meta property="og:type" content="website"/>
  <meta name="twitter:creator" property="og:site_name" content="@msulibrary"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:site" content="https://www.lib.montana.edu"/>
  <!--<script type="text/javascript">
    window.onload = function(){
      location.href=document.getElementById("dataset-select").value;
    }       
  </script>-->
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
  <h1 class="offscreen" property="name">Dataset Search, Montana State University (MSU)</h1>
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
      <li><a href="index.php">Dataset Search</a></li>
    </ol>
  </nav>
  <main class="column--one-center" role="main">
    <section class="content center">
    <h2 id="content">MSU Dataset Search</h2>
    <p>Access datasets from creators affliated with Montana State University (MSU)</p>
    <form action="search.php" class="search-form" id="search-form" method="GET" role="search">
      <label class="offscreen" for="q">Search</label>
      <svg aria-hidden="true" class="search-icon" width="18" height="18" viewBox="0 0 18 18"><path d="M18 16.5l-5.14-5.18h-.35a7 7 0 10-1.19 1.19v.35L16.5 18l1.5-1.5zM12 7A5 5 0 112 7a5 5 0 0110 0z"></path></svg>
      <input class="text icon-search" type="search" id="q" name="q" maxlength="75" autofocus placeholder="Enter keyword, name, or title..."/>
      <input class="button" type="submit" value="Search" />                
    </form>
    <p><a href="search-advanced.php">Advanced Search</a></p>
    </section>
    <section class="feature" role="complementary">
<?php
// Set open value for section of search form to display
$params = (count($_POST)) ? $_POST : $_GET;
$view = (empty($params['view'])) ? null : $params['view'];

// Set switch control structure to shift view of featured datasets section based on value in url
switch($view) {
  default:
  {       
  // Select random record/item(s) from table
  $result = mysql_query("SELECT * FROM datasets WHERE status = 'a' ORDER BY RAND() LIMIT 4");              
  }
  break;
                                        
  case 'date':
  {       
  // Select random record/item(s) from table
  $result = mysql_query("SELECT * FROM datasets WHERE status = 'a' ORDER BY dataset_datePublished LIMIT 4");
  }
  break;
}
?>
    <form id="dataset-sort-form">
    <label class="offscreen" for="dataset-select">select a dataset view</label>
    <select name="dataset-select" class="dataset-select" id="dataset-select" onchange="javascript:location.href = this.value;">
      <option value="index.php?view=popular">Most popular</option>
      <option value="index.php?view=date">By date</option>
    </select>
    </form>
    <ul class="item">
<?php
  // Format individual fields/rows for display from $result query, set up while loop
  while ($row = mysql_fetch_array($result)) {
    $id = $row['recordInfo_recordIdentifier'];
    $date = $row['dataset_datePublished'];
    $name = $row['dataset_name'];
    $doi = $row['dataset_doi'];
    $url = $row['dataset_url'];

    // Display selected record/item(s) to the user
    echo "<li class=\"citation\">\n";
    echo "<time datetime=\"$date\">$date</time>\n";
    echo "<cite><a href=\"./item.php?id=$id\" title=\"$name\">$name</a></cite>\n";
    $creators = getCreators($id);
    if ($creators != '') {
      echo "<span class=\"creator\">" . getCreators($id) . "</span>\n";
      //echo "<p>Creator" . ((substr_count($creators, ",") == 1)? ": " : "s: ") . getCreators($id) . "</p>\n";
    }
    echo "</li>\n";
  }
?>
      </ul>
    </section>
  </main>
  <footer role="contentinfo">
    <p>© Copyright Montana State University (MSU) Library</p>
  </footer>
  <script src="./meta/scripts/global.js" defer="defer"></script>
</body>
</html>
