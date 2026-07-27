<?php
	// Accepts a pending book submission: copies it into the live `books`
	// table (so it instantly shows up on books.php / index.php / book.php /
	// cart etc. for every user) and marks the submission as 'Accepted'.
	session_start();
	if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))){
		header("Location:index.php");
		exit;
	}

	require_once "./functions/database_functions.php";
	$conn = db_connect();

	if(!isset($_GET['id'])){
		header("Location: admin_book.php");
		exit;
	}

	$sub = getSubmissionById($conn, $_GET['id']);
	if(!$sub){
		header("Location: admin_book.php?msg=notfound");
		exit;
	}

	$isbn      = mysqli_real_escape_string($conn, $sub['isbn']);
	$type      = mysqli_real_escape_string($conn, $sub['type']);
	$bookTitle = mysqli_real_escape_string($conn, $sub['title']);
	$author    = mysqli_real_escape_string($conn, $sub['author']);
	$image     = mysqli_real_escape_string($conn, $sub['image']);
	$descr     = mysqli_real_escape_string($conn, $sub['descr']);
	$price     = floatval($sub['price']);
	$gmail     = mysqli_real_escape_string($conn, $sub['gmail']);
	$phone     = mysqli_real_escape_string($conn, $sub['phone']);

	$publisherid = findOrCreatePublisherId($conn, $sub['publisher']);
	$categoryid  = findOrCreateCategoryId($conn, $sub['category']);

	// If that ISBN is already a live book, update it instead of failing on
	// the duplicate primary key.
	$exists = mysqli_query($conn, "SELECT book_isbn FROM books WHERE book_isbn = '$isbn'");
	if($exists && mysqli_num_rows($exists) > 0){
		$query = "UPDATE books SET
			type = '$type',
			book_title = '$bookTitle',
			book_author = '$author',
			book_image = '$image',
			book_descr = '$descr',
			book_price = '$price',
			publisherid = '$publisherid',
			categoryid = '$categoryid',
			seller_email = '$gmail',
			seller_phone = '$phone'
			WHERE book_isbn = '$isbn'";
	} else {
		$query = "INSERT INTO books
			(book_isbn, type, book_title, book_author, book_image, book_descr, book_price, publisherid, categoryid, seller_email, seller_phone)
			VALUES ('$isbn', '$type', '$bookTitle', '$author', '$image', '$descr', '$price', '$publisherid', '$categoryid', '$gmail', '$phone')";
	}

	$result = mysqli_query($conn, $query);
	if(!$result){
		header("Location: admin_book.php?msg=accepterror");
		exit;
	}

	$id = intval($_GET['id']);
	mysqli_query($conn, "UPDATE book_submissions SET status = 'Accepted' WHERE id = '$id'");

	if(isset($conn)) { mysqli_close($conn); }
	header("Location: admin_book.php?msg=accepted");
	exit;
?>
