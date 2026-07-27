<?php
	session_start();
	if(!isset($_SESSION['manager']) || $_SESSION['manager'] != true){
		header("Location:index.php");
		exit;
	}

	require_once "./functions/database_functions.php";
	$conn = db_connect();

	if(!isset($_GET['bookisbn'])){
		header("Location: admin_book.php");
		exit;
	}

	$book_isbn = mysqli_real_escape_string($conn, $_GET['bookisbn']);

	$query = "DELETE FROM books WHERE book_isbn = '$book_isbn'";
	$result = mysqli_query($conn, $query);
	if(!$result){
		echo "delete data unsuccessfully " . mysqli_error($conn);
		exit;
	}
	if(isset($conn)) { mysqli_close($conn); }
	header("Location: admin_book.php?msg=deleted");
	exit;
?>
