<?php
    
	include("PHPCrawl_083/libs/PHPCrawler.class.php");
	include("simple_html_dom.php");
	error_reporting(E_ALL);
	
	
	
	function determineTopic($html)
  {
	  $article = $html->find('body', 0)->plaintext;
	  $tr = substr_count($article, "Trump");	//to be expanded by loading topic list
	  $Ob = substr_count($article, "Obama");
	  if($tr+$Ob == 0)
		  return 0;
	  if($tr<$Ob)
		  return 1;
	  else
		  return 2;
  }
  
  function insertTableElement($topic, $url, $date, $title)
  {
	  $date = preg_replace('#\/#','-', $date).' 00:00:00';
	  require("config.php");
	  $title = preg_replace('#\'#','\'\'', $date).' 00:00:00';
	  $command = 'insert into articles (idx, date, url, title) values ('.$topic.',\''.$date.'\',\''.$url.'\',\''.$title.'\')';
	  echo $command;
	  $stmt = $dbh->prepare($command);
	  $stmt->execute();
  }
	
	class MyCrawler extends PHPCrawler 
{ 

  function handleDocumentInfo(PHPCrawlerDocumentInfo $PageInfo) 
  { 
	preg_match( '/[0-9]{4}\/[0-9]{2}\/[0-9]{2}/',$PageInfo->url, $m);
	if($m == null)
		return;
	$html = str_get_html($PageInfo->content);
	$date = $m[0]; 
	$topic = determineTopic($html);
	$title = $html->find('title', 0)->plaintext;
	$html->clear();
	unset($html);
	if($topic != 0)
		insertTableElement($topic, $PageInfo->url, $date, $title);
	
	echo $topic.": ".$title;
    echo "<br>\n"; 
  } 
  
  
  
} 
	
	$crawler = new MyCrawler();
	$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i"); 
	$crawler->setRequestLimit(200);
	$crawler->addContentTypeReceiveRule("#text/html#"); 
	$crawler->setURL("www.cnn.com"); 
	$crawler->setFollowMode(1);
	
	if(!$crawler->addLinkPriority("/Obama/",5))
		echo "failure 59";
	if(!$crawler->addLinkPriority("/Trump/",5))
		echo "failure 61";
		
	
	if(!$crawler->addURLFollowRule("#.*politics.*$# i"))
		echo "failure";

	
	$crawler->go();  
	$report = $crawler->getProcessReport(); 

	if (PHP_SAPI == "cli") $lb = "\n"; 
	else $lb = "<br />"; 
		 
	echo "Summary:".$lb; 
	echo "Links followed: ".$report->links_followed.$lb; 
	echo "Documents received: ".$report->files_received.$lb; 
	echo "Bytes received: ".$report->bytes_received." bytes".$lb; 
	echo "Process runtime: ".$report->process_runtime." sec".$lb;  
	

?>