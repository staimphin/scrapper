<?php
/**
 include('class/class_scrapper.php');
 include('lib/settings.php');

//connect to URL
$scrapper = new scrapper($args);

//retrieve specifics part of the page
//$scrapper->getPage();

// return scrapped data as json
$scrapper->getMesg();