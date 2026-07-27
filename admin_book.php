<?php
	session_start();
	if((!isset($_SESSION['manager'])  && !isset($_SESSION['expert']))){
		header("Location:index.php");
		exit;
	}
	$title = "List book";
	require_once "./template/header.php";
	require_once "./functions/database_functions.php";
	$conn = db_connect();
	$result = getAll($conn);
	$pending = getPendingSubmissions($conn);
	$pendingCount = mysqli_num_rows($pending);
?>
	<div class="admin-toolbar">
		<a href="admin_signout.php" class="btn btn-danger"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a>
		<a href="admin_publishers.php" class="btn btn-primary"><span class="glyphicon glyphicon-paperclip"></span>&nbsp;Publishers</a>
		<a href="admin_categories.php" class="btn btn-primary"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Categories</a>
	<?php
	if (isset($_SESSION['manager']) && $_SESSION['manager']==true){
		echo '<a class="btn btn-primary" href="admin_add.php"><span class="glyphicon glyphicon-plus"></span>&nbsp;Add Book</a>';
	}
	?>
	</div>

	<?php
		if(isset($_GET['msg'])){
			$msg = $_GET['msg'];
			$messages = array(
				'accepted'    => array('success', 'Book accepted! It is now live for users.'),
				'rejected'    => array('info', 'Submission rejected.'),
				'deleted'     => array('info', 'Book removed from the catalog.'),
				'accepterror' => array('danger', "Couldn't accept that submission, please try again."),
				'notfound'    => array('danger', 'That submission could not be found (maybe already handled).'),
			);
			if(isset($messages[$msg])){
				echo '<div class="alert alert-' . $messages[$msg][0] . '">' . $messages[$msg][1] . '</div>';
			}
		}
	?>

	<h3 class="section-heading">Pending Submissions <span class="badge-count"><?php echo $pendingCount; ?></span></h3>
	<p class="text-muted">Books users uploaded via the "Upload" (donation/resale) page. Accept to publish them for everyone, or Reject to hide them.</p>

	<?php if($pendingCount == 0){ ?>
		<div class="empty-state">Nothing waiting for review right now.</div>
	<?php } else { ?>
	<div class="table-responsive">
	<table class="table admin-table">
		<tr>
			<th>Image</th>
			<th>ISBN</th>
			<th>Title</th>
			<th>Author</th>
			<th>Type</th>
			<th>Price</th>
			<th>Publisher</th>
			<th>Category</th>
			<th>Email</th>
			<th>Phone</th>
			<th>&nbsp;</th>
		</tr>
		<?php while($row = mysqli_fetch_assoc($pending)){ ?>
		<tr>
			<td><?php if(!empty($row['image'])){ ?><img class="admin-thumb" src="./bootstrap/img/<?php echo htmlspecialchars($row['image']); ?>"><?php } ?></td>
			<td><?php echo htmlspecialchars($row['isbn']); ?></td>
			<td><?php echo htmlspecialchars($row['title']); ?></td>
			<td><?php echo htmlspecialchars($row['author']); ?></td>
			<td><span class="type-badge type-<?php echo strtolower(htmlspecialchars($row['type'])); ?>"><?php echo htmlspecialchars($row['type']); ?></span></td>
			<td><?php echo htmlspecialchars($row['price']); ?></td>
			<td><?php echo htmlspecialchars($row['publisher']); ?></td>
			<td><?php echo htmlspecialchars($row['category']); ?></td>
			<td><?php echo htmlspecialchars($row['gmail']); ?></td>
			<td><?php echo htmlspecialchars($row['phone']); ?></td>
			<td class="action-cell">
				<a href="admin_accept.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Accept this book and publish it for users?');"><span class="glyphicon glyphicon-ok"></span> Accept</a>
				<a href="admin_reject.php?id=<?php echo $row['id']; ?>" class="btn btn-default btn-sm" onclick="return confirm('Reject this submission?');"><span class="glyphicon glyphicon-remove"></span> Reject</a>
			</td>
		</tr>
		<?php } ?>
	</table>
	</div>
	<?php } ?>

	<h3 class="section-heading" style="margin-top:40px">Book Catalog</h3>
	<div class="table-responsive">
	<table class="table admin-table">
		<tr>
			<th>ISBN</th>
			<th>Title</th>
			<th>Author</th>
			<th>Image</th>
			<th>Type</th>
			<th>Price</th>
			<th>Publisher</th>
			<th>Category</th>
			<th>Email</th>
			<th>Phone</th>
			<th>&nbsp;</th>
		</tr>
		<?php while($row = mysqli_fetch_assoc($result)){ ?>
		<tr>
			<td><?php echo htmlspecialchars($row['book_isbn']); ?></td>
			<td><?php echo htmlspecialchars($row['book_title']); ?></td>
			<td><?php echo htmlspecialchars($row['book_author']); ?></td>
			<td><?php echo htmlspecialchars($row['book_image']); ?></td>
			<td><?php echo htmlspecialchars($row['type']); ?></td>
			<td><?php echo htmlspecialchars($row['book_price']); ?></td>
			<td><?php echo getPubName($conn, $row['publisherid']); ?></td>
			<td><?php echo getCatName($conn, $row['categoryid']); ?></td>
			<td><?php echo htmlspecialchars(isset($row['seller_email']) ? $row['seller_email'] : ''); ?></td>
			<td><?php echo htmlspecialchars(isset($row['seller_phone']) ? $row['seller_phone'] : ''); ?></td>
			<td class="action-cell">
				<?php
					// Manager: can Edit and Delete. Expert: can Edit only.
					if (isset($_SESSION['manager']) && $_SESSION['manager']==true){
						echo '<a href="admin_edit.php?bookisbn=' . urlencode($row['book_isbn']) . '" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span> Edit</a> ';
						echo '<a href="admin_delete.php?bookisbn=' . urlencode($row['book_isbn']) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this book permanently?\');"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
					} else if (isset($_SESSION['expert']) && $_SESSION['expert']==true){
						echo '<a href="admin_edit.php?bookisbn=' . urlencode($row['book_isbn']) . '" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span> Edit</a>';
					}
				?>
			</td>
		</tr>
		<?php } ?>
	</table>
	</div>

<?php
	if(isset($conn)) {mysqli_close($conn);}
	require_once "./template/footer.php";
?>
