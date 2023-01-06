<?php
    require __DIR__."/../controllers/items_controller.php";
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
    <main class="search-main">
        <h1 class="search-heading">Search</h1>
        <form class="search-form" method="get">

            <select class="type-select" name="type">
                <option value="book">Book</option>
            </select>
            <input type="text" name="title" placeholder="Title" />
            <input type="text" name="author" placeholder="Author" />
            <input type="text" name="genre" placeholder="Genre" />
            <input type="submit" value="Submit" class="submit" />    
        </form>
        <?php 
        if(isset($_SESSION["roles"]) && in_array("admin", $_SESSION["roles"])) {
            echo "<button class='add-item-button'>Add New Item</button>";
        }
        ?>
        <div class='add-item-form-wrapper' style="display: <?php echo empty($validation_err) && empty($validation_succ) ? 'none' : 'block';?>">
            <form class='add-item-form' method="post">
                <h2 class="add-item-heading">Add Item</h2>
                <?php
                    if(!empty($validation_err)) {
                        echo("<div class='error'><p>". $validation_err."</p></div>");
                    } elseif (!empty($validation_succ)) {
                        echo("<div class='succ'><p>". $validation_succ."</p></div>");
                    }
                ?>               
                <select class="type-select" name="type">
                    <option value="book">Book</option>
                </select>
                <input type="text" name="title" placeholder="Title" required />
                <input type="text" name="author" placeholder="Author" required />
                <input type="text" name="genre" placeholder="Genre" required />
                <input type="number" name="quantity" placeholder="Quantity" required />
                <input type='hidden' name='csrf_token' value='<?php echo $_SESSION['csrf_token']; ?>' />
                <input type="submit" value="Submit" class="submit" />

            </form>
        </div>
        <div class="items-list">
            <?php 
                foreach($items as $item) {
                    echo "<div class='item'>";

                    echo "<div class='item-display-wrapper'>";
                    echo "<p class='item-title'>Title: " . $item['title'] . "</p>";
                    echo "<p class='item-author'>Author: " . $item['author'] . "</p>";
                    echo "<p class='item-genre'>Genre: " . $item['genre'] . "</p>";
                    echo "<p class='item-quantity'>Quantity: " . $item['quantity'] . "</p>";

                    echo "<form class='borrow-form' action='/project/borrowings' method='POST'>";
                    echo "<input type='date' name='date' min= '" .date('Y-m-d') . "' max='" . date('Y-m-d', strtotime('+2 months')) . "' class='date-input' required />";
                    echo "<input type='number' name='item_id' value='" . $item['item_id'] . "' class='input-hidden' />";
                    echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "' />"; // for csrf protection
                    echo "<input type='submit' value='Borrow' class='submit' />";
                    echo "</form>";
                    echo "</div>";

                    if (isset($_SESSION["roles"]) && in_array("admin", $_SESSION["roles"])) {
                        echo "<form class='delete-item-form' method='post'>";
                        echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "' />"; // for csrf protection
                        echo "<input type='hidden' name='item_id' value='" . $item['item_id'] . "' />";
                        echo "<input type='hidden' name='_method' value='DELETE'>";
                        echo "<input type='submit' value='Delete Item' class='submit' />";
                        echo "</form>";
                    }

                    echo "</div>";
                }
            ?>
        </div>
    </main>
    <script>
        const button = document.querySelector('.add-item-button');
        const form = document.querySelector('.add-item-form-wrapper');
        form.addEventListener("click", (e) => {
            e.stopPropagation();
            if(e.target === e.currentTarget) form.style.display = 'none';
        });
        if(button) {
            button.addEventListener("click", () => {
                form.style.display = 'block';
            });
        }
    </script>
</body>
</html>