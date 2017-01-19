<html>
   <body>
Index Successfully Editied
<?php
    // Defining the basic cURL function
    function curl($url) {
        $ch = curl_init();  // Initialising cURL
        curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
        curl_close($ch);    // Closing cURL
        return $data;   // Returning the data from the function
    }
?>

<?php
    $scraped_website = curl("http://www.cnn.com/terms");  // Executing our curl function to scrape the webpage http://www.example.com and return the results into the $scraped_website variable
	echo " Here's the scraped website: ";
	echo $scraped_website;
?>
	</body>
</html>