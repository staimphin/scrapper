<?php
/** * Simple page scrapper * * @author : gregory.staimphin@gmail.com * @date:  2018-09-06 */
 include('class/class_scrapper.php');
 include('lib/settings.php');

//connect to URL
$scrapper = new scrapper($args);

// return scrapped data as json
echo $scrapper->retrieveData();
