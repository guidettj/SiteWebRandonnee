<?php
//LES DESCRIPTIONS ET INFORMATIONS DES RANDONNÉES ONT ÉTÉ TROUVÉ SUR LE SITE VISORANDO.

session_start();
$bdd = new PDO("mysql:host=localhost;dbname=ExempleBD", "root", "");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//on récupère les colonnes de la ligne de la randonnées
$sql = "SELECT * FROM randonnees";
$result = $bdd->query($sql);
if (isset($_GET['nom'])) {
    $nom = $_GET['nom'];
    $sql = "SELECT * FROM randonnees WHERE nom = :nom";
    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}
//Lorsque l'utilisateur ajoute une note, on vérifie si il est connecté, puis on verifie si il a déjà noté cette randonnée, et on ajoute la note dans la base de données
//Enfin, on recalcule la moyenne pour pouvoir la modifier dans la descripiton.
if (isset($_POST['ajout_note'])) {
    if (!isset($_SESSION['username'])) {
        $_SESSION['nom_rando'] = $_GET['nom'];
        $message = "Veuillez vous connecter pour donner votre avis.";
        header("Location: connexion.php?message=" . urlencode($message));
        exit();
    } else {
        $nom = $_GET['nom'];
        $username = $_SESSION['username'];
        $sql_check = "SELECT COUNT(*) FROM note WHERE randonnee_id = :nom AND utilisateur_id = :username";
        $stmt_check = $bdd->prepare($sql_check);
        $stmt_check->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt_check->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt_check->execute();

        $count = $stmt_check->fetchColumn();
        if ($count > 0) {
            $message_n = "<p class='erreur'>Vous avez déjà noté cette randonnées</p>";
        } else {
            $note = $_POST['note'];
            $sql_insert = "INSERT INTO note (randonnee_id, utilisateur_id, note) VALUES (:nom, :username, :note)";
            $stmt_insert = $bdd->prepare($sql_insert);
            $stmt_insert->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt_insert->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt_insert->bindParam(':note', $note, PDO::PARAM_INT);
            $stmt_insert->execute();
            $message_n = "<p class='mrc'>Merci d'avoir donné votre avis!</p>"; //ici

            $nom = $_GET['nom'];
            $sql_avg = "SELECT AVG(note) AS moyenne FROM note WHERE randonnee_id = :nom";
            $stmt_avg = $bdd->prepare($sql_avg);
            $stmt_avg->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt_avg->execute();
            $row_avg = $stmt_avg->fetch(PDO::FETCH_ASSOC);
            $moyenne = $row_avg['moyenne'];

            $sql = "UPDATE randonnees SET note = :moyenne WHERE nom = :nom";
            $stmt = $bdd->prepare($sql);
            $stmt->bindParam(':moyenne', $moyenne);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}
//Nombres de votants
$sql_nmb = "SELECT randonnee_id, COUNT(*) AS nombre_votants FROM note GROUP BY randonnee_id";
$stmt_n = $bdd->query($sql_nmb);
while ($row_n = $stmt_n->fetch(PDO::FETCH_ASSOC)) {
    if ($row_n['randonnee_id'] == $nom) {
        $nmb_votant = $row_n['nombre_votants'];
    }
}
//Déconnexion
if (isset($_POST['deconnecter'])) {
    session_destroy();
    header("Location: accueil.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>
        <?php echo $nom; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="styles/pageRando.css" />

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
            <li><a href="contribuer.php">Contribuer</a></li>
        </ul>
    </div>
    <div class="centre">
        <?php
        if (isset($message_n)) {
            echo $message_n;
        }
        ?>
        <main>
            <div id="titre">
                <?php echo $nom; ?>
            </div>
            <ul><img id="image" src="img_BD/<?php echo $row['image']; ?>" /></ul>
            <section>
                <p id="description">Description</p>
                <table>
                    <tr>
                        <td colspan="2">
                            <span>Distance <img class="petitImg" src="imgdoc/distance.png" /> :</span>
                            <?php echo $row['km']; ?>km
                        </td>
                        <td colspan="2"><span>Dénivelé Positif <img class="petitImg" src="imgdoc/deniveleP1.png" />
                                :</span>
                            <?php echo $row['deniveleP']; ?>m
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><span>Durée <img class="petitImg" src="imgdoc/duree1.png" /> :</span>
                            <?php echo $row['duree']; ?>
                        </td>
                        <td colspan="2"><span>Dénivelé Négatif <img class="petitImg" src="imgdoc/deniveleN.png" />
                                :</span>
                            <?php echo $row['deniveleN']; ?>m
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><span>Difficulté <img class="petitImg" src="imgdoc/difficulte1.png" /> :</span>
                            <?php echo $row['difficulte']; ?>
                        </td>
                        <td colspan="2"><span>Score
                                <?php
                                if ($nmb_votant == 1) {
                                    echo "(" . $nmb_votant . " vote)";
                                } else {
                                    echo "(" . $nmb_votant . " votes)";
                                }
                                ?><img class="petitImg" src="imgdoc/score.png" /> :
                            </span>
                            <?php
                            //on récupère la moyenne des notes.
                            $nom = $_GET['nom'];
                            $sql_avg = "SELECT AVG(note) AS moyenne FROM note WHERE randonnee_id = :nom";
                            $stmt_avg = $bdd->prepare($sql_avg);
                            $stmt_avg->bindParam(':nom', $nom, PDO::PARAM_STR);
                            $stmt_avg->execute();
                            $row_avg = $stmt_avg->fetch(PDO::FETCH_ASSOC);

                            $moyenne = $row_avg['moyenne'];
                            if ($moyenne == floor($moyenne)) {
                                // La moyenne est un nombre entier
                                $moyenne = number_format($moyenne, 0);
                            } else {
                                // La moyenne a des décimales
                                $moyenne = number_format($moyenne, 2);
                            }

                            echo $moyenne; ?>/5
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"><span>Adresse <img class="petitImg" src="imgdoc/adresse.png" /> :</span>
                            <?php echo $row['adresse']; ?>
                        </td>
                    </tr>
                </table>
                <div id="textD">
                    <?php echo nl2br($row['description']); //On garde les sauts de lignes avec nl2br.?>
                </div>
            </section>

    </div>
    <div class="centre" id="note">
        <span>Votre feedback est précieux pour nous !</span>

        <form action="" method="POST">

            <label for="note">Votre note :</label>
            <div class="rating">
                <input type="radio" id="star5" name="note" value="5" />
                <label for="star5" title="5">★</label>
                <input type="radio" id="star4" name="note" value="4" />
                <label for="star4" title="4">★</label>
                <input type="radio" id="star3" name="note" value="3" />
                <label for="star3" title="3">★</label>
                <input type="radio" id="star2" name="note" value="2" />
                <label for="star2" title="2">★</label>
                <input type="radio" id="star1" name="note" value="1" />
                <label for="star1" title="1">★</label>
            </div>
            <input type="submit" name="ajout_note" value="Noter" class="btn" />
        </form>
    </div>
    </main>


    </div>
</body>

</html>
