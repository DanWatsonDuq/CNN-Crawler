<?php
    
	include("PHPCrawl_083/libs/PHPCrawler.class.php");
	include("simple_html_dom.php");
	
	class MyCrawler extends PHPCrawler 
{ 
  function handleDocumentInfo(PHPCrawlerDocumentInfo $PageInfo) 
  { 
    // print out the URL of the document 
	$html = new simple_html_dom();
	$html->load($PageInfo->content);
	$title = $html->find('title', 0);

	if($title != NULL)
		echo $title->plaintext." ";
	else
		echo "failed to get title";
	
    echo "<br>\n"; 
  } 
} 
	
	
	//using curl to fetch the requested website as is.
    function curl($url) {
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $data = curl_exec($ch); 
        curl_close($ch);
        return $data;   
    }
	
	$crawler = new MyCrawler();
	$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i"); 
	$crawler->setTrafficLimit(10000 * 1024);
	$crawler->addContentTypeReceiveRule("#text/html#"); 
	$crawler->setURL("www.cnn.com"); 
	$crawler->setFollowMode(1);
	
	if(!$crawler->addLinkPriority("Obama",5))
		echo "failure";
	if(!$crawler->addLinkPriority("trump",5))
		echo "failure";
		
	
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