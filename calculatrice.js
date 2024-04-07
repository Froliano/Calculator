over = false;

function addToDisplay(value) 
{
    var display = document.getElementById('display');
    if(["+","-", "*", "/", "(", ")"].includes(value))
    {
        if(over)
        {
            over = false;
        }
        display.value += " " + value + " ";
    }
    else
    {
        if(over)
        {
            clearDisplay();
            over = false;
        }
        display.value += value;
    }
}

function clearDisplay() {
    document.getElementById('display').value = '';
}

// Écoute des événements de pression de touche dans l'ensemble du document
document.addEventListener('keydown', function(event) 
{
    // Récupère le code de la touche pressée
    var key = event.key;
    // Vérifie si la touche pressée est un chiffre ou un opérateur
    if (!isNaN(key) || ['+', '-', '*', '/', '.', '(', ')'].includes(key)) 
    {
        // Si oui, ajoute le caractère au champ de texte
        addToDisplay(key);
    } 
    else if (key === 'Enter') 
    {
        $("#array_form").submit();
    }
    else if (key == "c")
    {
        clearDisplay();
    }
});

document.addEventListener("DOMContentLoaded", function() {
    // Code à exécuter lorsque le site est chargé
    OnLoad();
});

function OnLoad() {
    $.ajax({
        type: "POST",
        url: "historique.php",
        data: {param1: 'valeur1', param2: 'valeur2'},
    }).done(function(data) { 
        $("#history").html(data);
        });
}

document.addEventListener("DOMContentLoaded", function() {
    var table = document.getElementById("history");
    var inputBar = document.getElementById("display");

    table.addEventListener("click", function(event) {
        var elementClique = event.target;
        var tr = elementClique.closest("tr").firstChild; 
        inputBar.value = tr.innerHTML;
    });
});

$(document).ready(function() {
    $("#array_form").submit(function(event) {
        event.preventDefault();
        over = true;
    $.ajax({
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        data: $(this).serialize(),
    }).done(function(data) { 
        $("#display").val(data);
        OnLoad();
        });
    });
});