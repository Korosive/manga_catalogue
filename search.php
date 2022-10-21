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
	<title>Search Database Page</title>
</head>
<?php
	include "nav.php";
?>
<body>
	<?php
		if (isset($_SESSION['message']))
		{
			echo "<p>" . $_SESSION['message'] . "</p>";
			unset($_SESSION['message']);
		}
	?>
	<h1>Search Database</h1>
	<form method="get" action="search.php">
		<input type="text" name="title" id="title" />
		<input type="submit" name="Search" />
	</form>
	<?php
		if (isset($_GET['page']))
		{
			$page = $_GET['page'];
		}
		else
		{
			header("location: search.php?page=1");
		}
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
			$api_url = "https://api.jikan.moe/v4/manga?letter=$title&limit=10&page=$page";
			
		}
		else
		{
			$api_url = "https://api.jikan.moe/v4/manga?limit=10&page=$page";
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
	    		<th>Add To List</th>
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
				echo "<td><a href='add_manga.php?mal_id=" . $result->mal_id . "'>Add To List</a></td>";
				echo "</tr>";
			}
		}
		else
		{
			echo "<p>No search results.</p>";
		}

		if ($page > 1)
		{
			echo "<a href='search.php?page=" . ($page - 1) . "'>Previous Page</a>";
		}

		echo "<a href='search.php?page=" . ($page + 1) . "'>Next Page</a>";
	?>
</body>
</html>