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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

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
	<h1 class="text-center border-bottom">Search Database</h1>
	<form class="row row-cols-lg-auto g-3 align-items-center justify-content-center" method="get" action="search.php">
		<?php
			echo "<div class='col-12'>";
			echo "<div class='input-group'>";
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
				echo "<input class='form-control' type='text' name='title' id='title' value='$inputtitle' />";
			}
			else
			{
				echo "<input class='form-control' type='text' name='title' id='title' />";
			}
			echo "</div>";
			echo "</div>";

			echo "<div class='col-12'>";
			echo "<input class='btn btn-primary' type='submit' name='Search' value='Search' />";
			echo "</div>";
		?>
		
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
			foreach ($data as $d) 
			{
				$searchresults[] = $d;
			}
		}

		if (sizeof($searchresults) > 0)
		{
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

			echo "<table class='table table-striped table-hover table-bordered'>";
			echo "<tr><th>Image</th><th>English Name</th><th>Japanese Name</th><th>Author</th><th>Original Run</th><th>Status</th><th>Add To List</th></tr>";
			foreach ($searchresults as $result)
			{
				echo "<tr>";
				echo "<td><img src='" . $result->images->jpg->small_image_url . "' alt='Image of manga'/>";

				echo($result->title != "" ? "<td>" . $result->title . "</td>" : "-");

				$jp_title = "";
				//echo "<p>" . $result->title_japanese . "</p>";
				if ($result->title_japanese == "" || $result->title_japanese == NULL)
				{
					echo "<td>-</td>";
					$jp_title = "-";
				}
				else
				{
					echo "<td>" . $result->title_japanese . "</td>";
					$jp_title = $result->title_japanese;
				}

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

				$check = "SELECT * FROM mangas WHERE mal_id = " . $result->mal_id;

				$checkresult = $conn->query($check);
				if ($checkresult->num_rows > 0)
				{
					echo "<td>Already in list</td>";
				}
				else
				{
					echo "<td>";
					echo "<form class='row row-cols-lg-auto g-3' method='POST' action='add_manga.php'>";
					echo "<input type='hidden' name='mal_id' id='mal_id' value='" . $result->mal_id . "'/>";
					echo "<input type='hidden' name='eng_name' id='eng_name' value='" . $result->title . "'/>";
					echo "<input type='hidden' name='jp_name' id='jp_name' value='" . $jp_title . "'/>";
					echo "<input type='hidden' name='author' id='author' value='" . $authors . "'/>";
					echo "<input type='hidden' name='run_start' id='run_start' value='" . $run_start . "'/>";
					echo "<input type='hidden' name='run_end' id='run_end' value='" . $run_end . "'/>";
					echo "<div class='col-12'>";
		    		echo "<div class='input-group'>";
					echo "<select class='form-select' name='status' id='status'>";
			    	echo "<option value='Reading'>Reading</option>";
			    	echo "<option value='Completed'>Completed</option>";
			    	echo "<option value='On-Hold'>On-Hold</option>";
			    	echo "<option value='Dropped'>Dropped</option>";
			    	echo "<option value='Planned To Read'>Planned To Read</option>";
			    	echo "</select>";
			    	echo "</div>";
		    		echo "</div>";
		    		echo "<div class='col-12'>";
					echo "<input class='btn btn-primary' type='submit' value='Add To List'/>";
					echo "</div>";
					echo "</form>";
					echo "</td>";
				}
				echo "</tr>";
			}
		}
		else
		{
			echo "<p>No search results.</p>";
		}

		$pagination = $response_data->pagination;

		echo "<nav aria-label='Page pagination'>";
		echo "<ul class='pagination justify-content-center'>";

		if ($page > 1)
		{
			if (isset($title) && $title != "")
			{
				echo "<li class='page-item'>";
				echo "<a class='page-link' href='search.php?title=$title&page=" . ($page - 1) . "'>Previous</a>";
				echo "</li>";
			}
			else
			{
				echo "<li class='page-item'>";
				echo "<a class='page-link' href='search.php?page=" . ($page - 1) . "'>Previous</a>";
				echo "</li>";
			}
		}
		else
		{
			echo "<li class='page-item disabled'>";
			echo "<a class='page-link' href='#'>Previous</a>";
			echo "</li>";
		}

		if ($pagination->has_next_page == TRUE && $page < $pagination->items->total)
		{
			if (isset($title) && $title != "")
			{
				echo "<li class='page-item'>";
				echo "<a class='page-link' href='search.php?title=$title&page=" . ($page + 1) . "'>Next Page</a>";
				echo "</li>";
			}
			else
			{
				echo "<li class='page-item'>";
				echo "<a class='page-link' href='search.php?page=" . ($page + 1) . "'>Next Page</a>";
				echo "</li>";
			}
		}
		else
		{
			echo "<li class='page-item disabled'>";
			echo "<a class='page-link' href='#'>Next</a>";
			echo "</li>";
		}

		echo "</ul>";
		echo "</nav>";
	?>
</body>
</html>