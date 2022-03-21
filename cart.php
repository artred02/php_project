<?php
include_once("includes/header.php");

//Vérifications permettant de savoir si l'utilisateur est connecté. Si il ne l'est pas il est redirigé vers l'index.
if (!isset($_SESSION["id"])){
    header("Location: /");
}

if (isset($_GET["id"])){
    //Si l'utilisateur a un id de session différent de celui sur le panier auquel il essaye d'accéder et qu'il n'est pas admin,
    //il est redirigé sur son panier
    if($_SESSION["id"] != $_GET["id"] && $_SESSION["rank"] != "admin") {
        header("Location: /cart.php?id=" . $_SESSION["id"]);
    }

    //requête pour connaitre le nombre d'items dans le cart grâce à un compteur
    if (isset($_GET["item_id"])){
        $item_request=getDatabase()->prepareAndExecute('SELECT count(item_id) AS count FROM `items` WHERE `item_id` = ?', array($_GET["item_id"]));
        if ($item_request->fetch(PDO::FETCH_ASSOC)["count"]) {
            array_push($_SESSION["cart"], $_GET["item_id"]);
            header("Location: /cart.php?id=" . $_SESSION["id"]);
        }
    }

    $prix_cart=0;

    echo '
        <div>
        
    ';
    $compteur=0;
    //Affichage des items dans le cart
    foreach ($_SESSION["cart"] as $cart_item) {
        $get_items = getDatabase()->prepareAndExecute("SELECT * FROM items WHERE item_id=?", array($cart_item));
        $item = $get_items->fetch(PDO::FETCH_ASSOC);
        $prix_cart+=$item["item_price"];
        echo '<div class="div_cart">' . 'Nom de la ' . $item["item_category"] .
            ' : ' . $item["item_name"] . '<br>' .
            'Marque de la ' . $item["item_category"] . ' : ' .
            $item["item_brand"] . '<br>' . 'Prix de la ' .
            $item["item_category"] . ' : ' . $item["item_price"] . "€" .
            '</div><br><br>';
        $compteur++;
    }
    echo '
        </div>
    ';

    //Requête pour savoir si il existe une carte pour l'utilisateur
    $seeifcarte=getDatabase()->prepareAndExecute("SELECT * FROM carte_banquaire WHERE cli_id=?", array($_SESSION["id"]));
    if ($seeifcarte->rowCount()==1) {

        //Création d'un bouton pour acheter les items présent dans le panier et d'un boutton pour vider le panier
        //Il y a aussi une div permettant d'afficher le nombre d'articles dans le panier
        if ($compteur>0){
            echo '<form method="post"><input type="submit" name="button_buy" value="Buy ' . $prix_cart . '€"></form>';
            echo '<div class="div_phrase">Vous avez ' . $compteur . ' item(s) dans votre panier</div>';
            echo '<form method="post"><input type="submit" name="button_empty" value="Vider le panier"></form>';
        }else{
            echo '<div class="div_phrase">Votre panier est vide, rendez-vous sur le shop :)</div>';
        }

        //Si le boutton d'achat est cliqué alors on retire l'argent de la carte et on update la table items pour ajouter les items dans la table
        if (isset($_POST["button_buy"])) {
            $carte = getDatabase()->prepareAndExecute("SELECT * FROM carte_banquaire WHERE cli_id=?", array($_SESSION["id"]));
            $carte = $carte->fetch();
            if ($carte["carte_solde"] >= $prix_cart) {
                $carte["carte_solde"] -= $prix_cart;
                getDatabase()->prepareAndExecute("UPDATE carte_banquaire SET carte_solde=? WHERE carte_id=?", array($carte["carte_solde"], $carte["carte_id"]));

                foreach ($_SESSION["cart"] as $cart_item){

                    $inventoryselect=getDatabase()->prepareAndExecute("SELECT inventory_count FROM inventory WHERE inventory_client_id=? AND inventory_item_id=?", array($_SESSION["id"], $cart_item));
                    if ($inventoryselect->rowCount()==0){
                        getDatabase()->prepareAndExecute("INSERT INTO inventory(inventory_item_id, inventory_count, inventory_client_id) VALUES(?, ?, ?)", array($cart_item, 1, $_SESSION["id"]));
                    }else{
                        $inventorycount=$inventoryselect->fetch()[0];
                        getDatabase()->prepareAndExecute("UPDATE inventory SET inventory_count=? WHERE inventory_client_id=? AND inventory_item_id=?", array($inventorycount+1, $_SESSION["id"], $cart_item));
                    }
                }

                //on vide le panier et on redirige sur le panier pour que celà actualise
                $_SESSION["cart"] = array();
                header("Location: /cart.php?id=" . $_SESSION["id"]);
                die();
            }else{
                //Si le solde est insuffisant alors on affiche la phrase
                echo '<div class="div_phrase">Le solde est insuffisant</div>';
            }
        }

        //Si le boutton permettant de vider le panier est cliqué alors on vide le panier
        if (isset($_POST["button_empty"])){
            $_SESSION["cart"] = array();
            header("Location: /shop.php");
        }

    }else{
        //Si l'utilisateur n'a pas de carte banquaire, il faut qu'il aille en créer une
        echo '<div class="div_phrase">Pour acheter il vous faut une carte banquaire (ça fonctionne mieux), alors rendez-vous sur votre profil :)</div>';
    }
}

include_once "includes/footer.php";