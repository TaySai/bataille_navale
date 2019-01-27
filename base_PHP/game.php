<?php 
/**
 * Page de jeu au tour par tour
 * (étape d'enchainement des attaques et affichage du résultat)
 */
// Ouverture d'une session
session_start();
// On inclu la librairie de fonctions créée dans le cadre du projet
include('lib/functions.inc.php');

// catch the both fleet human and virtual
$human_player = $_SESSION['human_player'];
$virtual_player = $_SESSION['virtual_player'];
// count the array's length one time and stock the variable (cuz we will use it many times, better if we save the number somewhere)
$length_virtual_player = count($human_player);


// we suppose that  there is no problem by putting $form_error = false, if we find any prob it becomes true
$form_error = false;
// Test to see if there is no problem, we put it in a condition $_POST['submit_form'] cuz the person must submit first
if (isset($_POST['submit_form'])) {
    // catch submit form
    $shot_line = $_POST['shot_line'];
    $shot_column = $_POST['shot_column'];
    // check if it respect some terms
    $form_error_message = 'La configuration demandée n\'est pas réalisable pour les raisons suivantes :<br/>';
    if ( ($shot_line < 1 || $shot_line > 10) || $shot_line == null) {
        $form_error = true;
        // we remove the variable if this one doesn't respect the terms
        unset($shot_line);
        $form_error_message .= '- Les coordonnées de la ligne ne sont pas correctes<br/>';
    }
    if ( ($shot_column < 1 || $shot_column > 10) || $shot_column == null) {
        // check if it respect some terms
        $form_error = true;
        // we remove the variable if this one doesn't respect the terms
        unset($shot_column);
        $form_error_message .= '- Les coordonnées de la colonne ne sont pas correctes<br/>';
    }

    if ($form_error === false && isset($shot_line) && isset($shot_column)) {
        // test if the person miss or touch, if touch the case blue it becomes red, and miss the case become gray
        $rand_line = rand(1,10);
        $rand_column = rand(1,10);
        $message_virtual = doesItTouch($human_player, $rand_line, $rand_column, 'human_player');
        $message_human = doesItTouch($virtual_player, $shot_line, $shot_column, 'virtual_player');
    }
}
$human_fleet = countFleet($human_player, $length_virtual_player);
$virtual_fleet = countFleet($virtual_player, $length_virtual_player);
?>

<!DOCTYPE html>
<html>
    <!-- Propriétés du document HTML -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Bataille Navale</title>
        <meta name="description" content="Jeu de bataille navale à un joueur contre l'ordinateur." />
        <meta name="robots" content="noindex,nofollow" />
        <link rel="stylesheet" href="css/default.css" media="screen"/>
    </head>
    <!-- Corps du document -->
    <body>
        <h1>Bataille Navale</h1>
        <?php if( $virtual_fleet > 0 && $human_fleet > 0 ) { ?>
        <p>
            Nous y sommes <strong><?php echo $_SESSION['nickname']; ?></strong><br/>
            C'est à toi de jouer, lance des attaques contre notre adversaire.<br/>
            Bonne chance !
        </p>
        <form method="POST" action="game.php" name="shot_form">
            <p>
                <label>Coordonnées de l'attaque :</label><br/>
                <label for="shot_line">Ligne de l'attaque (1 à 10) :</label><input type="text" name="shot_line" id="shot_line" value="" />
                <label for="shot_column">Colonne de l'attaque (1 à 10) :</label><input type="text" name="shot_column" id="shot_column" value="" /><br/>
            </p>
            <p>
                <input type="submit" name="submit_form" value="Envoyer" />
            </p>
        </form>
        <?php } else { ?>
            <?php if($virtual_fleet === 0) { ?>
                    <p class="success">Félicitations <strong><?php echo $_SESSION['nickname']; ?></strong>, vous avez mené cette bataille à bien, nous avons gagné</p>
            <?php } else { ?>
                <p class="success">Dommage <strong><?php echo $_SESSION['nickname']; ?></strong>, vous avez mené cette bataille à bien, vous ferz mieux la prochaine fois</p>
             <?php } ?>
        <?php } ?>
        <?php
        // Ce bloc PHP a pour but d'afficher les potentielles erreurs présentes dans le formulaire envoyé
        if ($form_error) { echo '<p class="alert">' . $form_error_message . '</p>'; }
        ?>
        <div>
            <p>Situation de votre flotte</p>
            <table>
                <?php for($i=1; $i<=$length_virtual_player;$i++ ){ ?>
                    <tr>
                        <?php for($j=1; $j<=$length_virtual_player;$j++ ){
                            switch ($human_player[$i][$j]) {
                                case 1:
                                    $color = 'gray';
                                    break;
                                case 2:
                                    $color = 'blue';
                                    break;
                                case 3:
                                    $color = 'red';
                                    break;
                                default:
                                    $color = 'white';
                            }
                            ?>
                            <td style="background-color:<?php echo $color ?>;">
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <div>
            <p>Situation de la flotte adverse</p>
            <table>
                <?php for($i=1; $i<=$length_virtual_player;$i++ ){ ?>
                    <tr>
                        <?php for($j=1; $j<=$length_virtual_player;$j++ ){
                            switch ($virtual_player[$i][$j]) {
                                case 1:
                                    $color = 'gray';
                                    break;
                                case 2:
                                    $color = 'blue';
                                    break;
                                case 3:
                                    $color = 'red';
                                    break;
                                default:
                                    $color = 'white';
                            }
                            ?>
                            <td style="background-color:<?php echo $color ?>;">
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <?php if (isset($_POST['submit_form'])) { ?>
        <div>
            <p>Resultat de votre attaque sur l'adversaire (ligne: <?php echo $shot_line ?>, colonne : <?php echo $shot_column ?>):</p>
            <p><?php echo $message_human ?></p>
            <p>Vous disposez de <?php echo $human_fleet ?> emplacement(s) actif(s)</p>
            <br>
            <p>Resultat de l'attaque subie (ligne: <?php echo $rand_line ?>, colonne : <?php echo $rand_column ?>):</p>
            <p><?php echo $message_virtual ?></p>
            <p>Votre adversaire dispose de <?php echo $virtual_fleet ?> emplacement(s) actif(s)</p>
        </div>
        <?php } ?>
    </body>
</html>