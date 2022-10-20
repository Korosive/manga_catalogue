<?php
	session_start();
	if (isset($_POST['add']))
	{
		if (isset($_POST['eng_name']) && isset($_POST['jp_name']) && isset($_POST['author']) && isset($_POST['run_start']) && 
			isset($_POST['run_end']) && isset($_POST['read_start']) && isset($_POST['read_end']) && isset($_POST['read_state']))
		{
			$eng_name = $_POST['eng_name'];
			$jp_name = $_POST['jp_name'];
			$author = $_POST['author'];
			$run_start = $_POST['run_start'];
			$run_end = $_POST['run_end'];
			$read_start = $_POST['read_start'];
			$read_end = $_POST['read_end'];
			$read_state = $_POST['read_state'];

			require_once 'settings.php';

			$conn = new mysqli($host, $user, $pswd, $db);

			$insertquery = "INSERT INTO mangas (eng_name, jp_name, author, run_start, run_end, read_start, read_end, read_state)
				VALUES ('$eng_name', '$jp_name', '$author', '$run_start', '$run_end', '$read_start', '$read_end', '$read_state')";

			echo "<p>[$insertquery]</p>";

			$insertresult = $conn->query($insertquery);

			if ($insertresult === TRUE)
			{
				$_SESSION['message'] = "Successfully added manga.";
			}
			else
			{
				$_SESSION['message'] = "Failed to add manga.";
			}

			$conn->close();

			header("location: index.php");
		}
		else
		{
			echo "not all inputs";
		}
	}
	else
	{
		echo "bruh";
		//header("location: index.php");
	}
?>