<!DOCTYPE html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Obama and Trump</title>
	
	<!-- Bootstrap core CSS -->
    <link href="../bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Custom styles for this template -->
    <link href="../bootstrap-3.3.7-dist/css/jumbotron-narrow.css" rel="stylesheet">
	
	<link rel="stylesheet" href="../jquery/jquery.mobile-1.4.5.min.css">
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	
	
	
</head>
<body>

<div class="jumbotron">
<h1 width="100%">Barrack Obama & Donald Trump<h1>
</div>
    
	
       <?php
			require("config.php");
            $Ostmt = $dbh->prepare('select * from articles where idx=1 order by date desc');
			$Ostmt->execute();
			
			
			$Tstmt = $dbh->prepare('select * from articles where idx=2 order by date desc');
			$Tstmt->execute();
			
			
            for($i =0; $i<25; $i++) {
				$Orow = $Ostmt->fetch(PDO::FETCH_ASSOC);
				$Trow = $Tstmt->fetch(PDO::FETCH_ASSOC);
            ?>
					
					 <div data-role="main" class="ui-content">
						<div data-role="collapsible">
							<h1><?php echo $Orow['title']?> </h1>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo$Orow['body']?>
							
								<a href="<?php echo$Orow['url']."\">"."Read full article"?></a>
								<p>
								
								
						</div>
					</div>
				
					
					<div data-role="main" class="ui-content">
						<div data-role="collapsible">
							<h1><?php echo $Trow['title']?></h1>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo$Trow['body']?>
							
								<a href="<?php echo$Trow['url']."\">"."Read full article"?></a>
								<p>
						</div>
					</div>
					
               
            <?php
            }
            ?>  
		

</body>
</html>