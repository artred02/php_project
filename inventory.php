<?php
include_once("includes/header.php");
//Vérifications permettant de savoir si l'utilisateur est connecté. Si il ne l'est pas il est redirigé vers l'index.
if (!isset($_SESSION["id"])){
    header("Location: /");
}

//Si l'utilisateur a un id de session différent de celui sur l'inventaire auquel il essaye d'accéder et qu'il n'est pas admin,
//il est redirigé sur son inventaire
if (isset($_GET["id"])) {
    if ($_SESSION["id"] != $_GET["id"] && $_SESSION["rank"] != "admin") {
        header("Location: /inventory.php?id=" . $_SESSION["id"]);
    }

    //Requête d'une partie de la table
    $inventory = getDatabase()->prepareAndExecute("SELECT items.item_name, items.item_brand, items.item_category, inventory.inventory_count FROM inventory, items WHERE inventory.inventory_client_id=? AND items.item_id=inventory.inventory_item_id", array($_SESSION["id"]));
    $inventoryfetch = $inventory->fetchAll(PDO::FETCH_ASSOC);

    //Affichage des items appartenant à l'utilisateur
    foreach ($inventoryfetch as $inventorylist) {
        echo '<div class="inventory">' .
            'Item category : ' . $inventorylist["item_category"] . '<br>' .
            'Item brand : ' . $inventorylist["item_brand"] . '<br>' .
            'Item name : ' . $inventorylist["item_name"] . '<br>' .
            'Number of items : ' . $inventorylist["inventory_count"] .
            '<br><br><br>' .
            '</div>';
    }

}



include_once "includes/footer.php";