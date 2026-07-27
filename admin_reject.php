<?php
	// Rejects a pending book submission. It stays in book_submissions with
	// status 'Rejected' (for record keeping) but is removed from the
	// pending queue and never touches the live `books` table.
	session_start();
	if((!isset($_SESSION['manager']) && !isset($_SESSION['expert']))){
		header("Location:index.php");
		exit;
	}

	require_once "./functions/database_functions.php";
	$conn = db_connect();
	ensureSubmissionsTable($conn);

	if(!isset($_GET['id'])){
		header("Location: admin_book.php");
		exit;
	}

	$id = intval($_GET['id']);
	mysqli_query($conn, "UPDATE book_submissions SET status = 'Rejected' WHERE id = '$id'");

	if(isset($conn)) { mysqli_close($conn); }
	header("Location: admin_book.php?msg=rejected");
	exit;
?>
