<?php
require_once "includes/header.php";

//Si on appuie sur le bouton, les infos rentrées sont enregistrées dans la base de données si elles sont différentes de null.
if (isset($_POST["submit"])){
    $email=htmlspecialchars($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }
    $name=htmlspecialchars($_POST["name"]);
    $surname=htmlspecialchars($_POST["surname"]);

    #hash du password
    $password=password_hash($_POST["password"], PASSWORD_BCRYPT);

    //verification entre le password et le confirm password
    //Si l'email n'est pas présent dans la bdd (rowcount==0) alors la création du compte peut se faire
    if (password_verify($_POST["confirm_password"],$password)){
        $getclient=getDatabase()-> prepareAndExecute("SELECT * FROM client WHERE cli_mail=?",array($email));
        if ($getclient->rowCount()==0){
            getDatabase()-> prepareAndExecute("INSERT INTO `client`(`cli_password`, `cli_name`, `cli_surname`, `cli_mail`, `rank`) VALUES (?,?,?,?,?)",array($password, $name,$surname, $email, 'user'));
            //lorsque le compte est créé, on est redirigé vers la page de login
            header("Location: /login.php");
            } else{
                echo '<div class="infos">Cet email est déjà utilisé, voulez-vous vous <a href="login.php">login</a> ?</div>';
        }
    } else {
        echo '<div class="infos">Je crois que les mots de passes sont différents.</div>';
    }
}

?>

<link rel="stylesheet" type="text/css" href="index/index.css"/>

<!--Formulaire de création de compte-->
<form method="post" >
    <input required type="email" name="email" placeholder="adresse mail" />
    <input required type="password" name="password" placeholder="mot de passe">
    <input required type="password" name="confirm_password" placeholder="confirmer le mot de passe">
    <input required type="text" name="name" placeholder="Nom">
    <input required type="text" name="surname" placeholder="Prénom">
    <br /><input type="submit" name="submit" />
</form>

<?php
include_once "includes/footer.php";