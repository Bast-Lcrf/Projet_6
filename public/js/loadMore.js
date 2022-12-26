const loadMore = document.querySelector('.load_more');

let currentItem = 6;

loadMore.addEventListener('click', (e) => {
    const elementList = [...document.querySelectorAll('.tricks_home .card')];
    e.target.classList.add('show-loader');

    for(let i = currentItem; i < currentItem + 3; i++) {
        setTimeout( function() {
            e.target.classList.remove('show-loader');
            if(elementList[i]) {
                elementList[i].style.display = 'block';
            }
        }, 3000)
    }
    currentItem += 3;

    // On cache le bouton si tous les élements sont chargés
    if(currentItem >= elementList.length) {
        event.target.classList.add('loaded')
    }
})