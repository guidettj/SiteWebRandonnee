<?php
session_start();
//Si l'utilisateur veut se connecter, on vérifie si les informations sont les bonnes. Si elles le sont, on le redirige vers la page d'où il vient.
if (isset($_POST['connecter'])) {
    require_once("config.php");
    $bdd = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    if (!$bdd) {
        exit();
    }
    $email = $bdd->quote($_POST['email']);
    $password = $_POST['password'];
    $query = "SELECT username, password FROM utilisateurs WHERE email = $email";
    $result = $bdd->query($query);
    if ($result->rowCount() == 1) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        if(password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            if (!isset($_GET['message'])) {
                header("Location: accueil.php");
                exit();
            } else if ($_GET['message'] == "Veuillez vous connecter pour accéder à la page contribuer.") {
                header("Location: contribuer.php");
                exit();
            } else {
                header("Location: pageRando.php?nom=" . $_SESSION['nom_rando']);
                exit();
            }
        }
        else{
            // Mot de passe incorrect
            $idInvalide = "<p class='erreur'>Identifiants de connexion invalides<p>";
        }
    } else {
        $idInvalide = "<p class='erreur'>Identifiants de connexion invalides<p>";
    }
}
//Si l'utilisateur veut s'inscrire, on vérifie si le nom d'utilisateur et l'adresse mail n'existe pas déjà dans la base de données.
if (isset($_POST['inscription'])) {
    $bdd = new PDO("mysql:host=localhost;dbname=tpnote", "root", "");
    if (!$bdd) {
        exit();
    }
    $username = $bdd->quote($_POST['username']);
    $email = $bdd->quote($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $query = "SELECT username FROM utilisateurs WHERE username = $username";
    $result = $bdd->query($query);
    if ($result->rowCount() > 0) {
        $erreurUsername = "<p class='erreur'>Le nom d'utilisateur est déjà utilisé.</p>";
    } else {
        $query = "SELECT username FROM utilisateurs WHERE email = $email";
        $result = $bdd->query($query);
        if ($result->rowCount() > 0) {
            $erreurEmail = "<p class='erreur'>Vous avez déjà un compte.</p>";
        } else {
            $requete = "INSERT INTO utilisateurs VALUES($username,$email,$password)";
            print($password);
            $requete = "INSERT INTO utilisateurs (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $bdd->prepare($requete);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $resultat = $stmt->execute();
            if ($resultat) {
                $_SESSION['username'] = $_POST['username'];
                if (isset($_GET['message'])) {
                    header("Location: contribuer.php");
                    exit();
                } else {
                    header("Location: accueil.php");
                    exit();
                }
            } else {
                $erreurG = "<p class='erreur'>Une erreur s'est produite lors de l'inscription.</p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="styles/connexion.css" />
</head>

<body>
    <div id="barre">
        <?php
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            echo '<li class="lico"><span id="nom-utilisateur">' . $username . '</span> </li>';
            echo "<form action='' method='POST' id='bouton'>";
            echo "<input type='submit' name='deconnecter' value='Se déconnecter' class='btn'/>";
            echo "</form> ";

        } else {
            echo '<li class="lico"><a id="connexion" href="connexion.php">Connexion</a></li>';
        }
        ?>
        <ul class="barre-de-menu">
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="contribuer.php">Contribuer</a></li>

        </ul>
    </div>
    <div class="centre">
        <main>
            <?php
            if (isset($_GET['message'])) {
                $message = $_GET['message'];
                echo "<p>" . htmlspecialchars($message) . "</p>";
            } ?>
            <h1>Connexion</h1>
            <form action="" method="POST">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required />

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required />

                <input type="submit" name="connecter" value="Se connecter" class="btn" />
                <?php if (isset($idInvalide)) {
                    echo $idInvalide;
                    unset($idInvalide);
                } ?>
            </form>
            <h1>Inscription</h1>
            <form action="" method="POST">
                <label for="prenom">Nom d'utilsateurs * :</label>
                <input type="text" name="username" required />

                <label for="email">Email * :</label>
                <input type="email" name="email" required />

                <label for="password">Mot de passe * :</label>
                <input type="password" name="password" required />

                <input type="submit" name="inscription" value="S'inscrire" class="btn" />
                <?php
                if (isset($erreurUsername)) {
                    echo $erreurUsername;
                    unset($erreurUsername);
                }
                if (isset($erreurEmail)) {
                    echo $erreurEmail;
                    unset($erreurEmail);
                }
                if (isset($erreurG)) {
                    echo $erreurG;
                    unset($erreurG);
                }
                ?>
            </form>
        </main>
        <div>
</body>

</html>
