<?php
    /**
     * Connection à la DATABASE et afficher les données en fonctions des besoins
     * 
     * @author Ismail T.
     * 
     * @version 0.1, 17 avril 2022  
     */
    require 'config.php';
    $message = "";
    $books = [];
    $keyword;
    
    if (!empty($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    }
    
    
    $mysqli = @mysqli_connect(HOSTNAME,USERNAME,PASSWORD);
    
    if($mysqli){
        if (mysqli_select_db($mysqli, DATABASE)) {
            //preparation de la requete
            if (empty($keyword)) {
                $query = "SELECT * FROM `books`";    
            } else {
                $query = "SELECT * FROM `books` WHERE title LIKE '%$keyword%'";
            }
            
            //Envoie de la requete
            $result = mysqli_query($mysqli, $query);
            //var_dump($result);
            if ($result) {
                //Extraction des resultats
                while(($book = mysqli_fetch_assoc($result)) != null ){
                    $books[] = $book ;
                }
                //Liberation de la mémoire
                mysqli_free_result($result);
            } else {
                $message = 'Request error !';
            }
            

        } else {
            $message = 'database error !';
        }
        //Fermeture de la connection
        mysqli_close($mysqli);
    } else {
        $message = 'Error connection !';
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>biblioweb :: DB ACCESS</title>
</head>
<body>
    <div><?= $message; ?></div>
    <form action="<?= $_SERVER['PHP_SELF']?>" method="get">
        <input type="text" name="keyword" placeholder="Titre du livre">
        <button>Search</button>
    </form>
    <section id="list">
        <?php foreach ($books as $book) { ?>
            <article>
                <figure>
                    <img src="<?= $book['cover_url']?>" alt="<?= $book['title']?>" width="100">
                    <figcaption> <?= $book['title']?></figcaption>
                </figure>
                <p><?= $book['description']?></p>
                <p><a href="edit.php?ref=<?= $book['ref'] ?>">Modifier</a></p>
            </article>
        <?php } ?>
    </section>
</body>
</html>