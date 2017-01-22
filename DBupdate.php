<?php
    
	include("PHPCrawl_083/libs/PHPCrawler.class.php");
	include("simple_html_dom.php");
	error_reporting(E_ALL);
	set_time_limit(120);
	
	
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
  
  function insertTableElement($topic, $url, $date, $title, $preview)
  {
	  $date = preg_replace('#\/#','-', $date).' 00:00:00';
	  require("config.php");
	  $title = preg_replace('#\'#','\'\'', $title);
	  $command = 'select 1 from articles where url= \''.$url.'\'';
	  //echo $command.'<br>';
	  $stmt = $dbh->prepare($command);
	  $stmt->execute();
	  if($stmt->fetch())
		  return;
	  //$preview = $html->find('p', 3)->plaintext;
	  //$title = str_replace("'", "''", $title);
	  //$preview= str_replace("'", "''", $preview);
	  $command = 'insert into articles (idx, date, url, title, body) values ('.$topic.',\''.$date.'\',\''.$url.'\','.$dbh->quote($title).','.$dbh->quote($preview).')';
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
	$html = new simple_html_dom();
	$html->load($PageInfo->content);
	//echo $PageInfo->content;
	if(is_null($html))
	{
		echo "failed to get page: ".$PageInfo->url;
		return;
	}
	$date = $m[0]; 
	$topic = determineTopic($html);
	//$body = getPreview($html);
	$title = str_replace('- CNNPolitics.com','',$html->find('title', 0)->plaintext);
	//$bodyHtmlRaw = $PageInfo->content;
	$preview = "";
	foreach($html->find('div class="zn-body__paragraph"') as $p){
		$preview = $preview.($p->plaintext);
	}
	
	//foreach($body as $p){
	//	if(strlen($p->plaintext)>strlen($preview))
	//		$preview = $p->plaintext;
	//}
	$preview = preg_replace("#.*\(CNN\)#", "", $preview);
	$preview = substr($preview, 0, 600).'...';
	$html->clear();
	unset($html);
	if($topic != 0)
		insertTableElement($topic, $PageInfo->url, $date, $title, $preview);
	
	//echo $topic.": ".$title;
    //echo "<br>\n"; 
  } 
  
  
  
  
  
} 
	
	$crawler = new MyCrawler();
	$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i"); 
	$crawler->setRequestLimit(200);
	$crawler->addContentTypeReceiveRule("#text/html#"); 
	$crawler->setURL("http://www.cnn.com/politics"); 
	$crawler->setFollowMode(1);
	
	if(!$crawler->addLinkPriority("/Obama/",7))
		echo "failure";
	if(!$crawler->addLinkPriority("/Trump/",5))
		echo "failure";
		
	
	if(!$crawler->addURLFollowRule("#.*politics.*$# i"))
		echo "failure";
	
	$crawler->addURLFilterRule("#.*videos.*$# i");
	$crawler->addURLFilterRule("#.*gallery.*$# i");

	
	$crawler->go();  
	$report = $crawler->getProcessReport(); 

	if (PHP_SAPI == "cli") $lb = "\n"; 
	else $lb = "<br />"; 
		 
	echo "Summary:".$lb; 
	echo "Links followed: ".$report->links_followed.$lb; 
	echo "Documents received: ".$report->files_received.$lb; 
	echo "Bytes received: ".$report->bytes_received." bytes".$lb; 
	echo "Process runtime: ".$report->process_runtime." sec".$lb;  
	mail('vetemaster@gmail.com', 'DB Updating', 'Update Successful. Runtime = '.$report->process_runtime);
	

?>