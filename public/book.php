<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/book.css">
    <title>Checkpoint PHP 1</title>
</head>
<body>

<?php

include 'header.php';

require_once '../connec.php';

$pdo = new \PDO(DSN, USER, PASS);

$errors=[];

/** INSERT */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newBribe = array_map('trim',$_POST);


    if(empty($newBribe['name'])){
        $errors[]="The field name should not be empty";
    }

    if(empty($newBribe['payment'])){
        $errors[]="The field payment should not be empty";
    }

    $minPayment = 0;
    if($newBribe['payment']<=0){
        $errors[]="Payment amount should be more than 0";
    }

    $nameMaxLength = 200;
    if(strlen($newBribe['name'] > $nameMaxLength )){
        $errors[]="Your name should be less than 200 characters";
    }

    if(empty($errors)){

        $query = 'INSERT INTO bribe (name, payment) VALUES (:name, :payment)';
        $statement = $pdo->prepare($query);

        $statement->bindValue(':name', $newBribe['name'], \PDO::PARAM_STR);
        $statement->bindValue(':payment', $newBribe['payment'], \PDO::PARAM_INT);

        $statement->execute();

        header("Location: book.php");
    }
}


/** SHOW */

$query = "SELECT * FROM bribe ORDER BY name ASC";
$statement = $pdo->query($query);
$bribes = $statement->fetchAll(PDO::FETCH_ASSOC);



/** SHOW BY ID  **/

$query = 'SELECT name, payment FROM bribe WHERE name LIKE :letter';
$statement = $pdo->prepare($query);
$statement->bindValue(':letter', ($_GET['letter']).'%', PDO::PARAM_STR);
$statement->execute();

$bribesByLetter = $statement->fetch(PDO::FETCH_ASSOC);

return $bribesByLetter;


?>


<main class="container">
    <nav class="index">
        <?php foreach (range('A', 'Z') as $letter) { ?>
            <a href="book.php?letter=<?= $letter ?>"> <?= $letter; ?></a>
        <?php } ?>
    </nav>
    <section class="desktop">
        <div class="whisky-glass">
            <img src="image/whisky.png" alt="a whisky glass" class="whisky"/>
        </div>
        <div class="empty-glass">
            <img src="image/empty_whisky.png" alt="an empty whisky glass" class="empty-whisky"/>
        </div>

        <div class="pages">
            <div class="page leftpage">
                <p> Add a bribe </p>
                <ul>
                    <?php foreach ($errors as $error) {  ?>
                        <li> <?= $error ?></li>
                    <?php } ?>
                </ul>
                <form class="form" method="post" action="">
                    <label for="name">Name :</label>
                    <input type="text" id="name" name="name" value="<?= $newBribe['name'] ?? '' ?>">
                    <label for="payment">Payment :</label>
                    <input type="number" id="payment" name="payment" value="<?= $newBribe['payment'] ?? '' ?>">
                    <button>Pay!</button>
                </form>
            </div>

            <div class="page rightpage">
                <table>
                    <thead>
                    <tr>
                        <th colspan="2"> S </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bribesByLetter as $bribe) { ?>
                    <tr>
                        <td><?= $bribe['name'] ?></td>
                        <td><?= $bribe['payment'].' €' ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Sum</td>
                        <td class="sum">
                            <?php
                            $bribesSum=0;
                            foreach ($bribes as $bribe){
                                $bribesSum += $bribe['payment'];
                            }

                            echo $bribesSum.' €';
                            ?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="pen">
            <img src="image/inkpen.png" alt="an ink pen" class="inkpen"/>
        </div>
    </section>
</main>
</body>
</html>
