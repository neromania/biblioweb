<?php
require 'config.php';
$mysqli = mysqli_connect(HOSTNAME,USERNAME,PASSWORD);
$book = '';
$ref = "";
$message = "";
$_POST = "";
if (!empty($_GET['ref'])) {
    $ref = $_GET['ref'];
}elseif (!empty($_POST['ref'])) {
    if(!empty($_POST['title']) || !empty($_POST['description']) || !isset($_POST['cover_url']) || !isset($_POST['author_id'])  ){
        // Préparation de la requête: nettoyer les données entrantes​
//$reference = mysqli_real_escape_string($mysql, $_POST['ref']);
$title = mysqli_real_escape_string($mysqli, $_POST['title']);
$description = mysqli_real_escape_string($mysqli, $_POST['description']);
$coverUrl = mysqli_real_escape_string($mysqli, $_POST['cover_url']);
$authorId = mysqli_real_escape_string($mysqli, $_POST['author_id']);
$query3 = "UPDATE INTO books (title,author_id,description,cover_url) 
VALUES ('$title', '$authorId', '$description','$coverUrl')";
// Envoi de la requête​
$result = mysqli_query($mysqli, $query3);

// Vérification du résultat​
if($result && mysqli_affected_rows($mysqli)>0) {
echo "Insertion réussie.";
} else {
echo "Une erreur est survenue lors de l’insertion.";
}
    } else {
        $message = "Vous devez au moins renseigner un des champs a modifier !";
    }
} else {
    header('Location: index.php');
    header('HTTP/1.1 400 Bad Request');
    exit;
}

   
if($mysqli){
    if (mysqli_select_db($mysqli, DATABASE)) {
        //preparation de la requete
            $query = "SELECT * FROM `books` INNER JOIN authors ON books.author_id=authors.id WHERE ref='$ref'";
            $querySelect = "SELECT * FROM `authors`";
        //Envoie de la requete
        $result = mysqli_query($mysqli, $query);
        $result2 = mysqli_query($mysqli, $querySelect);

        //var_dump($result);
        if ($result) {
            //Extraction des resultats
            $book = mysqli_fetch_assoc($result);
            while(($author = mysqli_fetch_assoc($result2)) != null ){
                $authors[] = $author;
            }
            
            //Liberation de la mémoire
            mysqli_free_result($result);
            mysqli_free_result($result2);
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
//var_dump($book); 
//var_dump($authors);
?>
<div><?= $message ?></div>
<form action="<?= $_SERVER['PHP_SELF']?>" method="post" >
    <div>
        <input type="hidden" name="ref" value="<?= $book['ref']?>">
    </div>
    <div>
        <label>Titre : </label>
        <input type="text" name="title" value="<?= $book['title']?>">
    </div>
    <div>
        <label>Description : </label>
        <input type="text" name="description" value="<?= $book['description']?>">
    </div>
    <div>
        <label>Illustration : </label>
        <input type="text" name="illustration" value="<?= $book['cover_url']?>">
    </div>
    <select name="<?= $book['id']?>">
    <option value="0"></option>
        <?php foreach ($authors as $author) { ?>
            <optgroup label="<?= $author['nationality']?>">
                <option value="<?= $author['id']?>" <?= ($book['author_id'] == $author['id']) ? 'selected': ''?>><?= $author['firstname'].' '.$author['lastname']?></option>
            </optgroup>
        <?php } ?>
    </select>
    <button type="submit">Modifier</button>
</form>
<pre>
<?= var_dump($_POST)?>
</pre>
