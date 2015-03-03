<?php
include_once('Ganon/ganon.php');

header('Content-Type: text/javascript; charset=UTF-8');

if (isset($_GET['page']))
{
    $page = $_GET['page'];
}
else
{
    $page = 1;
}

$mot_cle_brut = trim($_GET["recherche"]);
$mot_cle = wd_remove_accents($mot_cle_brut);

//$mot_cle = "cookies";

$arrayResults = array ();

$cuisineazURL = 'http://www.cuisineaz.com/recettes/recherche_v2.aspx?recherche='. $mot_cle;
$septcentcinquantegURL = 'http://www.750g.com/recettes_'. $mot_cle .'.htm?page='. $page;
$marmitonURL = 'http://www.marmiton.org/recettes/recherche.aspx?aqt='. $mot_cle .'';

// CuisineAZ

/*$cuisineaz = file_get_dom($cuisineazURL);

$siteSource = "cuisineaz";

foreach($cuisineaz('div[id="divRecette", class="rechRecette"]') as $element) {

    foreach($element('a:first-child') as $lienRecette) {
        $urlRecette = $lienRecette->href;
    }

    $nbEtoile=0;

    foreach($element('span img') as $lienImage) {

        foreach($lienImage->attributes as $lienImage => $value) {
            if (($lienImage == "src" || $lienImage == "data-src") && $value != "http://images.cuisineaz.com/1x1-pixel.png") {
                $urlImage = $value;
            }
        }
    }

    foreach($element('h2') as $titreRecette) {
        $titreRecette = $titreRecette->getPlainText();
    }

    foreach($element('div[class="cazicon cazicon-star2"]') as $calculNoteRecette) {
        $nbEtoile++;
    }
    foreach($element('div[class="cazicon cazicon-demistar2"]') as $calculNoteRecette) {
        $nbEtoile+=0.5;
    }

    foreach($element('[class="rechCom"]') as $nbCommentairesRecette) {
        $nbCommentaires = $nbCommentairesRecette->getPlainText();
    }
    if ($nbCommentaires == " ") {
        $nbCommentaires = 0;
    }

    $arrayResults[] = array('SiteSource' => $siteSource,
        'LienRecette' => $urlRecette,
        'URLImage' => $urlImage,
        'TitreRecette' => $titreRecette,
        'NbCommentaires' => $nbCommentaires,
        'NbEtoiles' => $nbEtoile,
        'ScoreGlobal' => score_global($nbEtoile, $nbCommentaires));
}*/


// 750 grammes


$siteSource = "750grammes";

$septcentcinquanteg = file_get_dom($septcentcinquantegURL);

foreach($septcentcinquanteg('li[data-type="recette", data-type="video"]') as $element) {

    foreach($element('div[class="small-3 columns image"] > a') as $lienRecette) {
        $urlRecette = "http://www.750g.com/".$lienRecette->href;
    }

    foreach($element('div[class="small-3 columns image"] > a > img') as $imageURL) {
        if(substr($imageURL->src,0,28) != 'http://img.750g.com/750g_v2/') {
            $urlImage = $imageURL->src;
        }
    }

    foreach($element('h2') as $titreRecette) {
        $titreRecette = $titreRecette->getPlainText();
    }

    foreach($element('div[class="vote_txt"]') as $nbEtoilesRecette) {
        $nbEtoile = substr($nbEtoilesRecette->getPlainText(), 0, strpos($nbEtoilesRecette->getPlainText(), "/"));
    }

    $nbCommentaires = 0;
    foreach($element('div[class="comments"]') as $nbCommentairesRecette) {
        $nbCommentaires = str_replace("commentaires", "", $nbCommentairesRecette->getPlainText());
    }

    $arrayResults[] = array(
        'author' => $siteSource,
        'url' => $urlRecette,
        'image' => $urlImage,
        'title' => $titreRecette,
        'nbComments' => $nbCommentaires,
        'nbStars' => $nbEtoile,
        'score' => score_global($nbEtoile, $nbCommentaires)
    );
}


/*$siteSource = "marmiton";

$marmiton = file_get_dom($marmitonURL);

foreach($marmiton('div[class="m_search_result"]') as $element) {

    foreach($element('a') as $lienRecette) {
        $urlRecette = "http://www.marmiton.org".$lienRecette->href;
    }


    foreach($element('script') as $node) {
        $urlImage = $node->getInnerText();
    }

    foreach($element('div[class="m_search_titre_recette"] > a') as $titreRecette) {
        $titreRecette = $titreRecette->getPlainText();
    }

    $nbEtoile = 0;

    foreach($element('div[class="etoile1"] ') as $nbEtoilesRecette) {
        $nbEtoile++;
    }

    foreach($element('div[class="m_search_nb_votes"]') as $nbCommentairesRecette) {
        $nbCommentaires = str_replace(" votes)", "", $nbCommentairesRecette->getPlainText());
        $nbCommentaires = str_replace("(", "", $nbCommentaires);
    }

    $arrayResults[] = array('SiteSource' => $siteSource,
        'LienRecette' => $urlRecette,
        'URLImage' => $urlImage,
        'TitreRecette' => $titreRecette,
        'NbCommentaires' => $nbCommentaires,
        'NbEtoiles' => $nbEtoile,
        'ScoreGlobal' => score_global($nbEtoile, $nbCommentaires));
}*/

// tri du tableau par score
// $arrayResultsSorted = array_msort($arrayResults, array('score'=>SORT_DESC));

print json_encode($arrayResults);

function wd_remove_accents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caract�res
    $str= str_replace(" ", "_", $str);
    $str=str_replace(array(
        '�',
        '�',
        '�',
        '�',
        '�',
        '!',
        '?',
        '"',
        '/',
        '\\',
        ':',
        '#',
        '@',
        '�',
        '.',
        ',',
        '%',
        '<',
        '>',
        '+',
        '=',
        '�',
        '*',
        '�',
        '`',
        '�'
    ),'',$str);

    return $str;
}

function score_global($nbEtoile, $nbCommentaires)
{
    if ($nbEtoile > 4) {
        $scoreGlobal = 70*$nbEtoile+30*$nbCommentaires;
    } elseif ($nbEtoile > 3) {
        $scoreGlobal = 70*$nbEtoile+30*$nbCommentaires;
    } elseif ($nbEtoile > 2) {
        $scoreGlobal = 70*$nbEtoile+30*$nbCommentaires;
    } elseif ($nbEtoile > 1) {
        $scoreGlobal = 70*$nbEtoile+30*$nbCommentaires;
    } else {
        $scoreGlobal = 70*$nbEtoile+30*$nbCommentaires;
    }
    return $scoreGlobal;
}

function array_msort($array, $cols) {
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }

    $eval = 'array_multisort(';

    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }

    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }

    return $ret;
}