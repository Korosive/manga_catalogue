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
		if (isset($_GET['page']))
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 1;
			//header("location: search.php?page=1");
		}

		if (isset($_GET['title']))
		{
			$title = $_GET['title'];
			if (strpos($title, " ") !== FALSE)
			{
				$title = str_replace(" ", "+", $title);
			}
			$api_url = "https://api.jikan.moe/v4/manga?letter=$title&limit=10&page=$page";	
		}
		else
		{
			$api_url = "https://api.jikan.moe/v4/manga?limit=10&page=$page";
		}

		if (isset($_SESSION['message']))
		{
			echo "<p>" . $_SESSION['message'] . "</p>";
			unset($_SESSION['message']);
		}
	?>
	<h1>Search Database</h1>
	<form method="get" action="search.php">
		<?php
			if (isset($title))
			{
				$inputtitle = "";
				if (strpos($title, "+"))
				{
					$inputtitle = str_replace("+", " ", $title);
				}
				else
				{
					$inputtitle = $title;
				}
				echo "<input type='text' name='title' id='title' value='$inputtitle' />";
			}
			else
			{
				echo "<input type='text' name='title' id='title' />";
			}
		?>
		<input type="submit" name="Search" value="Search" />
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
				echo "<td>";

				if ($result->title != "")
				{
					echo $result->title;
				}
				else
				{
					echo "-";
				}

				echo "</td>";

				echo "<td>";
				if ($result->title_japanese != "")
				{
					echo $result->title_japanese;
				}
				else
				{
					echo "-";
				}
				echo "</td>";

				echo "<td>";
				$authors = "";
				if (count($result->authors) > 1) 
				{
					for ($i=0; $i < count($result->authors); $i++) 
					{ 
						if ($i == count($result->authors) - 1)
						{
							$authors .= $result->authors[$i]->name;
						}
						else
						{
							$authors .= $result->authors[$i]->name . " & ";
						}
					}
				}
				elseif (count($result->authors) == 1) 
				{
					$authors = $result->authors[0]->name;
				}
				else
				{
					$authors = "-";
				}
				echo $authors;

				echo "</td>";
				echo "<td>";
				if ($result->published->from != "")
				{
					$run_start = substr($result->published->from, 0, strpos($result->published->from, "T"));
					echo $run_start;
				}
				else
				{
					echo "?";
					$run_start = NULL;
				}
				echo " to ";
				if ($result->published->to != "")
				{
					
					$run_end = substr($result->published->to, 0, strpos($result->published->to, "T"));
					echo $run_end;
				}
				else
				{
					echo "?";
					$run_end = NULL;
				}

				$mal_id = $result->mal_id;
				
				echo "</td>";
				echo "<td>" . $result->status . "</td>";
				echo "<td>";
				echo "<form method='POST' action='add_manga.php'>";
				echo "<input type='hidden' name='mal_id' id='mal_id' value='" . $result->mal_id . "'/>";
				echo "<input type='hidden' name='eng_name' id='eng_name' value='" . $result->title . "'/>";
				echo "<input type='hidden' name='jp_name' id='jp_name' value='" . $result->title_japanese . "'/>";
				echo "<input type='hidden' name='author' id='author' value='" . $authors . "'/>";
				echo "<input type='hidden' name='run_start' id='run_start' value='" . $run_start . "'/>";
				echo "<input type='hidden' name='run_end' id='run_end' value='" . $run_end . "'/>";
				echo "<select name='status' id='status'>";
		    	echo "<option value='Reading'>Reading</option>";
		    	echo "<option value='Completed'>Completed</option>";
		    	echo "<option value='On-Hold'>On-Hold</option>";
		    	echo "<option value='Dropped'>Dropped</option>";
		    	echo "<option value='Planned To Read'>Planned To Read</option>";
		    	echo "</select>";
		    	echo "<br/>";
				echo "<input type='submit' value='Add To List'/>";
				echo "</form>";
				echo "</td>";
				echo "</tr>";
			}
		}
		else
		{
			echo "<p>No search results.</p>";
		}

		$pagination = $response_data->pagination;

		if ($page > 1)
		{
			if (isset($title) && $title != "")
			{
				echo "<a href='search.php?title=$title&page=" . ($page - 1) . "'>Previous Page</a>";
			}
			else
			{
				echo "<a href='search.php?page=" . ($page - 1) . "'>Previous Page</a>";
			}
		}

		if ($pagination->has_next_page == TRUE && $page < $pagination->items->total)
		{
			if (isset($title) && $title != "")
			{
				echo "<a href='search.php?title=$title&page=" . ($page + 1) . "'>Next Page</a>";
			}
			else
			{
				echo "<a href='search.php?page=" . ($page + 1) . "'>Next Page</a>";
			}
		}
	?>
</body>
</html>