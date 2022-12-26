// Javascript scrollUp button

// Récupère le bouton
let myButton = document.getElementById('scrollTopBtn');

// Afficher le bouton quand l'utilisateur scroll vers le bas de 300 pixels
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        myButton.style.display = "block";
    } else {
        myButton.style.display = "none";
    }
}

// Scroll vers le haut de la page quand l'utilisateur clique sur le bouton
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}