<?php
/** * Simple page scrapper * * @author : gregory.staimphin@gmail.com * @date:  2018-09-06 */
 
 class scrapper {
	private $curl;//CURL instance
	private $json = array();
	private $mesg = array();
	private $opts;//Curl Options
//	private $jsonKeys = array();//the output keys for the json file.
	private $source;//Source code to scrap
	private $scrapped = array();//retrieved data
	private $allowedKeys = array('wrapper', 'search','target_wrapper');
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
	 * @var example: 
	 * [ 'URL' => 'https://google.com',
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
	 * retrieve specific bloc from source code
	 *
	 *
	 */
	public function scrapSource($pattern )
	{
		preg_match_all($pattern, $this->source, $matches);
		//Put the results
		$this->scrapped = !empty($matches[0]) ? $matches[0] : array();
	}

	
	/**	 *	 * display the result of the process	 *	 *	 */
	public function getMesg()
	{
		//$this->retrieveData();
		echo $this->retrieveData();
	}

	/**	 *	 *	 * @return: returns a JSON  data	 *	 */
	public function retrieveData()
	{
		//get source
		$this->getPage();
		//print_r($this->search);
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
			$this->mesg[] = 'No results';
			$JSON['error'] = json_encode($this->mesg);
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
		//custom: removing the - from the laebl
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
 