<?php
	session_start();
	if (isset($_POST['mal_id']) && isset($_POST['eng_name']) && isset($_POST['jp_name']) && isset($_POST['author']) && isset($_POST['run_start']) && isset($_POST['run_end']) && isset($_POST['status']))
	{
		require_once "settings.php";
		$conn = new mysqli($host, $user, $pswd, $db);

		$mal_id = $_POST['mal_id'];

		$searchquery = "SELECT * FROM mangas WHERE mal_id = $mal_id";
		$result = $conn->query($searchquery);

		if ($result->num_rows > 0)
		{
			$_SESSION['message'] = "Manga is already in your list.";
		}
		else
		{
			$eng_name = $_POST['eng_name'];
			$jp_name = $_POST['jp_name'];
			$author = $_POST['author'];
			$run_start = $_POST['run_start'];
			$run_end = $_POST['run_end'];
			$status = $_POST['status'];

			$read_state = "Reading";
			$insertquery = "INSERT INTO mangas (mal_id, eng_name, jp_name, author, run_start, run_end, read_state) VALUES
				($mal_id, '$eng_name', '$jp_name', '$author', '$run_start', '$run_end', '$read_state')";


			if ($conn->query($insertquery)=== TRUE)
			{
				$_SESSION['message'] = "Successfully added manga to list.";
			}
			else
			{
				$_SESSION['message'] = "Failed to add manga to list.";
			}
		}
		
		$conn->close();

		header("location: search.php");
	}
	else
	{
		echo "nah";
	}
?>