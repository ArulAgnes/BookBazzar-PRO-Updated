<?php
	if (!function_exists("db_connect")){

		function db_connect(){
			$conn = mysqli_connect("localhost", "root", "", "bookbazzar");
			if(!$conn){
				echo "Can't connect database " . mysqli_connect_error();
				exit;
			}
			return $conn;
		}
	}
	if (!function_exists("select4LatestBook")){
	function select4LatestBook($conn){
		$row = array();
		$query = "SELECT book_isbn, book_image FROM books ORDER BY book_isbn DESC";
		$result = mysqli_query($conn, $query);
		if(!$result){
		    echo "Can't retrieve data " . mysqli_error($conn);
		    exit;
		}
		for($i = 0; $i < 4; $i++){
			array_push($row, mysqli_fetch_assoc($result));
		}
		return $row;
	}
}
if (!function_exists("getBookByIsbn")){
	function getBookByIsbn($conn, $isbn){
		$query = "SELECT book_title, book_author, book_price FROM books WHERE book_isbn = '$isbn'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		return $result;
	}
}
if (!function_exists("getCartId")){
	function getCartId($conn, $customerid){
		$query = "SELECT id FROM cart WHERE customerid = '$customerid'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "retrieve data failed!" . mysqli_error($conn);
			exit;
		}
		$row = mysqli_fetch_assoc($result);
		return $row['id'];
	}
}

if (!function_exists("insertIntoCart")){
	function insertIntoCart($conn, $customerid,$date){
		$query = "INSERT INTO cart(customerid) VALUES('$customerid')";

		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Insert Cart failed " . mysqli_error($conn);
			exit;
		}
	}
}
if (!function_exists("getbookprice")){
	function getbookprice($isbn){
		$conn = db_connect();
		$query = "SELECT book_price FROM books WHERE book_isbn = '$isbn'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "get book price failed! " . mysqli_error($conn);
			exit;
		}
		$row = mysqli_fetch_assoc($result);
		return $row['book_price'];
	}
}
if (!function_exists("getCustomerId")){
	function getCustomerId($name, $address, $city, $zip_code, $country){
		$conn = db_connect();
		$query = "SELECT customerid from customers WHERE 
		name = '$name' AND 
		address= '$address' AND 
		city = '$city' AND 
		zip_code = '$zip_code' AND 
		country = '$country'";
		$result = mysqli_query($conn, $query);
		// if there is customer in db, take it out
		if($result){
			$row = mysqli_fetch_assoc($result);
			return $row['customerid'];
		} else {
			return null;
		}
	}
}
if (!function_exists("getCustomerIdbyEmail")){
	function getCustomerIdbyEmail($email){
		$conn = db_connect();
		$query = "SELECT * from customers WHERE 
		email = '$email'";
		$result = mysqli_query($conn, $query);
		// if there is customer in db, take it out
		if($result){
			$row = mysqli_fetch_assoc($result);
			return $row;
		} else {
			return null;
		}
	}
}

if (!function_exists("getPubName")){
	function getPubName($conn, $pubid){
		$query = "SELECT publisher_name FROM publisher WHERE publisherid = '$pubid'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		if(mysqli_num_rows($result) == 0){
			return "Not Set";
		}

		$row = mysqli_fetch_assoc($result);
		return $row["publisher_name"];
	}
}

if (!function_exists("getCatName")){
	function getCatName($conn, $catid){
		$query = "SELECT category_name FROM category WHERE categoryid = '$catid'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		if(mysqli_num_rows($result) == 0){
			return "Not Set";
		}

		$row = mysqli_fetch_assoc($result);
		return $row['category_name'];
	}
}

if (!function_exists("getAll")){
	function getAll($conn){
		$query = "SELECT * from books ORDER BY book_isbn DESC";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		return $result;
	}
}
if (!function_exists("getAllPublishers")){
	function getAllPublishers($conn){
		$query = "SELECT * from publisher ORDER BY publisher_name ASC";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		return $result;
	}
}
if (!function_exists("getAllCategories")){
	function getAllCategories($conn){
		$query = "SELECT * from category ORDER BY category_name ASC";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		return $result;
	} 
}

/* ---------------------------------------------------------------------
   Added for the user Accept / Reject submission workflow
   --------------------------------------------------------------------- */

// Make sure the submissions table exists even if donation.php hasn't run yet
if (!function_exists("ensureSubmissionsTable")){
	function ensureSubmissionsTable($conn){
		$query = "CREATE TABLE IF NOT EXISTS book_submissions (
			id INT AUTO_INCREMENT PRIMARY KEY,
			isbn VARCHAR(255) NOT NULL,
			type VARCHAR(50) NOT NULL,
			title VARCHAR(255) NOT NULL,
			author VARCHAR(255) NOT NULL,
			image VARCHAR(255),
			descr TEXT,
			price DECIMAL(6,2) DEFAULT 0.00,
			publisher VARCHAR(255),
			category VARCHAR(255),
			gmail VARCHAR(255),
			phone VARCHAR(20),
			status VARCHAR(20) NOT NULL DEFAULT 'Pending',
			submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)";
		mysqli_query($conn, $query);
	}
}

// All submissions still waiting for a decision, newest first
if (!function_exists("getPendingSubmissions")){
	function getPendingSubmissions($conn){
		ensureSubmissionsTable($conn);
		$query = "SELECT * FROM book_submissions WHERE status = 'Pending' ORDER BY submitted_at DESC";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve submissions " . mysqli_error($conn);
			exit;
		}
		return $result;
	}
}

if (!function_exists("getSubmissionById")){
	function getSubmissionById($conn, $id){
		ensureSubmissionsTable($conn);
		$id = intval($id);
		$query = "SELECT * FROM book_submissions WHERE id = '$id'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve submission " . mysqli_error($conn);
			exit;
		}
		return mysqli_fetch_assoc($result);
	}
}

// Find a publisher by name, creating it if it doesn't exist yet, return its id
if (!function_exists("findOrCreatePublisherId")){
	function findOrCreatePublisherId($conn, $publisherName){
		$publisherName = mysqli_real_escape_string($conn, trim($publisherName));
		if($publisherName === ""){ $publisherName = "Not Set"; }
		$findResult = mysqli_query($conn, "SELECT publisherid FROM publisher WHERE publisher_name = '$publisherName'");
		if($findResult && mysqli_num_rows($findResult) > 0){
			$row = mysqli_fetch_assoc($findResult);
			return $row['publisherid'];
		}
		mysqli_query($conn, "INSERT INTO publisher(publisher_name) VALUES ('$publisherName')");
		return mysqli_insert_id($conn);
	}
}

// Find a category by name, creating it if it doesn't exist yet, return its id
if (!function_exists("findOrCreateCategoryId")){
	function findOrCreateCategoryId($conn, $categoryName){
		$categoryName = mysqli_real_escape_string($conn, trim($categoryName));
		if($categoryName === ""){ $categoryName = "Not Set"; }
		$findResult = mysqli_query($conn, "SELECT categoryid FROM category WHERE category_name = '$categoryName'");
		if($findResult && mysqli_num_rows($findResult) > 0){
			$row = mysqli_fetch_assoc($findResult);
			return $row['categoryid'];
		}
		mysqli_query($conn, "INSERT INTO category(category_name) VALUES ('$categoryName')");
		return mysqli_insert_id($conn);
	}
}
?>