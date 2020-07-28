<?php
require_once 'vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true,
    ]);
    $twig->addExtension(new \Twig\Extension\DebugExtension());
    
// Pagination
require 'back/pagination.php';

// Tri par catégorie
require 'back/filtre.php';

// Liste des catégories
function categories(){
    require 'back/bdd.php';

    $sql = 'SELECT categorie FROM categories';
    $req = $bdd -> query($sql);
    return $req;
}

// Liste des produits
function produits_list(){
    // Récupère le le numéro de page et la limite pour afficher les produits
    require 'back/pagination.php';

    // Récupère la valeur de $select_categorie
    require 'back/filtre.php';

    if($select_categorie == 'Choisissez une catégorie'){
        $sql = 'SELECT p.id, p.nom, p.reference, p.prix, p.date_achat, p.date_fin_garantie, c.categorie FROM produits AS p INNER JOIN categories AS c ON p.id_categorie = c.id ORDER BY id  ASC LIMIT :debut, :limit';

        $req = $bdd -> prepare($sql);

    } else{
        $sql = 'SELECT p.id, p.nom, p.reference, p.prix, p.date_achat, p.date_fin_garantie, c.categorie FROM produits AS p INNER JOIN categories AS c ON p.id_categorie = c.id WHERE c.categorie = :categorie ORDER BY id  ASC LIMIT :debut, :limit';

        $req = $bdd -> prepare($sql);
        $req -> bindValue('categorie', $select_categorie, PDO::PARAM_STR);
    }

    $req -> bindValue('debut', $debut, PDO::PARAM_INT);
    $req -> bindValue('limit', $limit, PDO::PARAM_INT);
    $req -> execute();

    return $req;
}

$template = $twig->load('base.html.twig');
echo $template->render(array(
    'user' => 'Admin',
    'categorieTitle' => $select_categorie,
    'categories' => categories(),
    'produits_list' => produits_list(),
    'nombre_page' => $nombre_page,
    'page_courante' => $current_page,
));