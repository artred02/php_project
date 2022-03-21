<?php
require_once "core/database.php";
session_start();
//appel de la base de donnée, le header est appelé par toutes les autres pages, donc pas besoin de le refaire pour chaque page
$database = new Database("localhost", "phpproject", "root", "");

function getDatabase() {
    GLOBAL $database;
    return $database;
}

?>
<!doctype html>
<html lang="fr">
<link rel="stylesheet" type="text/css" href="../index/index.css"/>

<!--Création du header-->
<header>
    <nav class="nav_bar">
        <div class="div_menu">
            <a href="/" class="menu">Menu Principal</a>
        </div>

        <div>
            <?php
            //Voir si l'utilisateur est connecté grace aux variables de session, si il l'est il y a un un texte qui indique le nom avec lequel il est conecté
            if (isset($_SESSION["id"])){
                echo '<div class="header_button">' .
                    'Connected as : '.$_SESSION["name"] .
                    '</div>';
            }
            ?>
        </div>

        <div>
            <?php
            //Voir si l'utilisateur est connecté grace aux variables de session, si il l'est il y a un boutton pour accéder à son panier qui apparaît
            if (isset($_SESSION["id"])) {
                echo '<a class="header_button" href="shop.php">Shop</a>';
            }else{
                echo '<a class="button_carte_not_connected" href="shop.php">Shop</a>';
            }
            ?>
        </div>

        <div>
            <?php
            //Voir si l'utilisateur est connecté grace aux variables de session, si il l'est il y a un boutton pour accéder à son panier qui apparaît
            if (isset($_SESSION["id"])){
                echo '<a class="header_button" href="cart.php?id=' . $_SESSION["id"] . '">' . 'Cart' . '</a>';
            }

            ?>
        </div>
        <div>
            <?php
            //Voir si l'utilisateur est connecté grace aux variables de session, si il l'est il y a un boutton pour accéder à son panier qui apparaît
            if (isset($_SESSION["id"])){
                echo '<a class="header_button" href="inventory.php?id=' . $_SESSION["id"] . '">' . 'Inventory' . '</a>';
            }

            ?>
        </div>

        <div>
            <?php
            if (isset($_SESSION["id"])){
                //Voir si l'utilisateur est connecté grace aux variables de session, si il l'est il y a des bouttons qui apparaîssent pout se logout ou voir le profil
                echo '<div class="if_connected"><a class="a_login" href="/profile.php?id=' . $_SESSION["id"] . '">Profile</a>';
                echo '<a class="a_register" href="/logout.php">Logout</a></div>';
            } else {
                //Si il ne l'est pas, les bouttons de connection et de register apparraissent à la place
                echo '<div class="if_not_connected"><a class="a_login" href="/login.php">Login</a><br>';
                echo '<a class="a_register" href="/register.php">Register</a></div>';
            }
            ?>
        </div>

    </nav>

</header>
