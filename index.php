<!DOCTYPE html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Obama and Trump</title>
	
	<!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Custom styles for this template -->
    <link href="jumbotron-narrow.css" rel="stylesheet">

</head>
<body>
<div class="container">
<div class="jumbotron">
 <h1>Barrack Obama & Donald Trump<h2>
</div>
    <table>
        <tbody>
		<div class="row marketing">
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
                <tr>
                    <td><a href="<?php echo$Orow['url']."\">".$Orow['title']?></a></td>
					<td><a href="<?php echo$Trow['url']."\">".$Trow['title']?></a></td>
                </tr>
            <?php
            }
            ?>  
		</div>
            </tbody>
            </table>
</div>
</body>
</html>