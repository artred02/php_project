<?php
include_once 'includes/header.php';

//Vérifications permettant de savoir si l'utilisateur est connecté. Si il ne l'est pas il est redirigé vers l'index.
if (isset($_SESSION["id"])){
    //Si l'utilisateur a un id de session différent de celui sur le profil auquel il essaye d'accéder et qu'il n'est pas admin,
    //il est redirigé sur son profil
    if($_SESSION["id"] != $_GET["id"] && $_SESSION["rank"] != "admin") {
        header("Location: /profile.php?id=" . $_SESSION["id"]);
    }
} else {
    header("Location: /");
}

//Si l'utilisateur est admin alors il peut changer le rank de la personne depuis le profil de cette dernière
if (isset($_POST["button_change"])){
    if ($_SESSION["rank"]=="admin"){
        if ($_POST["change_rank"]=="admin" || $_POST["change_rank"]=="user") {
            $change_rank = getDatabase()->prepareAndExecute("UPDATE `client` SET `rank`=? WHERE `cli_id` = ?", array($_POST["change_rank"], $_GET["id"]));
        }
    }
}

//Requête pour connaître le profil de la personne que l'on veut voir.
//Si le rank est user alors il ne put voir que son profil
//Si c'est un admin il pourra regarder les infos des autre personnes
$getuser = getDatabase()->prepareAndExecute("SELECT * FROM client WHERE cli_id = ?", array($_GET["id"]));
if($getuser->rowCount()==0){
    header("Location: /profile.php?id=" . $_SESSION["id"]);
}

$user = $getuser->fetch(PDO::FETCH_ASSOC);

//On affiche ici le profil
echo '<div class="div_user">Name : ' . $user["cli_name"] . '<br>' . 'Surname : ' . $user["cli_surname"] . '<br>' . 'Email : ' . $user["cli_mail"] . '<br>' . 'Rank : ' . $user["rank"] . '<br>' . '<br>' . '</div>'

?>

<?php
//Le bouton n'apparaît que si l'on est admin
if(isset($_SESSION["id"]) && $_SESSION["rank"] == "admin") {
    echo '
    <form method="post" class="div_user">
        <select name="change_rank" value="' . $user["rank"] . '" class="all_buttons">
            <option value="admin">admin</option>
            <option value="user">user</option>
        </select>
        <input type="submit" name="button_change" value="Changer" class="all_buttons">
    </form>';
}

//Si l'on se situe sur notre profil, un bouton d'ajout de carte banquaire apparaît et permet de nous redirige vers une page de création de carte
if ($_SESSION["id"]==$_GET["id"]){
    $getcarte=getDatabase()->prepareAndExecute("SELECT * FROM carte_banquaire WHERE cli_id=?", array($_SESSION["id"]));
    if ($getcarte->rowCount()==0){
        echo '
        <form method="post" class="form_button">
            <br><br>
            <input type="submit" name="submit" value="Ajouter une carte banquaire" class="all_buttons">
        </form>';
    }else{
        //Si l'utilisateur a déjà une carte, il peut voir son numéro de carte et le solde restant sur sa carte
        $carte = $getcarte->fetch();
        echo '<div class="div_card"> Numéro de carte : ' . $carte["carte_num"] . '<br> Solde : ' . $carte["carte_solde"] . '</div>';
    }
    }
if (isset($_POST["submit"])){
    header("Location: cartebanquaire.php");
}

?>


<div>
    <?php
    //Voir si l'utilisateur a pour rank 'admin', si il l'est il y a un bouton administration qui apparaît sur son profil
    if (isset($_SESSION["id"])){
        if ($_SESSION["rank"]=='admin'){
            echo "<a href='admin.php' class='admin_button'>Administration</a>";
        }
    }

    ?>
</div>





<?php

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // derniere requete depuis 30min
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
}
include_once "includes/footer.php";