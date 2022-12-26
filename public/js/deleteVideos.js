window.onload = () => {
    // Gestion des liens supprimer
    let links = document.querySelectorAll('[data-delete-video]')

    // On boucle sur links
    for( link of links) {
        // On ecoute le click
        link.addEventListener("click", function(e){
            // On empêche la navigation
            e.preventDefault()

            //On demande confirmation
            if(confirm('Voulez-vous supprimer cette video ?')) {
                // On envoie une requête Ajax vers le href du lien avec la methode DELETE
                fetch(this.getAttribute('href'), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-with": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // On récupère la response en JSON
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}