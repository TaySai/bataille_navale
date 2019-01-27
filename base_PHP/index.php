<?php 
/**
 * Page de lancement du jeu (étape de configuration de la partie)
 * Description : Cette page comprend une première partie de traitement en PHP, le but étant d'exécuter celui-ci si le formulaire est envoyé par l'utilisateur.
 * La seconde partie correspond au rendu HTML et plus particulièrement à l'affichage du formulaire de configuration de la partie.
 */

// Ouverture d'une session
session_start();

// On inclu la librairie de fonctions créée dans le cadre du projet
include('lib/functions.inc.php');

// Par défaut on considère qu'il n'y a pas d'erreurs associées au formulaire présent sur la page
$form_error = false;

// On vérifie si le formulaire a été envoyé par le joueur
if (isset($_POST['submit_form'])) {

    // Au cas où une erreur est détectée dans le fomulaire, on prépare une phrase à afficher à l'utilisateur
    $form_error_message = 'La configuration demandée n\'est pas réalisable pour les raisons suivantes :<br/>';

    // On affecte les données récupérées du formulaire dans des variables
    $nickName = $_POST['nickname'];
    $boat1Line = $_POST['boat_1_line'];
    $boat1Column = $_POST['boat_1_column'];
    $boat1Orientation = @$_POST['boat_1_orientation'];
    $boat2Line = $_POST['boat_2_line'];
    $boat2Column = $_POST['boat_2_column'];
    $boat2Orientation = @$_POST['boat_2_orientation'];
    $boat3Line = $_POST['boat_3_line'];
    $boat3Column = $_POST['boat_3_column'];
    $boat3Orientation = @$_POST['boat_3_orientation'];
    
    // Si l'utilisateur a bien renseigné un pseudonyme
    if ($nickName != null) {
        // On affecte le pseudo dans une variable de session afin de la garder en mémoire
        $_SESSION['nickname'] = $nickName;
    // Si aucun pseudo n'a été renseigné
    } else {
        // On affecte un changment de statut indiquant une erreur dans le formulaire
        $form_error = true;
        // On indique le message d'erreur explicitant cette dernière
        $form_error_message .= '- Votre pseudo est invalide<br/>';
    }
    // Si les coordonnées saisies pour le bateau 1 ne sont pas comprises entre 1 et 10 ou que l'orientation n'est pas connue
    if ( ($boat1Line < 1 || $boat1Line > 10) || ($boat1Column < 1 && $boat1Column > 10) || $boat1Orientation == null) {        
        $form_error = true;
        $form_error_message .= '- Les coordonnées du bateau 1 ne sont pas correctes<br/>';
    }
    // Si les coordonnées saisies pour le bateau 2 ne sont pas comprises entre 1 et 10 ou que l'orientation n'est pas connue
    if ( ($boat2Line < 1 || $boat2Line > 10) || ($boat2Column < 1 || $boat2Column > 10) || $boat2Orientation == null) {
        $form_error = true;
        $form_error_message .= '- Les coordonnées du bateau 2 ne sont pas correctes<br/>';
    }
    // Si les coordonnées saisies pour le bateau 3 ne sont pas comprises entre 1 et 10 ou que l'orientation n'est pas connue
    if ( ($boat3Line < 1 || $boat3Line > 10) || ($boat3Column < 1 || $boat3Column > 10) || $boat3Orientation == null) {
        $form_error = true;
        $form_error_message .= '- Les coordonnées du bateau 3 ne sont pas correctes<br/>';
    }

    // Si le formulaire ne comprend pas d'erreur jusqu'ici
    if (!$form_error) {
        // On initialise un nouveau plateau de jeu pour le joueur
        $_SESSION['human_player'] = initDeskGame();

        // On ajoute un bateau sur le plateau du joueur
        $boatAddingResult = addNewBoat('human_player', $boat1Line, $boat1Column, $boat1Orientation, 2);
        // Si une erreur a été constatée lors du positionnement du bateau on l'enregistre (de même pour les bateaux suivants)
        if (!$boatAddingResult) {
            $form_error = true;
            $form_error_message .= '- Le bateau 1 sort du plateau et/ou croise la position d\'un autre bateau<br/>';
        }
        $boatAddingResult = addNewBoat('human_player', $boat2Line, $boat2Column, $boat2Orientation, 4);
        if (!$boatAddingResult) {
            $form_error = true;
            $form_error_message .= '- Le bateau 2 sort du plateau et/ou croise la position d\'un autre bateau<br/>';
        }
        $boatAddingResult = addNewBoat('human_player', $boat3Line, $boat3Column, $boat3Orientation, 6);
        if (!$boatAddingResult) {
            $form_error = true;
            $form_error_message .= '- Le bateau 3 sort du plateau et/ou croise la position d\'un autre bateau<br/>';
        }
    }

    // Si aucune erreur n'a été repérée durant la configuration du plateau du joueur
    // On va préparer la configuration de l'ordinateur et rediriger le joueur vers la page de jeu
    if (!$form_error) {
        // On crée un plateau virtuel pour l'ordinateur
        $_SESSION['virtual_player'] = initDeskGame();

        // Tableau des deux orientations possibles
        $orientations = array('horizontale', 'verticale');
    
        // On positionne le bateau aléatoirement, et on réessaye tant qu'il y a une erreur de positionnement
        do {
            $key = array_rand($orientations);
            $boatAddingResult = addNewBoat('virtual_player', rand(1, 10), rand(1, 10), $orientations[$key], 2);
        } while(!$boatAddingResult);
        do {
            $key = array_rand($orientations);
            $boatAddingResult = addNewBoat('virtual_player', rand(1, 10), rand(1, 10), $orientations[$key], 4);
        } while(!$boatAddingResult);
        do {
            $key = array_rand($orientations);
            $boatAddingResult = addNewBoat('virtual_player', rand(1, 10), rand(1, 10), $orientations[$key], 6);
        } while(!$boatAddingResult);


        // On redirige l'utilisateur vers la page de jeu
        header("Location: game.php");
    }
    
}
?>

<!DOCTYPE html>
<html>
    <!-- Propriétés du document HTML -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Bataille Navale</title>
        <meta name="description" content="Proposition de solution concernant le projet de création d'un jeu de bataille navale." />
        <meta name="robots" content="noindex,nofollow" />
        <link rel="stylesheet" href="css/default.css" media="screen" />
    </head>
    <!-- Corps du document -->
    <body>
        <h1>Bataille Navale</h1>
        <p>
            Une bataille navale est sur le point d'éclater jeune moussaillon !<br/><br/>
            Nous avons besoin d'un capitaine pour diriger cet affrontement.<br/>
            Indique ton identité ci-dessous et choisi un emplacement stratégique pour notre flotte de bateaux.<br/>
            <i>N'oublies pas, les coordonnées d'un bateau correspondent à une position de départ et une orientation.</i>
        </p>

        <?php 
        // Ce bloc PHP a pour but d'afficher les potentielles erreurs présentes dans le formulaire envoyé
        if ($form_error) { echo '<p class="alert">' . $form_error_message . '</p>'; }
        ?>

        <!-- Formulaire de configuration du jeu par le joueur -->
        <form method="POST" action="index.php" name="registration_form">
            <!-- Champ de saisie du pseudo du joueur -->
            <p><label for="nickname">Pseudo : </label><input type="text" name="nickname" id="nickname" value="" /></p>
            <p>
                <!-- Coordonnées du bateau à positionner -->
                <!-- L'association entre la ligne et la colonne permet de déterminer une case de départ sur la plateau de jeu -->
                <!-- L'orientation quant à elle permettra d'indiquer vers où les cases suivantes seront occupées -->
                <label>Coordonnées du premier bateau (2 cases) :</label><br/>
                <label for="boat_1_line">Position en ligne (1 à 10) :</label><input type="text" name="boat_1_line" id="boat_1_line" value="" />
                <label for="boat_1_column">Position en colonne (1 à 10) :</label><input type="text" name="boat_1_column" id="boat_1_column" value="" /><br/>
                <label>Horizontale : </label><input type="radio" name="boat_1_orientation" value="horizontale" checked>
                <label>Verticale : </label><input type="radio" name="boat_1_orientation" value="verticale">
            </p>
            <p>
                <label>Coordonnées du deuxièmre bateau (4 cases) :</label><br/>
                <label for="boat_2_line">Position en ligne (1 à 10) :</label><input type="text" name="boat_2_line" id="boat_2_line" value="" />
                <label for="boat_2_column">Position en colonne (1 à 10) :</label><input type="text" name="boat_2_column" id="boat_2_column" value="" /><br/>
                <label>Horizontale : </label><input type="radio" name="boat_2_orientation" value="horizontale" checked>
                <label>Verticale : </label><input type="radio" name="boat_2_orientation" value="verticale">
            </p>
            <p>
                <label>Coordonnées du troisième bateau (6 cases) :</label><br/>
                <label for="boat_3_line">Position en ligne (1 à 10) :</label><input type="text" name="boat_3_line" id="boat_3_line" value="" />
                <label for="boat_3_column">Position en colonne (1 à 10) :</label><input type="text" name="boat_3_column" id="boat_3_column" value="" /><br/>
                <label>Horizontale : </label><input type="radio" name="boat_3_orientation" value="horizontale" checked>
                <label>Verticale : </label><input type="radio" name="boat_3_orientation" value="verticale">
            </p>
            <p>
                <input type="submit" name="submit_form" value="Envoyer" />
            </p>
        </form>
    </body>
</html>