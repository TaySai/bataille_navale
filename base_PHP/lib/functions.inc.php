<?php 

/**
 * Création d'un plateau de jeu vierge
 * @return : Tableau à 2 dimensions (10 par 10)
 */
function initDeskGame() 
{
    for ($i=1;$i<=10;$i++) {
        for ($j=1;$j<=10;$j++) {
            $aDesk[$i][$j] = 0;
        }
    }
    return $aDesk;
}

/**
 * Ajout d'un bateau sur un plateau de jeu existant
 * $playerId : Nom de la session dans laquelle est stockée le plateau sur lequel ajouter un bateau
 * $boatLine : Case horizontale sur laquelle positionner le point de départ du bateau
 * $boatColumn : Case verticale sur laquelle positionner le point de départ du bateau
 * $boatOrientation : Orientation suivant laquelle positionner le bateau
 * $length : Nombre de cases devant occuper le bateau sur le plateau
 * @return : TRUE si le positionnement a fonctionné, FALSE s'il a échoué
 */
function addNewBoat($playerId, $boatLine, $boatColumn, $boatOrientation, $length) 
{
    // Si on ne trouve pas le plateau en session le positionnement échoue
    if (!isset($_SESSION[$playerId])) {
        return false;
    }

    // On stock le plateau enregistré en session dans une variable
    $playerDesk = $_SESSION[$playerId];

    // On place le point de départ du bateau sur le plateau
    $playerDesk[$boatColumn][$boatLine] = 2;

    // Si l'orientation choisie est horizontale
    if ($boatOrientation == 'horizontale') {
        // Calcul de l'emplacement de la dernière case que le bateau occupera
        $maxPosition = $boatLine + ($length - 1);
        // Si la case maximum occupée est supérieure à 10, elle est hors cadre et le positionnement échoue
        if ($maxPosition > 10) {
            return false;
        // Sinon on tente de positionner le bateau sur le plateau
        } else {
            // Tant que la case courante sur laquelle on place le bateau ne correspond pas à la position max occupée
            while ($boatLine < $maxPosition) {
                // On passe à la case horizontale suivante
                $boatLine += 1;
                // Si la case suivante n'est pas libre, le positionnement échoue
                if ($playerDesk[$boatColumn][$boatLine] != 0) {
                    return false;
                }
                // Si la case est libre on y dépose une partie du bateau
                $playerDesk[$boatColumn][$boatLine] = 2;
            }
        }
    // Si l'orientation choisie est verticale on effectue le même traitement mais sur la colonne et non la ligne
    } else {
        $maxPosition = $boatColumn + ($length - 1);
        if ($maxPosition > 10) {
            return false;
        } else {
            while ($boatColumn < $maxPosition) {
                $boatColumn += 1;
                if ($playerDesk[$boatColumn][$boatLine] != 0) {
                    return false;
                }
                $playerDesk[$boatColumn][$boatLine] = 2;
            }
        }
    }

    // Mise à jour du plateau de jeu dans la variable de session
    $_SESSION[$playerId] = $playerDesk;

    // On retourne une information indiquant que le positionnement a fonctionné
    return true;
}
/***
 * @param $fleet array 2 dimensions
 * @param $line integer
 * @param $column integer
 * @return bool
 * if the value is a boat it turns red, else it turns gray
 */
function doesItTouch(&$fleet, $line, $column, $player) {
    switch ($fleet[$line][$column]) {
        case 2:
            $fleet[$line][$column] = 3;
            $_SESSION[$player] = $fleet;
            return 'Tir réussi, un bateau touché';
            break;
        case 0:
            $fleet[$line][$column] = 1;
            $_SESSION[$player] = $fleet;
            return 'Tir manqué, emplacement vide';
            break;
        default:
            return 'Tir manqué, empalcement déjà attaqué';
    }
}

/***
 * @param $fleet: array 2 dimensions
 * @param $lenght_fleet: integer
 * @return int
 * count number of fleet
 */
function countFleet($fleet, $lenght_fleet) {
    $counter = 0;
    for ($i=1;$i<=$lenght_fleet;$i++){
        for ($j=1;$j<=$lenght_fleet;$j++){
            if ($fleet[$i][$j] == 2){
                $counter++;
            }
        }
    }
    return $counter;
}
?>