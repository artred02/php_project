<?php

require_once "includes/header.php";
?>

<body>
<br><br><br><br>

<?php
//Vérifications permettant de savoir si l'utilisateur est connecté. Si il ne l'est pas il est redirigé vers l'index.
if (!isset($_SESSION["id"])){
    echo '<div class="nav"><p>Bonjour et bienvenu sur LE site d\'achat de pièces détachées.</p> <br> <p>Avant toutes choses, pour acheter, il faut s\'inscrire grâce au bouton \'Register\' ou vous connecter avec le bouton \'Login\'.
        <br>Je vous souhaite de trouver le bonheur parmis nous!!!</p></div>';
}else{
    //Affichage d'images
    echo'<div class="nav">Ici, nous vendons plusieurs types de pièces détachées disponibles sur le shop.</div><br>';
    echo '<img src="ressources/CPU.jpg" alt="CPU" width="300" height="200">';
    echo '<img src="ressources/GPU.jpg" alt="GPU" width="300" height="200">';
    echo '<img src="ressources/motherboard.jpg" alt="motherboard" width="300" height="200">';
    echo '<img src="ressources/boitier.png" alt="boitier" width="300" height="200">';
    echo '<img src="ressources/RAM.png" alt="RAM" width="300" height="200">';
}
?>

</body>

<?php
include_once "includes/footer.php";