<!DOCTYPE html>
<head>
    <title>Obama and Trump</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td>Obama</td>
                <td>Trump</td>
            </tr>
        </thead>
        <tbody>
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
                    <td><?php echo $Orow['title']?></td>
					<td><?php echo $Trow['title']?></td>
                </tr>
            <?php
            }
            ?>   
            </tbody>
            </table>
</body>
</html>