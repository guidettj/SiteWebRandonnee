<?php
session_start();
//Déconnexion
if (isset($_POST['deconnecter'])) {
    session_destroy();
    header("Location: accueil.php");
    exit();
}
//Si l'utilisateur n'est pas connecté, on crée une variable qui stockera le message à afficher et qui permettra de revenir sur cette page une fois connecté.
if (!isset($_SESSION['username'])) {
    $message = "Veuillez vous connecter pour accéder à la page contribuer.";
    header("Location: connexion.php?message=" . urlencode($message));
    exit();
}
require_once("config.php");
$bdd = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
if (!$bdd) {
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Contribuer</title>
    <link rel="stylesheet" type="text/css" href="styles/contribuer.css" />
</head>

<body>
    <div id="barre">
        <?php
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            echo '<li class="lico"><span id="nom-utilisateur">' . $username . '</span> </li>';
            echo "<form action='' method='POST' id='bouton'>";
            echo "<input type='submit' name='deconnecter' value='Se déconnecter' class='btn' />";
            echo "</form> ";

        } else {
            echo '<li class="lico"><a id="connexion" href="connexion.php">Connexion</a></li>';
        }
        ?>
        <ul class="barre-de-menu">
            <li><a href="accueil.php">Accueil</a></li>
            <li><a href="contribuer.php" class="actuel">Contribuer</a></li>
        </ul>
    </div>
    <div class="centre">
        <main>
            <?php //Ajout des données dans les bases de données.
            if (isset($_POST['ajout'])) {
                $nom = $bdd->quote($_POST['nom']);
                $km = $bdd->quote($_POST['km']);
                $duree = $bdd->quote($_POST['duree']);
                $difficulte = $bdd->quote($_POST['difficulte']);
                $deniveleP = $bdd->quote($_POST['deniveleP']);
                $deniveleN = $bdd->quote($_POST['deniveleN']);
                $adresse = $bdd->quote($_POST['adresse']);
                $note = $bdd->quote($_POST['note']);
                $description = $bdd->quote($_POST['description']);
                $departement = $bdd->quote($_POST['departement']);
                $img_nom = $_FILES['image']['name'];
                //img_BD doit autoriser lecture et éxecution pour tout le monde
                $destination = "img_BD/" . $img_nom;
                $deplacer = move_uploaded_file($_FILES['image']['tmp_name'], $destination);
                if (!$deplacer) {
                    echo "<p>Le fichier est trop volumineux.</p>";
                    exit();
                }
                try {
                    $requete = "INSERT INTO randonnees VALUES(NULL,$nom,'$img_nom',$km,$duree,$difficulte,$deniveleP,$deniveleN,$adresse,$note,$description,$departement)";
                    $resultat = $bdd->exec($requete);
                    if ($resultat !== false) {
                        echo "<p class='mrc'>Merci d'avoir ajouté une randonnée ! Votre contribution est précieuse.</p>";
                        $_SESSION['ajout'] = $_POST['ajout'];
                        $_SESSION['nom_rando'] = $_POST['nom']; //variables utilisées lorsque l'utilisateur ira sur acceuil
                        //Ajout de la note dans la base de données :
                        try {
                            $sql_insert = "INSERT INTO note (randonnee_id, utilisateur_id, note) VALUES ($nom, '$username', $note)";
                            $resultat = $bdd->exec($sql_insert);
                        } catch (Exception $e) {
                            echo "<p class='erreur'>Erreur : " . $e->getMessage() . "</p>";
                        }
                    } else {
                        echo "<p class='erreur'>Une erreur est survenue lors de l'ajout de la randonnée. Veuillez réessayer ultérieurement.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='erreur'>Erreur : " . $e->getMessage() . "</p>";
                }
            } else {
                ?>
                <div id="ajout">Ajouter une randonnées</div>
                <form class="form" action="" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <table>
                            <tr>
                                <td>Nom de la randonnée :</td>
                                <td><textarea name="nom" required cols="60" rows="1"></textarea></td>
                            </tr>
                            <tr>
                                <td>Image :</td>
                                <td><input type="file" name="image" required /></td>
                            </tr>
                            <tr>
                                <td>Distance :</td>
                                <td><input type="number" name="km" step="0.01" required />
                                    <span>km</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Durée :</td>
                                <td><input type="time" name="duree" required /></td>
                            </tr>
                            <tr>
                                <td>Difficulté :</td>
                                <td><select name="difficulte">
                                        <option value="Très facile">Très facile</option>
                                        <option value="Facile">Facile</option>
                                        <option value="Moyenne">Moyenne</option>
                                        <option value="Difficile">Difficile</option>
                                        <option value="Très difficile">Très difficile</option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>Dénivelé Positif :</td>
                                <td><input type="number" name="deniveleP" required /></td>
                            </tr>
                            <tr>
                                <td>Dénivelé Négatif :</td>
                                <td><input type="number" name="deniveleN" required /></td>
                            </tr>
                            <tr>
                                <td>Adresse de départ :</td>
                                <td><textarea name="adresse" required cols="60" rows="1"></textarea></td>
                            </tr>
                            <tr>
                                <td>Département :</td>
                                <td><select name="departement">
                                        <option value="Ain">Ain</option>
                                        <option value="Allier">Allier</option>
                                        <option value="Ardèche">Ardèche</option>
                                        <option value="Cantal">Cantal</option>
                                        <option value="Drôme">Drôme</option>
                                        <option value="Isère">Isère</option>
                                        <option value="Loire">Loire</option>
                                        <option value="Haute-Loire">Haute-Loire</option>
                                        <option value="Puy-de-Dôme">Puy-de-Dôme</option>
                                        <option value="Rhône">Rhône</option>
                                        <option value="Savoie">Savoie</option>
                                        <option value="Haute-Savoie">Haute-Savoie</option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>Donnez une note de 1 à 5 :</td>
                                <td>
                                    <input type="radio" name="note" id="note1" value="1" required>
                                    <label for="note1">1</label>
                                    <input type="radio" name="note" id="note2" value="2" required>
                                    <label for="note2">2</label>
                                    <input type="radio" name="note" id="note3" value="3" required>
                                    <label for="note1">3</label>
                                    <input type="radio" name="note" id="note4" value="4" required>
                                    <label for="note2">4</label>
                                    <input type="radio" name="note" id="note5" value="5" required>
                                    <label for="note2">5</label>
                                </td>
                            </tr>
                            <tr>
                                <td>Description de la randonnée :</td>
                                <td><textarea name="description" required cols="60" rows="20"></textarea></td>
                            </tr>
                            <tr>
                                <td><input type="reset" value="Effacer" class="btn" />
                                    <input type="submit" name="ajout" value="Enregistrer" class="btn" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
                <?php
            }

            ?>
        </main>
    </div>
</body>

</html>
