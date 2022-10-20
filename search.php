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
	<h1>Search Database</h1>
	<form method="get" action="search.php">
		<input type="text" name="title" id="title" />
		<input type="submit" name="Search" />
	</form>
	<?php
		/*
			API = https://docs.api.jikan.moe/#tag/manga/operation/getMangaSearch
			How to read APIs in PHP = https://tutorialsclass.com/php-rest-api-file_get_contents/
		*/
		if (isset($_SESSION['message']))
		{
			echo "<p>" . $_SESSION['message'] . "</p>";
			unset($_SESSION['message']);
		}

		$searchresults = array();
		if (isset($_GET['title']))
		{
			$title = $_GET['title'];
			$api_url = "https://api.jikan.moe/v4/manga?letter=$title";
			
		}
		else
		{
			$api_url = "https://api.jikan.moe/v4/manga";
		}

		$json_data = file_get_contents($api_url);
		$response_data = json_decode($json_data);
		$data = $response_data->data;
		if (sizeof($data) > 0)
		{
			foreach ($data as $d) {
				$searchresults[] = $d;
			}
		}

		if (sizeof($searchresults) > 0)
		{
			echo "<table>";
			echo "<tr>
				<th>Image</th>
	    		<th>English Name</th>
	    		<th>Japanese Name</th>
	    		<th>Author</th>
	    		<th>Original Run</th>
	    		<th>Status</th>
	    	</tr>";
			foreach ($searchresults as $result)
			{
				echo "<tr>";
				echo "<td><img src='" . $result->images->jpg->small_image_url . "' alt='Image of manga'/>";
				echo "<td>" . $result->title . "</td>";
				echo "<td>" . $result->title_japanese . "</td>";
				echo "<td>";

				foreach ($result->authors as $author) {
					echo $author->name;
				}

				echo "</td>";
				echo "<td>" . substr($result->published->from, 0, strpos($result->published->from, "T")) . " to ";
				if ($result->published->to != "")
				{
					echo substr($result->published->to, 0, strpos($result->published->to, "T"));
				}
				else
				{
					echo " Today";
				}
				
				echo "</td>";
				echo "<td>" . $result->status . "</td>";
				echo "</tr>";
			}
		}
		else
		{
			echo "<p>No search results.</p>";
		}
	?>
</body>
</html>