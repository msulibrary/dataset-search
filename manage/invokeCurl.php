<?php

/*
 *  AJAX action to get feed contents
 */

// Get POST data
$feedUrl = isset($_POST['feedUrl']) ? $_POST['feedUrl'] : '';
$feedContentType = isset($_POST['feedContentType']) ? $_POST['feedContentType'] : '';

// Retrieving contents using curl
$curlSession = curl_init();
curl_setopt($curlSession, CURLOPT_URL, $feedUrl);
curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

if ($feedContentType == 'x')
{
	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('accept: application/xml'));
}
else
{
	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array('content-type: application/json, accept: application/json'));
}

$feedContents = curl_exec($curlSession);
curl_close($curlSession);

echo $feedContents;

?>
