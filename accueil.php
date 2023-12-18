<?php
session_start();
//Déconnexion
if (isset($_POST['deconnecter'])) {
  session_destroy();
  header("Location: accueil.php");
  exit();
}
//Lorsque l'utilisateur a ajouté une randonnée, il sera redirigé sur la page de la rando, puis je supprime les variables globales pour pouvoir retourner sur acceuil.
if (isset($_SESSION['ajout'])) {
  header("Location: pageRando.php?nom=" . $_SESSION['nom_rando']);
  unset($_SESSION['ajout']);
  unset($_SESSION['nom_rando']);
  exit(); 
}
try { //Test pour vérifier si la connexion avec la base de données fonctionne. Ensuite, on trie les données en fonction de la condition choisie par l'utilisateur. 
  //Si aucun choix n'a été fait, on les trie par ordre alphabétique.
  $bdd = new PDO("mysql:host=localhost;dbname=ExempleBD", "root", "");
  $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  if (isset($_GET['valider'])) {
    $_SESSION['tri'] = $_GET['tri'];
    if ($_SESSION['tri'] === 'alphabetique') {
      $sql = "SELECT * FROM randonnees ORDER BY nom ";
    } elseif ($_SESSION['tri'] === 'score') {
      $sql = "SELECT * FROM randonnees ORDER BY note DESC";
    } elseif ($_SESSION['tri'] === 'departement') {
      $sql = "SELECT * FROM randonnees ORDER BY departement ";
    }
  } else {
    $_SESSION['tri']= 'alphabetique';
    $sql = "SELECT * FROM randonnees ORDER BY nom ";
  }
  $result = $bdd->query($sql);
} catch (PDOException $e) {
  echo "<p class='erreur'>Erreur : " . $e->getMessage() . "</p>";
}
?>
<!DOCTYPE html>
<html lang="en">  
<head>
  <meta charset="UTF-8" />
  <title>Accueil</title>
  <link rel="stylesheet" type="text/css" href="styles/accueil.css" />
</head> 
<body>
  <div id="barre"> 
    <?php //Si l'utilisateur est connecté, on affiche son nom d'utilisateur et un bouton pour se déconnecter, sinon il y a un lien pour pouvoir se connecter.
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
      <li><a href="accueil.php" class="actuel">Accueil</a></li>
      <li><a href="contribuer.php">Contribuer</a></li> 
    </ul>
  </div> 
  <div class="centre"> 
    <main> 
      <div id="desc">
        <span>Bienvenue sur mon site dédié aux passionnés de randonnée dans la région Auvergne-Rhône-Alpes ! Vous y
          trouverez une multitude de parcours adaptés à tous les niveaux, que vous soyez débutant ou randonneur
          chevronné.</span>
        <span>Explorez nos itinéraires soigneusement sélectionnés, offrant des paysages époustouflants, des sentiers
          pittoresques et des points de vue à couper le souffle. Des balades tranquilles en pleine nature aux défis plus
          audacieux en montagne, notre site regorge d'inspiration pour votre prochaine aventure. </span>
        <span>Chaque parcours est accompagné d'informations détaillées, telles que la durée, la difficulté, le dénivelé
          positif et négatif, ainsi que des photos pour vous donner un aperçu de ce qui vous attend. De plus, vous avez
          la possibilité de contribuer en ajoutant vos propres parcours préférés, afin de partager vos découvertes avec
          notre communauté de passionnés.</span>
        <span>Que vous soyez à la recherche d'une escapade tranquille le week-end, d'une randonnée en famille ou d'un
          défi personnel, notre site est là pour vous guider et vous offrir une expérience de randonnée inoubliable dans
          la magnifique région Auvergne-Rhône-Alpes.</span>
        <span>Préparez-vous à lacer vos chaussures, à prendre votre sac à dos et à vous aventurer sur les sentiers ! Que
          l'exploration commence !</span>
      </div>
      <h1 id="titre">Liste Randonnées</h1> 
      <form method="get">
    <label for="tri">Trier par :</label>
    <select id="tri" name="tri">
        <?php //Si l'utilisateur a choisi de trier, on affiche d'abord la condition de tri, et j'ai choisi d'utiliser $_SESSION['tri'] pour que,
        // lorsque l'utilisateur revient sur la page d'accueil, la même condition de tri soit toujours présente.
        if (isset($_GET['valider'])) {
            if ($_SESSION['tri'] === 'score') {
                ?>
                <option value="alphabetique">Ordre alphabétique</option>
                <option value="score" selected>Score</option>
                <option value="departement">Département</option>
                <?php
            } else if ($_SESSION['tri'] === 'departement') {
                ?>
                <option value="alphabetique">Ordre alphabétique</option>
                <option value="score">Score</option>
                <option value="departement" selected>Département</option>
                <?php
            } else {
                ?>
                <option value="alphabetique" selected>Ordre alphabétique</option>
                <option value="score">Score</option>
                <option value="departement">Département</option>
                <?php
            }
        } else {
            ?>
            <option value="alphabetique">Ordre alphabétique</option>
            <option value="score">Score</option>
            <option value="departement">Département</option>
            <?php
        }
        ?>
    </select>
    <input type="submit" name="valider" value="Valider" class="btn">
</form>
      <?php 
        if ($result->rowCount() > 0) { //Si il y a des randonnées, on parcours chaque lignes de la base de données. Si le variable tri est département, on affiche
          // une fois le nom du département 
          $dep = "";
          foreach ($result as $row) {
            if ($_SESSION['tri'] === 'departement') {
              if ($dep !== $row['departement']) {
                $dep = $row['departement'];
                echo "<div class='departement'>" . $dep . "</div>";
              }
            }
            echo "<ul>";
            echo "<li><a href='pageRando.php?nom=" . urlencode($row['nom']) . "' class='nom-randonnee'>" . $row['nom'] . "</a></li>"; //J'ai utilisé urlencode pour ne pas avoir de problemes avec les caractères spéciaux
            echo "<img class='image' src='img_BD/" . $row['image'] . "'/>";
            echo "<div class='point-depart'>Adresse du point de départ : " . $row['adresse'] . "</div>";
            echo "</ul>";
          }
        } else {
          echo "Aucun résultat trouvé.";
        } 
      ?>
    </main>
  </div>
</body>

</html>
