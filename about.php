<?php

// Set title, description, and keywords
$pageTitle = 'About the MSU Dataset Search Database';
$pageDescription = 'MSU Dataset Search online full-text retrieval database.';
$pageKeywords = 'MSU, data';

// Select page layout - choices include: no columns = fullWidth, and right column = rightCol
$pageLayout = 'rightCol';

// Include page header
include './meta/inc/header.php';
?>

<div id="main" itemscope itemtype="http://schema.org/Thing/CreativeWork/WebPage/AboutPage"><a name="mainContent"></a>
<div class="gutter">
	<h2 class="mainHeading"><span itemprop="name">About the MSU Dataset Search Online Database</span></h2> 
	<h3>What is it?</h3>
<!--    	<p class="descript"><a href="http://www.lib.montana.edu/digital/"><img class="object" src="./meta/img/libraries.png" alt="link to msu libraries digital website"/></a></p> -->
        <p class="descript"> The <strong><span itemprop="publisher">MSU Dataset Search</span></strong> online database has datasets from creators affiliated with Montana State University.</p>
        <p class="descript">The MSU Dataset Search database is a product of the <a href="http://www.lib.montana.edu/archives/"><strong><span itemprop="provider">Montana State University Library Special Collections and Archival Informatics</span></strong></a>.</p>
        <p class="descript">Anyone interested in any other use of these materials, including for-profit Internet editions, should obtain permission from Special Collections and Archival Informatics at Montana State University Library.</p>
        <p class="nav return"><a class="bck" href="./index.php">Back to home page</a></p>
</div><!-- end gutter div -->
</div><!-- end main div -->
<div id="sideBar">
<div class="gutter">
</div><!-- end gutter div -->
</div><!-- end sideBar div -->
<?php
include './meta/inc/footer.php';
?>
