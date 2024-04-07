<?php

    // Connexion à la base de données MySQL
    $connexion = new mysqli("localhost", "admin", "admin", "calculatrice", 3306);
    if($connexion->connect_error)
    {
        die("Erreur de connexion");
    }


    // Fonctions pour les opérations arithmétiques

    // Fonction de soustraction
    function substract($num1, $num2)
    {
        return $num1 - $num2;
    }

    // Fonction d'addition
    function add($num1, $num2)
    {
        return $num1 + $num2;
    }

    // Fonction de multiplication
    function multiply($num1, $num2)
    {
        return $num1 * $num2;
    }

    // Fonction de division
    function divide($num1, $num2)
    {
        if($num2 == 0)
        {
            die("Division by zero");
        }
        return $num1 / $num2;
    }

    // Fonction pour gérer la priorité des opérations
    // les operands (1, 64 ...), les operateurs (*, - ...), le début des parenthèse, 
    // la fin des parenthèse, le nombre de parenthèse ( avant le debut du calcul
    function priority($operands, $operator, $start, $end, $nbPar)
    {
        // Tableau associatif pour compter le nombre d'opérateurs de chaque type
        $symbols = array(
            "*" => 0,
            "/" => 0,
            "+" => 0,
            "-" => 0,
        );

        // Compter le nombre d'opérateurs dans les parenthèses
        for($i = $start; $i < $end; $i++)
        {
            $symbols[$operator[$i]] += 1;
        }

        // Boucle pour effectuer les n opérations en accordant la priorité aux multiplications et divisions
        for($i = $start; $i < $end; $i++)
        {
            // Priorité aux multiplications et divisions
            if($symbols["*"] > 0 || $symbols["/"] > 0)
            {
                for($i = $start; $i < count($operator); $i++)
                {
                    // Effectuer la multiplication
                    if($symbols["*"] > 0 && $operator[$i] == "*")
                    {
                        $operands[$i-$nbPar] = multiply($operands[$i-$nbPar], $operands[$i+1-$nbPar]);
                        // Supprimer le * traitées et le 2eme operande
                        array_splice($operands, $i+1-$nbPar, 1);
                        array_splice($operator, $i, 1);
                        $symbols["*"] -= 1;
                    }
                    // Effectuer la division
                    if($symbols["/"] > 0 && $operator[$i] == "/")
                    {
                        $operands[$i-$nbPar] = divide($operands[$i-$nbPar], $operands[$i+1-$nbPar]);
                        // Supprimer le / traitées et le 2eme operande
                        array_splice($operands, $i+1-$nbPar, 1);
                        array_splice($operator, $i, 1);
                        $symbols["/"] -= 1;
                    }
                }
            }
            // Si aucune multiplication ou division, effectuer l'addition et la soustraction
            else
            {
                // Effectuer l'addition
                if($symbols["+"] > 0 && $operator[$i] == "+")
                {
                    $operands[$i-$nbPar] = add($operands[$i-$nbPar], $operands[$i+1-$nbPar]);
                    // Supprimer le + traitées et le 2eme operande
                    array_splice($operands, $i+1-$nbPar, 1);
                    array_splice($operator, $i, 1);
                    $symbols["+"] -= 1;
                    
                }
                // Effectuer la soustraction
                if($symbols["-"] > 0 && $operator[$i] == "-")
                {
                    $operands[$i-$nbPar] = substract($operands[$i-$nbPar], $operands[$i+1-$nbPar]);
                    // Supprimer le - traitées et le 2eme operande
                    array_splice($operands, $i+1-$nbPar, 1);
                    array_splice($operator, $i, 1);
                    $symbols["-"] -= 1;
                }
            }
        }
        // Retourner les opérandes et opérateurs après les opérations
        return [$operands, $operator];
    }
        

    // Récupération de l'expression depuis les données POST
    $calcul = $_POST['display'];
    // Séparation de l'expression en opérateurs et opérandes
    $split = explode(" ", $calcul);
    $operator = [];
    $operands = [];

    // Dictionnaire pour compter le nombre de parenthèses ouvrantes et fermantes
    $dictionnaire = array(
        "(" => 0,
        ")" => 0,
    );

    // Analyse de l'expression et remplissage des tableaux d'opérateurs et d'opérandes
    foreach($split as $op)
    {
        // verifier si c'est des operandes
        if(is_numeric($op))
        {
            array_push($operands, $op);
        }
        // verifier si c'est des operateurs et enlever les caractères vides
        else if($op != "")
        {
            if($op == "(")
            {
                $dictionnaire["("] += 1;
            }
            else if($op == ")")
            {
                $dictionnaire[")"] += 1;
            }
            array_push($operator, $op);
        }
    }

    // Vérification de la syntaxe de l'expression. si il y a autant d'operandes - 1 que d'operateurs
    if(count($operator) - $dictionnaire["("] - $dictionnaire[")"] != count($operands)-1)
    {
        die("syntax error");
    }
    
    // Boucle pour calculer jusqu'à ce qu'il ne reste qu'une seule operande
    while(count($operands) > 1)
    {
        $index = 0;
        $indexLast = 0;
        $nbPar = 0;

        // Si des parenthèses sont présentes, gérer les opérations à l'intérieur
        if($dictionnaire["("] > 0)
        {
            for($i=0; $i<count($operator) && $indexLast == 0; $i++)
            {
                // Recherche de la derniere parenthèse ouvrante
                if($operator[$i] == "(")
                {
                    $index = $i;
                    $nbPar += 1;
                }
                // Recherche de la première parenthèse fermante correspondante
                else if($operator[$i] == ")")
                {
                    $indexLast = $i;
                }
            }
            // Si une parenthèse fermante correspondante n'est pas trouvée ou si elle précède la parenthèse ouvrante, erreur de syntaxe
            if($indexLast == 0 || $indexLast < $index)
            {
                die("parenthese error");
            }
            // Sinon, effectuer les opérations à l'intérieur des parenthèses
            else
            {
                // Appeler la fonction de priorité pour effectuer les opérations à l'intérieur des parenthèses
                $op = priority($operands, $operator, $index+1, $indexLast, $nbPar);
                // recuperer les arrays operands et operator
                $operands = $op[0];
                $operator = $op[1];
                // Mettre à jour le dictionnaire des parenthèses
                $dictionnaire["("] -= 1;
                $dictionnaire[")"] -= 1;
                // Supprimer les parenthèses traitées des tableaux d'opérateurs
                array_splice($operator, $index +1, 1);
                array_splice($operator, $index, 1);     

                // reinitialiser les bornes
                $index = 0;
                $indexLast = 0;         
            }
        }
        // Si aucune parenthèse, continuer à évaluer les opérations
        else
        {
            // Appeler la fonction de priorité pour effectuer les opérations sans considérer les parenthèses
            $op = priority($operands, $operator, $index, count($operator), 0);
            // recuperer les arrays operands et operator
            $operands = $op[0];
            $operator = $op[1];
        }
        
    }

    // Récupérer le résultat final
    $result = $operands[0];

    // Afficher le résultat
    echo $result;

    // Insérer l'expression, le résultat et la date/heure dans la base de données
    $sql = "INSERT INTO `calculs` (`calcul`, `result`, `time`) VALUES ('$calcul', '$result', NOW());";
    $res = $connexion->query($sql);
?>
