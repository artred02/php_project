<?php
require_once "includes/header.php";
    if (isset($_POST["submit"])){
        if(!empty($_POST["email"]) && !empty($_POST["password"])) {
            $email=$_POST["email"];
//        $getclient=$bdd->prepare("SELECT * FROM client WHERE cli_mail=?");
//        $getclient->execute(array($email));
            $getclient=getDatabase()-> prepareAndExecute("SELECT * FROM client WHERE cli_mail=?",array($email));
            if ($getclient->rowCount()==1){
                $getclient=$getclient->fetch();
                if (password_verify($_POST["password"], $getclient["cli_password"])){
                    $_SESSION["name"]=$getclient["cli_name"];
                    $_SESSION["surname"]=$getclient["cli_surname"];
                    $_SESSION["email"]=$getclient["cli_mail"];
                    $_SESSION["id"]=$getclient["cli_id"];
                    $_SESSION["rank"]=$getclient["rank"];
                    $_SESSION["cart"]=array();
                    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
                    header("Location: /");
                }
            }
        }
    }


?>

<form method="post" >
    <input required type="email" name="email" placeholder="adresse mail" /><br />
    <input required type="password" name="password" placeholder="mot de passe">
    <br /><input type="submit" name="submit" />
</form>


<?php
include_once "includes/footer.php";