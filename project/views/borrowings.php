<?php
    require __DIR__."/../controllers/borrowings_controller.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Library App</title>
</head>
<body>
    <main class="borrowings-main">
        <h1 class="borrowings-heading">My Borrowings</h1>
        <div class="borrowings-list">
            <?php
                if(count($items) > 0) {
                    foreach($items as $item) {
                        echo "<div class='borrowing'>";
                        echo "<p class='borrowing-type'>Type: " . $item['type'] . "</p>";
                        echo "<p class='borrowing-title'>Title: " . $item['title'] . "</p>";
                        echo "<p class='borrowing-author'>Author: " . $item['author'] . "</p>";
                        echo "<p class='borrowing-genre'>Genre: " . $item['genre'] . "</p>";
                        echo "<p class='borrowing-quantity'>Due Date: " . $item['due_date'] . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='no-borrowings'>No borrowings found.</p>";
                }
            ?>
        </div>
    </main>
</body>
</html>