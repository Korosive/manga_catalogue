<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta name="author" content="Eddie Taing"/>
	<meta name="description" content="Manga Tracker" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<title>Home Page</title>
</head>
<?php
	include "nav.php";
?>
<body>
	<h1 class="text-center border-bottom">My Manga List</h2>
	<?php
		if (isset($_SESSION['message']))
		{
			echo "<p>" . $_SESSION['message'] . "</p>";
			unset($_SESSION['message']);
		}
	?>
	<form class="row row-cols-lg-auto g-3 align-items-center justify-content-center" method="get" action="index.php">
		<div class="col-12">
			<div class="input-group">
				<input class="form-control" type="text" name="title" id="title" />
			</div>
		</div>
		<div class="col-12">
			<input class="btn btn-primary" type="submit" name="Search" value="Search" />
		</div>
	</form>
	<?php
		require_once 'settings.php';
		$searchresults = array();

		$conn = new mysqli($host, $user, $pswd, $db);

		//Check if error with connection
	   	if ($conn->connect_errno)
	    {
	       	echo "<p>Failed to connect to database: " . $conn->connect_error . "</p>";
	        exit();
	   	}

	    $tablequery = "CREATE TABLE IF NOT EXISTS mangas(
	    	record_id INT NOT NULL AUTO_INCREMENT,
	        mal_id INT NOT NULL,
	        eng_name VARCHAR(100) NOT NULL,
	        jp_name VARCHAR(100) NOT NULL,
	        author VARCHAR(30) NOT NULL,
	        run_start DATE NOT NULL,
	        run_end DATE,
	        read_state VARCHAR(20) NOT NULL,
	        PRIMARY KEY (record_id)
	    );";

	    $conn->query($tablequery);
	    
		if (isset($_GET['title']))
		{
			$title = $_GET['title'];

			if ($title)
			{
	        	$searchquery = "SELECT * FROM mangas WHERE eng_name LIKE '%{$title}%' OR jp_name LIKE '%{$title}%'";

	    		$searchresult = $conn->query($searchquery);

	    		if ($searchresult->num_rows > 0)
	    		{
	    			while($row = $searchresult->fetch_assoc())
	    			{
	    				$searchresults[] = $row;
	    			}
	    		}
			}
			else
			{
				echo "<p>Something wrong with search</p>";
			}
		}
		else
		{
			$searchquery = "SELECT * FROM `mangas`";

			$searchresult = $conn->query($searchquery);

			if ($searchresult->num_rows > 0)
	    	{
	    		while($row = $searchresult->fetch_assoc())
	    		{
	    			$searchresults[] = $row;
	    			
	    		}
	    	}
		}

		$conn->close();

		if (sizeof($searchresults) > 0)
		{
			echo "<table class='table table-striped table-hover table-bordered'>";
	    	echo "<tr><th>English Name</th><th>Japanese Name</th><th>Author</th><th>Original Run</th><th>Current State</th><th>Change Status?</th><th>Remove</th></tr>";
	    	foreach ($searchresults as $result) {
				echo "<tr>";
		    	echo "<td>" . $result['eng_name'] . "</td>";
		    	echo "<td>" . $result['jp_name'] . "</td>";
		    	echo "<td>" . $result['author'] . "</td>";
		    	echo "<td>" . date("d/m/Y", strtotime($result['run_start'])) . " - " . date("d/m/Y", strtotime($result['run_end'])) . "</td>";
		    	echo "<td>" . $result['read_state'] . "</td>";
		    	echo "<td>";

		    	echo "<form class='row row-cols-lg-auto g-3' method='POST' action='change_status.php'>";
		    	echo "<input type='hidden' name='record_id' id='record_id' value='" . $result['record_id'] . "'/>";
		    	echo "<div class='col-12'>";
		    	echo "<div class='input-group'>";
		    	echo "<select class='form-select' name='status' id='status'>";
		    	echo "<option value='Reading'>Reading</option>";
		    	echo "<option value='Completed'>Completed</option>";
		    	echo "<option value='On-Hold'>On-Hold</option>";
		    	echo "<option value='Dropped'>Dropped</option>";
		    	echo "<option value='Planned To Read'>Planned To Read</option>";
		    	echo "</select>";
		    	echo "</div>";
		    	echo "</div>";
		    	echo "<div class='col-12'>";
		    	echo "<input class='btn btn-primary' type='submit' value='Change Status'/>";
		    	echo "</div>";
		    	echo "</form>";

		    	echo "</td>";
		    	echo "<td><a href='remove.php?id=" . $result['record_id'] . "'>X</a></td>";
		    	echo "</tr>";
			}
			echo "</table>";
		}
		else
		{
			echo "<p>No manga</p>";
		}
		
	?>
</body>
</html>