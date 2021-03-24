<?php
//purpose: hold specific login data for database connections
//security: the directory where this file lives must have .htaccess file allowing only calls from php files to have read access to it.

//db name, login data and connection directions
mysql_connect("localhost","ADD-YOUR-DB-USER","ADD-YOUR-DB-PASSWORD") or die("Error: Unable to connect to the database.");	
mysql_select_db("ADD-YOUR-DB-NAME");

?>
