<?php
	session_start();
	if (isset($_POST['record_id']) && isset($_POST['status']))
	{
		$record_id = $_POST['record_id'];
		$status = $_POST['status'];

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

	    $updatestatus = "UPDATE mangas SET read_state = '$status' WHERE record_id = $record_id";
	    echo "[$updatestatus]";

	    if ($conn->query($updatestatus) == TRUE)
	    {
	    	$_SESSION['message'] = "Successfully updated status.";
	    }
	    else
	    {
	    	$_SESSION['message'] = "Failed to update status." . $conn->error;
	    }

	    $conn->close();

	    header("location:index.php");
	}
	else
	{
		header("location:index.php");
	}
?>