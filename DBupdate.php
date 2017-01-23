<?php
    

	include("simple_html_dom.php");
	error_reporting(E_ALL);
	set_time_limit(120);

	
	function determineTopic($html)					//given article text, determine subject
  {
	  $article = $html->find('body', 0)->plaintext;
	  $tr = substr_count($article, "Trump");	
	  $Ob = substr_count($article, "Obama");
	  if($tr+$Ob == 0)
		  return 0;
	  if($tr<$Ob)
		  return 1;
	  else
		  return 2;
  }
  
  function insertTableElement($topic, $url, $date, $title, $preview)	//preparing executing sql command
  {
	  require("config.php");
	  $title = preg_replace('#\'#','\'\'', $title);
	  $command = 'select 1 from articles where url= \''.$url.'\'';
	  $stmt = $dbh->prepare($command);
	  $stmt->execute();
	  if($stmt->fetch())	//check for duplicate article before inserting.
		  return;
	  $command = 'insert into articles (idx, date, url, title, body) values ('.$topic.',\''.$date.'\',\''.$url.'\','.$dbh->quote($title).','.$dbh->quote($preview).')';
	  echo $command;
	  $stmt = $dbh->prepare($command);
	  $stmt->execute();
  }
  
  
	
	class Crawler
	{ 
	public $URLFilterRules = array();
	public $URLFollowRules = array();
	public $URLCaptures = array();
	public $visited = array();
	public $articleCount=0;
	public $maximumArticleCount=200;
	
	function addFilter($rule){						//filters specify url's to not follow
		array_push($this->URLFilterRules, $rule);	//will follow iff no matches exist
	}
	function addFollow($rule){						//follow rules specify which urls to follow	
		array_push($this->URLFollowRules, $rule);	//will follow iff at least one match exists
	}
	function addCapture($rule){						//specifies which docs to capture.
		array_push($this->URLCaptures, $rule);
	}
	function shouldFollow($url){					//applies following rules.
		$success = false;
		foreach($this->URLFollowRules  as $rule){
			if(preg_match($rule, $url))
				$success = true;
		}
		if(!$success)
			return false;
		
		foreach($this->URLFilterRules  as $rule){
			if(preg_match($rule, $url))
				$success = false;
		}
		if(!$success)
			return false;
		
		return true;
	}
	
	function shouldCapture($url){				//applies capture rules.
		foreach($this->URLCaptures as $rule){
			if(preg_match($rule, $url))
				return true;
		}
	}
	
	
	function crawl($base,$relurl, $depth)	//base remains the same, relurl determines which page is displayed
	{	
		$url = $base.$relurl;
		echo "getting ".$url."<br>";
		$this->articleCount++;
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);	//fetch page with curl

		$page = curl_exec($c);		
		
		$html =  str_get_html($page);
		
		if(!is_object($html)){							//check for request failure
			echo "Failed to get page: ".$url."<br>";
			return;
		}
		else
			echo "Got Page: ".$url."<br>";
		
		if($this->shouldCapture($url)){					//check capture rules
			$this->handleDocumentInfo($html, $url);
		}
		if($depth == 0)
			return;
		foreach($html->find('a') as $element){			//get all links
			 
			 $link = $element->getAttribute('href');	
			 if(substr_count($link, "http")!=0)
				 continue;
			 
			if($this->shouldFollow($base.$link)&&$this->articleCount<$this->maximumArticleCount&&!in_array($base.$link, $this->visited)){	//check if not already visited, under page budget, and obeys follow rules
				array_push($this->visited, $base.$link);
				$this->crawl($base, $link, $depth-1);
			}
			else
				echo "not following: ".$base.$link."<br>";
		}
	}
	
	


  function handleDocumentInfo($html, $url) 
  { 
	preg_match( '/[0-9]{4}\/[0-9]{2}\/[0-9]{2}/',$url, $m);
	if($m == null)
		return;
	$date = $m[0]; 
	$topic = determineTopic($html);
	$title = str_replace('- CNNPolitics.com','',$html->find('title', 0)->plaintext);
	$preview = "";
	foreach($html->find('div class="zn-body__paragraph"') as $p){
		$preview = $preview.($p->plaintext);
	}
	$preview = preg_replace("#.*\(CNN\)#", "", $preview);
	$preview = substr($preview, 0, 600).'...';
	unset($html);
	if($topic != 0)
		insertTableElement($topic, $url, $date, $title, $preview);
   } 
  
  
} 
	
	$crawler = new Crawler();
	$crawler->addCapture("#cnn.com\/.*\/(politics|money)#");
	$crawler->addFilter("#\.(jpg|jpeg|gif|png)$# i"); 
	$crawler->addFilter("#.*videos.*$# i");
	$crawler->addFilter("#.*gallery.*$# i");
	$crawler->addFollow("#politics|money#");
	$crawler->crawl("http://www.cnn.com","/politics", 5);
	echo "Complete. Articles Visited: ".$crawler->articleCount;

?>