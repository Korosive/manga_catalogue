<?php
	session_start();
	if (isset($_GET['id']))
	{
		$id = $_GET['id'];

		require_once "settings.php";

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

	    $removequery = "DELETE FROM mangas WHERE record_id = $id";

	    if ($conn->query($removequery) === TRUE)
	    {
	    	$_SESSION['message'] = "Successfully removed manga.";
	    }
	    else
	    {
	    	$_SESSION['message'] = "Failed to remove manga.";
	    }
	}
	else
	{
		$_SESSION['message'] = "No manga to remove.";
	}

	header("location: index.php");
?>