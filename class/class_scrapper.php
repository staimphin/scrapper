<?php
/** * Simple page scrapper *
 * Scrapper is designed for retrieving some data from an URL (For example the news of a webpage)
 * and provide the result in  JSON format.
 * @version: V0.1 * @author : gregory.staimphin@gmail.com * @date:  2018-09-06 */
 
 class scrapper {
	private $curl;//CURL instance
	private $json = array();
	private $mesg = array();
	private $opts;//Curl Options
	private $source;//Source code to scrap
	private $scrapped = array();//retrieved data
	private $allowedKeys = array('wrapper', 'search','target_wrapper');// List of expected key in $arg

	/**
	 *
	 * Init the curl instance
	 * Set the scarpper and curl options  (optional)
	 *
	 */
	public function __construct($arg = array())
	{
		// Init the curl instance
		$this->curl = curl_init();
		
		if(!empty($arg)){
			$this->setUp($arg);
		}
	}

	/**
	 *
	 * Set the scrapper options
	 *
	 * @var $arg:  array
	 * Contain the URL of the source
	 *
	 * @fornat: 
	 * $args = array(
	 * 'URL' => '',// Source URL
	 *	array(
	 *		'tag' => '',//HTML tag of the target's wrapper : More likely to be a section / div / ul
	 *		'identifier' => '' // Element ID or class name of the wrapper
	 *	),
	 * 'target_wrapper' => '',//HML tag of targeted list: for example dl / li 
	 * 'search' => //labeled array: the researched keys will be used in the generated JSON. Search is not limited to the following arguments.
	 *	array(
	 *		'date' => '', // Date builtin patern: will perform a research for a date YYYY.?MM.?DD after the specified argument
	 *		'label' => '',// Label builtin patern: will perform a research for a value between inside a doublequote  after the speified argument
	 *		'url' => '',// URL builtin patern: will perform a research for the href attribute after the speified argument
	 *		'description' => '',// Generic patern: will perform a research for the content enclosed within the specified html tag
	 *		//  any additional key will perform a research for the content enclosed within the specified html tag
	 *	),
	 *);
	 *
	 *
	 */
	public function setUp($arg)
	{
		// set the  options for Curl	
		$this->opts = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
		];
		
		if(isset($arg['URL'])){
			$this->opts[CURLOPT_URL] =$arg['URL'];
		} else {
			$this->mesg[] ='URL not set';
		}

		//set the wrapper options
		foreach($this->allowedKeys as $key){
			if(isset($arg[$key])){
				$this->$key =$arg[$key];
			} else {
				$this->mesg[] = $key.' not set';
			}
		}
	}

	/**
	 * This function set the source to scrap
	 *
	 * Allow to set manually  some code to check according  the scrapper settings
	 *
	 * @var:  source to scrap
	 */
	public function setSource($source)
	{
		$this->source = $source;
	}

	/**	 *	 * Set into the private source the content of the specified URL	 *	 * @return: void	 */
	public function getPage()
	{
		curl_setopt_array($this->curl, $this->opts);

		$response = curl_exec($this->curl);
		curl_close($this->curl);
		
		//Filtering the sorce
		$pattern		= '@<'. $this->wrapper['tag'].'[^>]+'. $this->wrapper['identifier'].'.*>.*</'. $this->wrapper['tag'].'>@Umsix';
		preg_match($pattern, $response, $matches);
		
		//set the selected source if the regex is sucesfull, the full source other wise.
		$response = !empty($matches) ?  $matches[0] :$response;
		
		$this->setSource($response) ; 
	}

	/**
	 *
	 * retrieve the content of a specificied html tag bloc from source code
	 *  
	 * @var pattern: regular expression
	 *
	 */
	public function scrapSource($pattern )
	{
		preg_match_all($pattern, $this->source, $matches);
		//Save the results
		$this->scrapped = !empty($matches[0]) ? $matches[0] : array();
	}

	/**	 *	 *	 * @return: returns a JSON  data	 *	 */
	public function retrieveData()
	{
		//get source
		$this->getPage();
		// need to split the data into an array of target
		
		$pattern		= '@<'. $this->target_wrapper.'[^>]+>.*</'. $this->target_wrapper.'>@Umsix';
		$this->scrapSource($pattern);
		
		//retrieve the requested data from the source code
		foreach($this->scrapped as $data ){
			$this->json[] = $this->extraData($data);
		}
		$JSON = array();
		if(!empty($this->json)){
			$JSON = json_encode($this->json);
		} else {
			$this->mesg['error'] = 'No results';
			$JSON = json_encode($this->mesg);
		}
		
		return $JSON;
	}

	/**	 *	  @var $data:	 *	 * @return :	 */
	public function extraData($data)
	{
		$tmp = array();

		foreach($this->search as $key => $tag){
			switch($key){
				case 'url' :
					$pattern = '@'.$tag.'.*href="([^\'"]+.*)"@Umsix';	
				break;
				
				case 'date' :
					//assume date patern is YYYY?MM?DD
					$pattern = '@'.$tag.'.*([0-9]{4}.+[0-9]{2}.+[0-9]{2})@Umsix';	
				break;
				//Regexp matching an attribute pattern
				case 'label' :
					$pattern = '@'.$tag.'.*"(.*)"@Umsix';

				break;
				default:
					$pattern =  '@<'. $tag.'[^>]*>(.*)</'. $tag.'>@Umsix';				
				break;
			}

				preg_match($pattern, $data, $matches);
				$tmp[$key] = !empty($matches[1]) ?  $matches[1] :'';
		}
		//custom: removing the - from the label
		if(!empty($tmp['label'])){
			$tmp['label'] = str_replace( '-','',$tmp['label']);
		}
		//remove links / picture in order to keep the text
		if(!empty($tmp['description'])){
			$tmp['description'] = strip_tags($tmp['description']);
		}

		return $tmp;
	}

 }
 