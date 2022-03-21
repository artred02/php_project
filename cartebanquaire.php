<?php

include_once ("includes/header.php");

//Vérifications permettant de savoir si l'utilisateur est connecté. Si il ne l'est pas il est redirigé vers l'index.
if (!isset($_SESSION["id"])){
    header("Location: /");
}
//requête de la carte d'un client grâce à son id
$getcarte=getDatabase()-> prepareAndExecute("SELECT * FROM carte_banquaire WHERE cli_id=?",array($_SESSION["id"]));


//vérifions si le boutton est cliqué
if (isset($_POST["submit"])){
    //on cherche ici à savoir si une carte a déjà étée créée.
    if ($getcarte->rowCount()==0){
        if (strlen($_POST["num_card"])==16){
            $carte_num = htmlspecialchars($_POST["num_card"]);
            $solde = htmlspecialchars($_POST["solde"]);
            $carte = getDatabase()->prepareAndExecute("SELECT carte_num FROM carte_banquaire WHERE carte_num=?", array($carte_num));
            if ($carte->rowCount()==0){
                getDatabase()->prepareAndExecute("INSERT INTO carte_banquaire(carte_num, carte_solde, cli_id) VALUES (?,?,?)", array($carte_num, $solde, $_SESSION["id"]));
                header("Location: /profile.php?id=" . $_SESSION["id"]);
            }
        }else{
            echo '<div class="div_phrase">Une carte banquaire doit être composéee de 16 chiffres.</div>';
        }


    }
}

//Si il n'existe pas encore de carte un formulaire est créé
if ($getcarte->rowCount()==0){
    echo '
        <form method="post">
            <input type="text" name="num_card" placeholder="numéro de carte :">
            <input type="number" name="solde" placeholder="solde sur la carte :">
            <br><br><input type="submit" name="submit" class="all_buttons">
        </form>';
}


?>

<?php
include_once "includes/footer.php";