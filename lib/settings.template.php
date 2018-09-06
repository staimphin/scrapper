<?php 
/** *  settings.php * * Purpose of scrapper is to retrieve a list of items from a specified source * *///Arguments for class scrapper
$args = array(
 'URL' => '',// Source URL
 'wrapper' => 
	array(
		'tag' => '',//HTML tag of the target's wrapper : More likely to be a section / div / ul
		'identifier' => '' // Element ID or class name of the wrapper
	),
 'target_wrapper' => '',//HML tag of targeted list: for example dl / li 
 'search' => //labeled array: the researched keys will be used in the generated JSON. Search is not limited to the following arguments.
	array(
		'date' => '', // Date builtin patern: will perform a research for a date YYYY.?MM.?DD after the specified argument
		'label' => '',// Label builtin patern: will perform a research for a value between inside a doublequote  after the speified argument
		'url' => '',// URL builtin patern: will perform a research for the href attribute after the speified argument
		'description' => '',// Generic patern: will perform a research for the content enclosed within the specified html tag
		//  any additional key will perform a research for the content enclosed within the specified html tag
	),
);

