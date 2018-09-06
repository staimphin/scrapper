Scrapper

Author: Gregory Staimphin
Mail: gregory.staimphin@gmail.com
Langage: PHP

What is scrapper?
Scrapper is designed for retrieving data (For example the news of a webpage) between specifics tags from an provided URL  and provide the result in  JSON format.
1) The script is parsing the source page for a wrapper defined by an HTML tag (div, ul, section ...) and identifier ( ID or class name )
2) The data will be processed in order to retrieve  list of items (for example a list of li) matching the tag set in 'target_wrapper settings'
3) For each items of the list, a content search will be performed according the 'search' parameters. 
The matching contents will be saved as follow: SEARCH_KEY => MATCHING_CONTENT
4) The result is a JSON object 
[
{ SEARCH_KEY1: MATCHING_CONTENT, SEARCH_KEY2: MATCHING_CONTENT,..,  SEARCH_KEYn => MATCHING_CONTENT},
{ SEARCH_KEY1: MATCHING_CONTENT, SEARCH_KEY2: MATCHING_CONTENT,..,  SEARCH_KEYn => MATCHING_CONTENT}
]

How to use?

First include the scrapper class in your project.

The format for the settings option $arg are the following:

$args = array(
 'URL' => '',// Source URL
 'wrapper' => 
	array(
		'tag' => '',//HTML tag of the target's wrapper : More likely to be a section / div / ul
		'identifier' => '' // Element ID or class name of the wrapper
	),
 'target_wrapper' => '',//HTML tag of targeted list: for example dl / li 
 'search' => //labeled array: the researched keys will be used in the generated JSON. Search is not limited to the following arguments.
	array(
		'date' => '', // Date built in pattern: will perform a research for a date YYYY.?MM.?DD after the specified argument
		'label' => '',// Label built in pattern: will perform a research for a value between inside a double quote  after the specified argument
		'url' => '',// URL built in pattern: will perform a research for the href attribute after the specified argument
		'description' => '',// Generic pattern: will perform a research for the content enclosed within the specified html tag
		//  any additional key will perform a research for the content enclosed within the specified html tag
	),
);
Then, You can create a new instance of scrapper by using:

new scrapper($args);

or 
$scrapper = new scrapper();
$scrapper->setUp($arg);


Results:

the Json data are retrieved by using

$scrapper->retrieveData();
in order to display the data you should echo the result.
