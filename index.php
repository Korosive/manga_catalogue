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
<body>
	<h1>Home Page</h1>
	<form method="get" action="index.php">
		<input type="text" name="title" id="title" />
		<br/>
		<input type="submit" name="Search" />
	</form>
	<?php
		require_once 'settings.php';
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
	            	read_start DATE NOT NULL,
	            	read_end DATE,
	            	read_state VARCHAR(20) NOT NULL,
	            	PRIMARY KEY (record_id)
	        	);";

	        	$conn->query($tablequery);

	        	$searchquery = "SELECT * FROM mangas WHERE eng_name LIKE '%{$title}%' OR jp_name LIKE '%{$title}%'";

	    		$searchresult = $conn->query($searchquery);

	    		if ($searchresult->num_rows > 0)
	    		{
	    			echo "<table>";
	    			echo "<tr>
	    				<th>English Name</th>
	    				<th>Japanese Name</th>
	    				<th>Author</th>
	    				<th>Original Run</th>
	    				<th>Read Date</th>
	    				<th>Current State</th>
	    			</tr>";
	    			while($row = $searchresult->fetch_assoc())
	    			{
	    				echo "<tr>";
	    				echo "<td>" . $row['eng_name'] . "</td>";
	    				echo "<td>" . $row['jp_name'] . "</td>";
	    				echo "<td>" . $row['author'] . "</td>";
	    				echo "<td>" . $row['run_start'] . " - " . $row['run_end'] . "</td>";
	    				echo "<td>" . $row['read_start'] . " - " . $row['read_end'] . "</td>";
	    				echo "<td>" . $row['read_state'] . "</td>";
	    				echo "</tr>";
	    			}
	    			echo "</table>";
	    		}
	    		else
	    		{
	    			echo "<p>No results with that search</p>";
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

			$tablequery = "CREATE TABLE IF NOT EXISTS mangas(
	            record_id INT NOT NULL AUTO_INCREMENT,
	            eng_name VARCHAR(100) NOT NULL,
	            jp_name VARCHAR(100) NOT NULL,
	            author VARCHAR(30) NOT NULL,
	            run_start DATE NOT NULL,
	            run_end DATE,
	            read_start DATE NOT NULL,
	            read_end DATE,
	            read_state VARCHAR(20) NOT NULL,
	            PRIMARY KEY (record_id)
	        );";

	        $conn->query($tablequery);

			$searchquery = "SELECT * FROM `mangas`";

			$searchresult = $conn->query($searchquery);

			if ($searchresult->num_rows > 0)
	    	{
	    		echo "<table>";
	    		echo "<tr><th>English Name</th><th>Japanese Name</th><th>Author</th><th>Original Run</th><th>Read Date</th><th>Current State</th></tr>";
	    		while($row = $searchresult->fetch_assoc())
	    		{
	    			echo "<tr>";
	    			echo "<td>" . $row['eng_name'] . "</td>";
	    			echo "<td>" . $row['jp_name'] . "</td>";
	    			echo "<td>" . $row['author'] . "</td>";
	    			echo "<td>" . $row['run_start'] . " - " . $row['run_end'] . "</td>";
	    			echo "<td>" . $row['read_start'] . " - " . $row['read_end'] . "</td>";
	    			echo "<td>" . $row['read_state'] . "</td>";
	    			echo "</tr>";
	    		}
	    		echo "</table>";
	    	}
	    	else
	    	{
	    		echo "<p>No manga to display</p>";
	    	}

			$conn->close();
		}
	?>

	<form method="POST" action="add_manga.php">
		<label for="eng_name">English Name: </label>
		<input type="text" name="eng_name" id="eng_name" />
		<br/>
		<label for="jp_name">Japanese Name: </label>
		<input type="text" name="jp_name" id="jp_name" />
		<br/>
		<label for="author">Author: </label>
		<input type="text" name="author" id="author" />
		<br/>
		<label for="run_start">Run Start: </label>
		<input type="date" name="run_start" id="run_start" />
		<br/>
		<label for="run_end">Run End: </label>
		<input type="date" name="run_end" id="run_end" />
		<br/>
		<label for="read_start">Read Start: </label>
		<input type="date" name="read_start" id="read_start" />
		<br/>
		<label for="read_end">Read End: </label>
		<input type="date" name="read_end" id="read_end" />
		<br/>
		<label for="read_state">Read State: </label>
		<select id="read_state" name="read_state">
			<option value="reading">Reading</option>
			<option value="stopped">Stopped</option>
			<option value="finished">Finished</option>
		</select>
		<br/>
		<input type="submit" name="add" />
	</form>
</body>
</html>