<?php
require_once "./functions/database_functions.php";

// Only show books that an admin has actually accepted onto the live
// `books` table (type = 'Donating') - not raw, unreviewed submissions.
$conn = db_connect();
$query = "SELECT book_title, book_author, publisherid, categoryid FROM books WHERE type = 'Donating'";
$result = mysqli_query($conn, $query);

$bookList = "";
if ($result) {
    while ($book = mysqli_fetch_assoc($result)) {
        $bookList .= '<div class="book-entry">';
        $bookList .= '<strong>' . htmlspecialchars($book['book_title']) . '</strong> <span>&rarr;</span> ';
        $bookList .= htmlspecialchars($book['book_author']) . ' <span>&rarr;</span> ';
        $bookList .= getPubName($conn, $book['publisherid']) . ' <span>&rarr;</span> ';
        $bookList .= getCatName($conn, $book['categoryid']);
        $bookList .= '</div>';
    }
}
if ($bookList === "") {
    $bookList = '<p style="color:#8a8477">No donated books available right now.</p>';
}

if (isset($conn)) {
    mysqli_close($conn);
}
?>
    </div><!-- /#main (opened in template/header.php) -->

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-col">
                <h4>BookBazzar</h4>
                <p>A classic library with a modern, sci-fi soul &mdash; buy, sell, donate and discover books.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul style="list-style:none; padding:0;">
                    <li><a href="books.php">Browse Books</a></li>
                    <li><a href="donation.php">Donate / Resell a Book</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Donating Books <small>(contact Agram Charity: +91 1234567890)</small></h4>
                <div class="footer-book-list"><?php echo $bookList; ?></div>
            </div>
        </div>
        <div class="footer-bottom">&copy; <?php echo date("Y"); ?> BookBazzar. All rights reserved.</div>
    </footer>

    <script src="./bootstrap/js/jquery-2.1.4.min.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>
    <script src="./bootstrap/js/theme-effects.js"></script>
  </body>
</html>
