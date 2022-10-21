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
	<h1>Home Page</h1>
	<h2>My Manga List</h2>
	<?php
		if (isset($_SESSION['message']))
		{
			echo "<p>" . $_SESSION['message'] . "</p>";
			unset($_SESSION['message']);
		}
	?>
	<form method="get" action="index.php">
		<input type="text" name="title" id="title" />
		<input type="submit" name="Search" />
	</form>
	<?php
		require_once 'settings.php';
		$searchresults = array();
		if (isset($_GET['title']))
		{
			$title = $_GET['title'];

			if ($title)
			{
				//search
				$conn = new mysqli($host, $user, $pswd, $db);

				//Check if error with connection
	            if ($conn->connect_errno)
	            {
	                echo "<p>Failed to connect to database: " . $conn->connect_error . "</p>";
	                exit();
	            }

	            $tablequery = "CREATE TABLE IF NOT EXISTS mangas(
	            	record_id INT NOT NULL AUTO_INCREMENT,
	            	eng_name VARCHAR(100) NOT NULL,
	            	jp_name VARCHAR(100) NOT NULL,
	            	author VARCHAR(30) NOT NULL,
	            	run_start DATE NOT NULL,
	            	run_end DATE,
	            	read_state VARCHAR(20) NOT NULL,
	            	PRIMARY KEY (record_id)
	        	);";

	        	$conn->query($tablequery);

	        	$searchquery = "SELECT * FROM mangas WHERE eng_name LIKE '%{$title}%' OR jp_name LIKE '%{$title}%'";

	    		$searchresult = $conn->query($searchquery);

	    		if ($searchresult->num_rows > 0)
	    		{
	    			while($row = $searchresult->fetch_assoc())
	    			{
	    				$searchresults[] = $row;
	    			}
	    		}

	    		$conn->close();
			}
			else
			{
				echo "<p>Something wrong with search</p>";
			}
		}
		else
		{
			$conn = new mysqli($host, $user, $pswd, $db);

			//Check if error with connection
	        if ($conn->connect_errno)
	        {
	            echo "<p>Failed to connect to database: " . $conn->connect_error . "</p>";
	            exit();
	        }

			$tablequery = "CREATE TABLE IF NOT EXISTS mangas(
	            record_id INT NOT NULL AUTO_INCREMENT,
	            eng_name VARCHAR(100) NOT NULL,
	            jp_name VARCHAR(100) NOT NULL,
	            author VARCHAR(30) NOT NULL,
	            run_start DATE NOT NULL,
	            run_end DATE,
	            read_state VARCHAR(20) NOT NULL,
	            PRIMARY KEY (record_id)
	        );";

	        $conn->query($tablequery);

			$searchquery = "SELECT * FROM `mangas`";

			$searchresult = $conn->query($searchquery);

			if ($searchresult->num_rows > 0)
	    	{
	    		while($row = $searchresult->fetch_assoc())
	    		{
	    			$searchresults[] = $row;
	    			
	    		}
	    	}

			$conn->close();
		}

		if (sizeof($searchresults) > 0)
		{
			echo "<table>";
	    	echo "<tr><th>English Name</th><th>Japanese Name</th><th>Author</th><th>Original Run</th><th>Read Date</th><th>Current State</th></tr>";
	    	foreach ($searchresults as $result) {
				echo "<tr>";
		    	echo "<td>" . $result['eng_name'] . "</td>";
		    	echo "<td>" . $result['jp_name'] . "</td>";
		    	echo "<td>" . $result['author'] . "</td>";
		    	echo "<td>" . date("d/m/Y", strtotime($result['run_start'])) . " - " . date("d/m/Y", strtotime($result['run_end'])) . "</td>";
		    	echo "<td>" . $result['read_state'] . "</td>";
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