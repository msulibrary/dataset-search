<?php
// Get database parameters and connect to database
include_once './meta/assets/dbconnect.inc';
?>
<!DOCTYPE html>
<html lang="en" vocab="http://schema.org/" typeof="WebPage">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Dashboard - MSU Dataset Search, Montana State University (MSU) Library</title>
  <meta name="description" content="Dashboard for MSU Dataset Search showing statistics for curated datasets from creators affiliated with Montana State University."/>
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
<style>
.grid {
display: grid;
grid-gap: 10px;
grid-template-columns: repeat(4, [col] 25% ) ;
grid-template-rows: repeat(3, [row] auto  );
width:100%;
}
/*
.wrapper {
  display: grid;
  grid-gap: 10px;
  grid-template-columns: minmax(200px, 1fr) 200px 200px;
  width:100%;
}
*/
.box {
  border: 1px solid gray;
  border-radius: 5px;
  padding: 20px;
}
.title {
grid-column: col 1 / span 2;
grid-row: row;
}
.count {
grid-column: col 3 / span 1 ;
grid-row: row;
}
.contributor {
grid-column: col 4;
grid-row: row;
}
.bar-chart1 {
grid-column: col 1 / span 2 ;
grid-row: row 2 ;
}
.pie-chart {
grid-column: col 3 / span 2;
grid-row: row 2;
}
.bar-chart2 {
grid-column: col 1 / span 2;
grid-row: row 3;
}
.scatter-chart {
grid-column: col 3 / span 2;
grid-row: row 3;
}
#dataset-sort-form {text-align:left;}
.column--one-center {width:75%;}
/*medium screen view < 801px (based on 16px/1rem browser default)*/
@media all and (max-width:50.1em) {
.column--one-center {width: 90%;}
.grid {
grid-template-columns: repeat(1, [col] 100% ) ;
grid-template-rows: repeat(7, [row] auto );
}

/*small screen view < 481px (based on 16px/1rem browser default)*/
@media all and (max-width:30.063em) {
}
</style>
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
  <h1 class="offscreen" property="name">Dashboard - Dataset Search, Montana State University (MSU)</h1>
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
      <li><a href="dashboard.php">Dashboard</a></li>
    </ol>
  </nav>
  <main role="main" class="column--one-center">
    <!--<form id="dataset-sort-form">
    <label class="offscreen" for="dataset-select">select a dataset view</label>
    <select name="dataset-select" class="dataset-select" id="dataset-select" onchange="javascript:location.href = this.value;">
      <option value="dashboard.php?filter=college&query=">By college</option>
      <option value="dashboard?filter=department&query=">By department</option>
    </select>
    </form>-->
    <section class="content grid">
    <script src="https://d3js.org/d3.v5.min.js"></script>
    <div class="box title">
<?php if ($filter == 'filter') { ?>
      <h2 id="content">MSU Dataset Dashboard</h2>
<?php } else { ?>
      <h2 id="content">MSU <?php echo ucwords($filter); ?> Dataset Dashboard</h2>
<?php
}
?>
      <p>Last updated: <?php $updated = date('Y-m-d h:i:s a', time()); echo $updated; ?></p>
    </div>
    <div class="box count"><h2>74</h2><p>total datasets</p></div>
    <div class="box contributor"><h2>167</h2><p>contributors</p></div>
<?php
if($filter == 'college') {

//need to declare and pass two values filter=college&query= 

  $example_college = "Letters & Science";

  //query for the first horizontal bar chart https://www.w3resource.com/mysql/aggregate-functions-and-grouping/aggregate-functions-and-grouping-count-with-group-by.php
  $query_string_horz_bar_1 = 'SELECT a.name_affiliation_msuDepartment AS name, a.name_affiliation_msuDepartment_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key WHERE a.name_affiliation_msuCollege = $example_college GROUP BY a.name_affiliation_msuDepartment';
  //query for the second horizontal bar chart
  $query_string_horz_bar_2 = 'SELECT c.creator_name AS name, c.creator_name AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key WHERE a.name_affiliation_msuCollege = $example_college GROUP BY a.name_affiliation_msuCollege';
    //dataset_datePublished Y/M/D
  $query_string_scatter_plot = 'SELECT d.dataset_datePublished AS data_date, a.name_affiliation_msuDepartment_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key WHERE a.name_affiliation_msuCollege = $example_college GROUP BY d.dataset_datePublished';

  //each specific repository
  $repositories = @mysql_query($query_string9);

  $repository_rows = [];
  while($row = mysql_fetch_assoc($repositories)){
    $repository_rows[] = $row;
  }
  //the number of datasets associated with each specific repository
  $repository_counts = [];
  foreach($repository_rows as $repository){
    $query_string = 'SELECT COUNT(dataset_name) FROM datasets WHERE dataset_repositoryName = "'.$repository['dataset_repositoryName'].'";';
    $result = @mysql_query($query_string);
    $count = mysql_fetch_assoc($result);
    array_push($repository_counts, $repository['dataset_repositoryName'], $count['COUNT(dataset_name)']);
  }
  // print_r($repository_counts);

  //query the database
  $datasets_csv = @mysql_query($query_string_horz_bar_1);
  $datasets = @mysql_query($query_string_horz_bar_1);

  convertToCSV($datasets_csv);//Shorter department names?

  $json_datasets_horz_bar_1 = convertToJSON($datasets);

  ////////

  $datasets = @mysql_query($query_string_horz_bar_2);
  $datasets_csv = @mysql_query($query_string_horz_bar_2);

  convertToCSV($datasets_csv);//Why does Colleges have two different Letters and Sciences?

  $json_datasets_horz_bar_2 = convertToJSON($datasets);

  ////////

  $datasets = @mysql_query($query_string_scatter_plot);
  $datasets_csv = @mysql_query($query_string_scatter_plot);

  convertToCSV($datasets_csv);

  $json_datasets_scatter_plot = convertToJSON($datasets);


  if(!$datasets){

    echo '<p class="warn">Error in retrieving datasets from database!<br />'.'Error: '. mysql_error() .'</p>';

  }

  $num_datasets = mysql_num_rows($datasets);

  if($num_datasets == 0){

    echo '<p class="warn">No datasets were found for this dashboard!</p>';

  }else{

    //todo: funnel data into D3JS here for infographic

    //convert data into csv

    //define script tag here to call all infographics
  }

}
//
////
////Work needed
////
////
//
if($filter == 'department'){

  $example_college = "Letters & Science";
  //need to change to $query in all SQL statement

  //query for the first horizontal bar chart https://www.w3resource.com/mysql/aggregate-functions-and-grouping/aggregate-functions-and-grouping-count-with-group-by.php
  $query_string_horz_bar_1 = 'SELECT c.creator_name AS name, c.creator_name AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key WHERE a.name_affiliation_msuDepartment = $example_college GROUP BY c.creator_name';
  //query for the second horizontal bar chart
  $query_string_horz_bar_2 = 'SELECT d.dataset_funder AS name, d.dataset_funder AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key WHERE a.name_affiliation_msuDepartment = $example_college GROUP BY d.dataset_funder';
  //dataset_datePublished Y/M/D
$query_string_scatter_plot = 'SELECT d.dataset_datePublished AS data_date, a.name_affiliation_msuDepartment_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key WHERE a.name_affiliation_msuDepartment = $example_college GROUP BY d.dataset_datePublished';

  //each specific repository
  $repositories = @mysql_query($query_string9);

  $repository_rows = [];
  while($row = mysql_fetch_assoc($repositories)){
    $repository_rows[] = $row;
  }
  //the number of datasets associated with each specific repository
  $repository_counts = [];
  foreach($repository_rows as $repository){
    $query_string = 'SELECT COUNT(dataset_name) FROM datasets WHERE dataset_repositoryName = "'.$repository['dataset_repositoryName'].'";';
    $result = @mysql_query($query_string);
    $count = mysql_fetch_assoc($result);
    array_push($repository_counts, $repository['dataset_repositoryName'], $count['COUNT(dataset_name)']);
  }
  // print_r($repository_counts);

  //query the database
  $datasets_csv = @mysql_query($query_string_horz_bar_1);
  $datasets = @mysql_query($query_string_horz_bar_1);

  convertToCSV($datasets_csv);//Shorter department names?

  $json_datasets_horz_bar_1 = convertToJSON($datasets);

  ////////

  $datasets = @mysql_query($query_string_horz_bar_2);
  $datasets_csv = @mysql_query($query_string_horz_bar_2);

  convertToCSV($datasets_csv);//Why does Colleges have two different Letters and Sciences?

  $json_datasets_horz_bar_2 = convertToJSON($datasets);

  ////////

  $datasets = @mysql_query($query_string_scatter_plot);
  $datasets_csv = @mysql_query($query_string_scatter_plot);

  convertToCSV($datasets_csv);

  $json_datasets_scatter_plot = convertToJSON($datasets);


  if(!$datasets){

    echo '<p class="warn">Error in retrieving datasets from database!<br />'.'Error: '. mysql_error() .'</p>';

  }

  $num_datasets = mysql_num_rows($datasets);

  if($num_datasets == 0){

    echo '<p class="warn">No datasets were found for this dashboard!</p>';

  }else{

    //todo: funnel data into D3JS here for infographic

    //convert data into csv

    //define script tag here to call all infographics
  }

}
if($filter == null) {
  //filter is not set to display specific dashboards, display default dashboard

 //  //query string to find 20 most recent datasets
 //  $query_string1 = 'SELECT d.dataset_name, c.creator_name, a.name_affiliation_msuDepartment FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key ORDER BY recordInfo_recordModified LIMIT 20';
 //  //query string to find total number of datasets
        // $query_string2 = 'SELECT COUNT(dataset_name) FROM datasets;';
 //  //query string to find total number of authors
        // $query_string3 = 'SELECT COUNT(creator_name) FROM creators';
 //  //query string to find number of colleges represented
 //  $query_string4 = 'SELECT DISTINCT COUNT(name_affiliation_msuCollege) FROM affiliations;';
 //  //query string to find number of departments represented
 //  $query_string5 = 'SELECT DISTINCT COUNT(name_affiliation_msuDepartment) FROM affiliations;';
 //  //query string to find datasets by their creation date
 //  $query_string6 = 'SELECT dataset_name, recordInfo_recordCreationDate FROM datasets;';
 //  //query string to find datasets by their modified date
 //  $query_string7 = 'SELECT dataset_name, recordInfo_recordModified FROM datasets';
 //  //query string to find all keywords in the database
 //  $query_string8 = 'SELECT dataset_name, dataset_category1, dataset_category2, dataset_category3, dataset_category4, dataset_category5 FROM datasets;';
 //  //query string to find where our data is, the counts of datasets from each repository
 $query_string9 = 'SELECT DISTINCT dataset_repositoryName FROM datasets;';

  //query for the first horizontal bar chart https://www.w3resource.com/mysql/aggregate-functions-and-grouping/aggregate-functions-and-grouping-count-with-group-by.php
  $query_string_horz_bar_1 = 'SELECT a.name_affiliation_msuDepartment AS name, a.name_affiliation_msuDepartment_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key GROUP BY a.name_affiliation_msuDepartment';
  //query for the first horizontal bar chart https://www.w3resource.com/mysql/aggregate-functions-and-grouping/aggregate-functions-and-grouping-count-with-group-by.php
  $query_string_pie_chart = 'SELECT a.name_affiliation_msuDepartment AS name, a.name_affiliation_msuDepartment_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key GROUP BY a.name_affiliation_msuDepartment';
  //query for the second horizontal bar chart
  $query_string_horz_bar_2 = 'SELECT a.name_affiliation_msuCollege AS name, a.name_affiliation_msuCollege_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key GROUP BY a.name_affiliation_msuCollege';
  //dataset_datePublished Y/M/D
  $query_string_scatter_plot = 'SELECT d.dataset_datePublished AS data_date, a.name_affiliation_msuCollege_abbr AS abbr, COUNT(*) AS count FROM datasets d JOIN creators c ON d.recordInfo_recordIdentifier = c.recordInfo_recordIdentifier JOIN affiliations a ON c.creator_key = a.creator_key GROUP BY d.dataset_datePublished';

  //each specific repository
  $repositories = @mysql_query($query_string9);

  $repository_rows = [];
  while($row = mysql_fetch_assoc($repositories)){
    $repository_rows[] = $row;
  }
  //the number of datasets associated with each specific repository
  $repository_counts = [];
  foreach($repository_rows as $repository){
    $query_string = 'SELECT COUNT(dataset_name) FROM datasets WHERE dataset_repositoryName = "'.$repository['dataset_repositoryName'].'";';
    $result = @mysql_query($query_string);
    $count = mysql_fetch_assoc($result);
    array_push($repository_counts, $repository['dataset_repositoryName'], $count['COUNT(dataset_name)']);
  }
  // print_r($repository_counts);

  //query the database NOTE: you could use these $datasets variables to populate a menu for the filter
  $datasets_csv = @mysql_query($query_string_horz_bar_1);
  $datasets = @mysql_query($query_string_horz_bar_1);

  convertToCSV($datasets_csv);//Shorter department names?

  $json_datasets_horz_bar_1 = convertToJSON($datasets);

  //query the database
  $datasets_csv = @mysql_query($query_string_pie_chart);
  $datasets = @mysql_query($query_string_pie_chart);

  convertToCSV($datasets_csv);//Shorter department names?

  $json_datasets_pie_chart = convertToJSON($datasets);
 //echo '<p>'.$json_datasets_pie_chart.'</p>';

 ////////

  $datasets = @mysql_query($query_string_horz_bar_2);
  $datasets_csv = @mysql_query($query_string_horz_bar_2);

  convertToCSV($datasets_csv);//Why does Colleges have two different Letters and Sciences?

  $json_datasets_horz_bar_2 = convertToJSON($datasets);

  ////////

  $datasets = @mysql_query($query_string_scatter_plot);
  $datasets_csv = @mysql_query($query_string_scatter_plot);

  convertToCSV($datasets_csv);

  $json_datasets_scatter_plot = convertToJSON($datasets);


  //now create the visualization
?>
    <div class="box bar-chart1" id="horizontal-bar-chart-1"><h3>Collaborators per Department</h3></div>
<script>//source: https://www.d3-graph-gallery.com/graph/barplot_horizontal.html
        // set the dimensions and margins of the graph
        var margin = {top: 20, right: 30, bottom: 40, left: 90},
            width = 375 - margin.left - margin.right,
            height = 300 - margin.top - margin.bottom;

        // append the svg object to the body of the page
        var svg = d3.select("#horizontal-bar-chart-1")
          .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
          .append("g")
            .attr("transform",
                  "translate(" + margin.left + "," + margin.top + ")");

        // Parse the Data
        // data = [{name:"BIO", value:100},{name:"M",value:200},{name:"PHYS", value:50},{name:"KYN", value:25},{name:"CSCI", value:150},{name:"ENG", value:250}] //Dummy Data

        data = <?php echo $json_datasets_horz_bar_1 ?>;
        //document.write(data);
var maxDomain = d3.max(data, function(d) {return d.count;});

          // Add X axis
        var x = d3.scaleLinear()
            .domain([0,2 + + maxDomain])
            .range([ 0, width]);
        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x))
            .selectAll("text")
              .attr("transform", "translate(-10,0)rotate(-45)")
              .style("text-anchor", "end");

        // Y axis
        var y = d3.scaleBand()
            .range([ 0, height ])
            .domain(data.map(function(d) { return d.abbr; }))
            .padding(.3); //This effects the thickness of the barcharts
        svg.append("g")
            .call(d3.axisLeft(y));

          //Bars
        svg.selectAll("myRect")
            .data(data)
            .enter()
            .append("rect")
            .attr("x", x(0) )
            .attr("y", function(d) { return y(d.abbr); })
            .attr("width", function(d) { return x(d.count); })
            .attr("height", y.bandwidth() )
            .attr("fill", "#ffcb06");
      </script>

    <div class="box pie-chart" id="pie-chart"><h3>Total Datasets by Department</h3></div>
<script>//source: https://www.d3-graph-gallery.com/graph/donut_basic.html

        data = [{name:"BIO", value:100},{name:"KYN", value:25},{name:"CSCI", value:150},{name:"ENG", value:250}]
        //data = <?php //echo $json_datasets_pie_chart; ?>
        <?php //echo $json_datasets_pie_chart; ?>

        // set the dimensions and margins of the graph
        var width = 300
            height = 300
            margin = 25

        // The radius of the pieplot is half the width or half the height (smallest one). I subtract a bit of margin.
        var radius = Math.min(width, height) / 2 - margin

        // set the color scale
        var color = d3.scaleOrdinal()
          .range(["#003f7f", "#1a80c9", "#ffcb05", "#f5c037"]);

        //size of the circle
        var arc = d3.arc()
          .outerRadius(radius)
          .innerRadius(100);    // This is the size of the donut hole

        // Compute the position of each group on the pie:
        var pie = d3.pie()
          //.sort(null)
            .value(function(d) {return d.value; });

        // append the svg object to the div called 'my_dataviz'
        var svg = d3.select("#pie-chart")
          .append("svg")
            .attr("width", width)
            .attr("height", height)
            .append("g")
              .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

        // Create dummy data
        //var data = {a: 9, b: 20, c:30, d:8, e:12}

        // Build the pie chart: Basically, each part of the pie is a path that we build using the arc function.
        var g = svg.selectAll("arc")
          .data(pie(data))
          .enter().append("g")
          .attr("class", "arc");

        g.append("path")
          .attr("d", arc)
          .style("fill", function(d) { return color(d.data.name); });
      </script>    

<div class="box bar-chart2" id="horizontal-bar-chart-2"><h3>Total Datasets by Department</h3></div>
<script>//source: https://www.d3-graph-gallery.com/graph/barplot_horizontal.html
        // set the dimensions and margins of the graph
        var margin = {top: 20, right: 30, bottom: 40, left: 90},
            width = 375 - margin.left - margin.right,
            height = 300 - margin.top - margin.bottom;

        // append the svg object to the body of the page
        var svg = d3.select("#horizontal-bar-chart-2")
          .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
          .append("g")
            .attr("transform",
                  "translate(" + margin.left + "," + margin.top + ")");

        // Parse the Data
        // data = [{name:"BIO", value:100},{name:"M",value:200},{name:"PHYS", value:50},{name:"KYN", value:25},{name:"CSCI", value:150},{name:"ENG", value:250}] //Dummy Data

        data = <?php echo $json_datasets_horz_bar_2 ?>;


        var maxDomain = d3.max(data, function(d) {return d.count;});
        //document.write(maxDomain);

          // Add X axis
        var x = d3.scaleLinear()
            .domain([0,5 + + maxDomain])
            .range([ 0, width]);
        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x))
            .selectAll("text")
              .attr("transform", "translate(-10,0)rotate(-45)")
              .style("text-anchor", "end");

        // Y axis
        var y = d3.scaleBand()
            .range([ 0, height ])
            .domain(data.map(function(d) { return d.abbr; }))
            .padding(.3); //This effects the thickness of the barcharts
        svg.append("g")
            .call(d3.axisLeft(y));

          //Bars
        svg.selectAll("myRect")
            .data(data)
            .enter()
            .append("rect")
            .attr("x", x(0) )
            .attr("y", function(d) { return y(d.abbr); })
            .attr("width", function(d) { return x(d.count); })
            .attr("height", y.bandwidth() )
            .attr("fill", "#003f7f");
      </script>

    <div class="box scatter-chart" id="scatter-chart-1"><h3>Total Datasets by Month</h3></div>
<script>//source: https://www.d3-graph-gallery.com/graph/connectedscatter_basic.html
        // set the dimensions and margins of the graph
        var margin = {top: 10, right: 30, bottom: 30, left: 60},
            width = 375 - margin.left - margin.right,
            height = 300 - margin.top - margin.bottom;
        // append the svg object to the body of the page
        var svg = d3.select("#scatter-chart-1")
          .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
          .append("g")
            .attr("transform",
                  "translate(" + margin.left + "," + margin.top + ")");
        

        // Parse the Data
        data = [{date:"2018-08", value:10},{date:"2018-09",value:17},{date:"2018-10", value:22},{date:"2018-11", value:35},{date:"2018-12", value:35},{date:"2019-01", value:42}] //Dummy Data

        var maxDomain = d3.max(data, function(d) {return d.value;});
        var minDomain = d3.min(data, function(d) {return d.value;});


        var parseTime = d3.timeParse("%Y-%m");

        var dates = [];
        for (var obj of data){
          dates.push(parseTime(obj.date));
        };
        //document.write(dates);
        var domain = d3.extent(dates);

        //document.write(domain);

          // Add X axis --> it is a date format
          var x = d3.scaleTime()
            .domain(domain)
            .range([ 0, width ]);
          svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x))
            .selectAll("text")
              .attr("transform", "translate(-10,0)rotate(-45)")
              .style("text-anchor", "end");


          // Add Y axis
          var y = d3.scaleLinear()
            .domain( [minDomain, maxDomain])
            .range([ height, 0 ]);
          svg.append("g")
            .call(d3.axisLeft(y));
          // Add the line
          svg.append("path")
            .datum(data)
            .attr("fill", "none")
            .attr("stroke", "#003f7f")
            .attr("stroke-width", 1.5)
            .attr("d", d3.line()
              .x(function(d) { return x(parseTime(d.date)) })
              .y(function(d) { return y(d.value) })
              )
          // Add the points
          svg
            .append("g")
            .selectAll("dot")
            .data(data)
            .enter()
            .append("circle")
              .attr("cx", function(d) { return x(parseTime(d.date)) })
              .attr("cy", function(d) { return y(d.value) } )
              .attr("r", 5)
              .attr("fill", "#003f7f")
      </script>
<?php
  }
function convertToJSON($queryResult){
  if(!$queryResult){

    echo '<p class="warn">Error in retrieving datasets from database!<br />'.'Error: '. mysql_error() .'</p>';
  }
$num_datasets = mysql_num_rows($queryResult);

  if($num_datasets == 0){

    echo '<p class="warn">No datasets were found for this dashboard!</p>';
  }else{

    //get all the rows from the query
    $datasets_rows = array();
    $firstElement = true;

    //append all rows to php array
    while($row = mysql_fetch_assoc($queryResult)){
      if($firstElement){
        $firstElement = false;
      }
      else{
        //print_r($row);
        $datasets_rows[] = $row;
      }

    }


    //print_r($datasets_rows);

    //transform php array into JSON for D3
    $json_datasets = json_encode($datasets_rows);

    return $json_datasets;
    //print_r($json_datasets);
  }
}

//converts to friendly CSV, but also prints out values of CSV
function convertToCSV($queryResult){

  $data = '';
  $header = '';
  $fields = mysql_num_fields ( $queryResult );

  for ( $i = 0; $i < $fields; $i++ )
  {
      $header .= mysql_field_name( $queryResult , $i ) . "\t";
  }

  while( $row = mysql_fetch_row( $queryResult ) )
  {
      $line = '';
      foreach( $row as $value )
      {
          if ( ( !isset( $value ) ) || ( $value == "" ) )
          {
              $value = "\t";
          }
          else
          {
              $value = str_replace( '"' , '""' , $value );
              $value = '"' . $value . '"' . "\t";
          }
          $line .= $value;
      }
      $data .= trim( $line ) . "\n";
  }
  $data = str_replace( "\r" , "" , $data );

  if ( $data == "" )
  {
      $data = "\n(0) Records Found!\n";
  }

  // header("Content-type: application/octet-stream");
  // header("Content-Disposition: attachment; filename=your_desired_name.xls");
  // header("Pragma: no-cache");
  // header("Expires: 0");
  // print "$header\n$data";

  //print_r($data);
  //return $data;
}
?>
  </main>
  <footer role="contentinfo">
    <p>© Copyright Montana State University (MSU) Library</p>
  </footer>
  <script src="./meta/scripts/global.js" defer="defer"></script>
</body>
</html>
