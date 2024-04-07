<?php
    // Connexion à la base de données MySQL
    $connexion = new mysqli("localhost", "admin", "admin", "calculatrice", 3306);
    // Vérifier si la connexion échoue
    if($connexion->connect_error)
    {
        // Afficher un message d'erreur et arrêter l'exécution du script
        die("Erreur de connexion");
    }
    
    // Requête SQL pour sélectionner toutes les lignes de la table "calculs"
    $sql = "SELECT * FROM calculs ;";
    // Exécution de la requête SQL
    $res = $connexion->query($sql);

    // Affichage du début du tableau HTML avec les en-têtes
    echo "<tr><th>expression</th><th>resultat</th><th>date</th></tr>";

    // Boucle à travers les résultats de la requête SQL
    foreach( $res as $row ) {
        // Affichage des données de chaque ligne dans une nouvelle ligne du tableau HTML
        echo "<tr><td>{$row['calcul']}</td> <td>{$row['result']}</td> <td>{$row['time']}</td></tr>";
    }
?>
