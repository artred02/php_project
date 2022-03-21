<?php
include_once("includes/header.php");

?>
<!--Formulaire permettant d'accéder aux éléments du shop grâce à sa catégorie-->
<div class="div_form">
    <form method="post">
        <select name="item_category" class="all_buttons">
            <option value="Carte mère">Carte mère</option>
            <option value="Processeur">Processeur</option>
            <option value="Carte graphique">Carte graphique</option>
            <option value="Boitier">Boitier</option>
            <option value="Mémoire RAM">Mémoire RAM</option>
        </select>
        <input type="submit" name="submit" value="Chercher" class="all_buttons">
    </form>
</div>

<nav class="div">
    <?php
    //Si on click sur le boutton de pour chercher, une requête vers les items est faite par la catégorie
    if (isset($_POST["submit"])){
        $get_items=getDatabase()-> prepareAndExecute("SELECT * FROM items WHERE item_category=?", array($_POST["item_category"]));
        $items=$get_items->fetchAll(PDO::FETCH_ASSOC);



        echo '
            <div class="div_research">
            
        ';

        //affichage des items
        echo '<div class="div_phrase">';
            foreach ($items as $item){
                echo $item["item_category"] . " : " . "<br>" .
                    'Nom de la ' . $item["item_category"] .
                    ' : ' . $item["item_name"] . '<br>' .
                    'Marque de la ' . $item["item_category"] .' : '.
                    $item["item_brand"] . '<br>' . 'Prix de la ' .
                    $item["item_category"] . ' : ' . $item["item_price"] . "€<br>";
                //Si on est connecté alors on peut ajouter l'item à notre panier
                    if (isset($_SESSION["id"])){
                        echo '<br><a class="button_add_cart" href="cart.php?id=' . $_SESSION["id"] . '&item_id='. $item["item_id"] .'">add to your cart</a>'
                    .'<br><br>';
                    }else{
                        echo'<br>';
                    }
            }
            echo '
            </div>
        ';
    }
    ?>

</nav>

<?php
include_once "includes/footer.php";