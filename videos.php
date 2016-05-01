<?php
	session_start();	
?>

<html>
    
    <?php include_once "header.php" ?>

	<div class="mdl-grid">
	        

<?php
	require_once 'dbConnection.php';

	$dbCon = new dbConnection();
	$con = $dbCon->con;

	$result = mysqli_query($con, "SELECT * FROM videos");

	if ($result && mysqli_num_rows($result) != 0) {
		$num = 0;
		while($row = mysqli_fetch_assoc($result)) {
?>

		<div class="mdl-cell mdl-cell--2-col">
			<div class="demo-card-square mdl-card mdl-shadow--2dp" style="width: 180px; height: 220px;">
		        <div class="mdl-card__title mdl-card--expand" style="color: #fff; background:
				    url('./thumbs/<?php echo $row["Id"]; ?>.jpg') no-repeat; background-size:cover;">
				    <!--h2 class="mdl-card__title-text">Update</h2-->
				</div>
				<div class="mdl-card__supporting-text">
				    <?php echo $row["Title"]; ?>
			    </div>
		        <div class="mdl-card__actions mdl-card--border">
		            <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"
		            	href=translaterRedirector.php?id=<?php echo $row["Id"]; ?>>
		                Translate
		            </a>
		        </div>
		    </div>
		</div>

<?php
		}
	}		
?>

		</div>
	<?php include_once "footer.php" ?>
</html>