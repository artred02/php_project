<!--La page admin, celle qui permet de gérer les utilisateurs et d'insérer des items dans la base de donnée.-->

<?php


include_once ("includes/header.php");

//Vérifications permettant de savoir si l'utilisateur est connecté. Si il ne l'est pas il est redirigé vers l'index.
if (!isset($_SESSION["id"])){
    header("Location: /");
}

//Voir si l'utilisateur est bien adminitrateur
if ($_SESSION["rank"]!='admin') {
    header("Location: /");
}
?>

<?php
//insertion d'un item dans la base de donnée si le bouton du formulaire est pressé.
if (isset($_POST["button_form"])) {
    $item_name=$_POST["item_name"];
    $item_category=$_POST["item_category"];
    $item_brand=$_POST["item_brand"];
    $item_price=$_POST["item_price"];
    getDatabase()->prepareAndExecute("INSERT INTO `items`(`item_category`, `item_name`, `item_brand`, `item_price`) VALUES (?,?,?,?)",array($item_category,$item_name,$item_brand,$item_price));
}


//$see_client est une fonction qui récupère toute la table client
$see_client=getDatabase()-> prepareAndExecute("SELECT * FROM client");
$see_client=$see_client->fetchAll(PDO::FETCH_ASSOC);

//Afficher un bouton qui redirige vers le profil utilisateur sur lequel on clique
foreach ($see_client as $client) {
    echo '<a class="see_profile_as_admin" href="/profile.php?id=' . $client["cli_id"] . '">' . $client["cli_name"] . '</a><br>';
}

?>

<!--Formulaire de création d'un item-->
<!doctype html>

<div class="form_item">
    <form method="post">
        <input type="text" name="item_name" placeholder="nom de la pièce :">
        <input type="text" name="item_brand" placeholder="marque de la pièce :">
        <input type="number" step="0.01" name="item_price" placeholder="prix :">
        <select name="item_category">
            <option value="Carte mère">Carte mère</option>
            <option value="Processeur">Processeur</option>
            <option value="Carte graphique">Carte graphique</option>
            <option value="Boitier">Boitier</option>
            <option value="Mémoire RAM">Mémoire RAM</option>
        </select>
        <input type="submit" name="button_form" class="all_buttons">
    </form>
</div>





<?php
include_once "includes/footer.php";