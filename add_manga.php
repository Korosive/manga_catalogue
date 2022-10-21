<?php
	session_start();
	if (isset($_GET['mal_id']))
	{
		$mal_id = $_GET['mal_id'];
		$api_url = "https://api.jikan.moe/v4/manga/$mal_id";
		$json_data = file_get_contents($api_url);
		$response_data = json_decode($json_data);
		$data = $response_data->data;

		require_once "settings.php";

		$conn = new mysqli($host, $user, $pswd, $db);

		$eng_name = $data->title;
		$jp_name = $data->title_japanese;
		foreach ($data->authors as $author) {
			$author = $author->name;
		}
		$run_start = date("Y-m-d", strtotime(substr($data->published->from, 0, strpos($data->published->from, "T"))));

		if (substr($data->published->to, 0, strpos($data->published->to, "T")) != "")
		{
			$run_end = date("Y-m-d", strtotime(substr($data->published->to, 0, strpos($data->published->to, "T"))));
		}
		else
		{
			$run_end = NULL;
		}
			
		$read_start = date("Y-m-d");
		$read_state = "reading";
		$insertquery = "INSERT INTO mangas (eng_name, jp_name, author, run_start, run_end, read_state) VALUES
			('$eng_name', '$jp_name', '$author', '$run_start', '$run_end', '$read_state')";


		if ($conn->query($insertquery)=== TRUE)
		{
			$_SESSION['message'] = "Successfully added manga to list.";
		}
		else
		{
			$_SESSION['message'] = "Failed to add manga to list.";
		}

		$conn->close();

		header("location: search.php");
	}
	else
	{
		echo "nah";
	}
?>