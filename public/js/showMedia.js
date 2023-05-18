// Script afficher / cacher les médias sur mobile et tablette
const show = document.querySelector('.show_media')
const media = document.querySelector('.tricks_media')
const hide = document.querySelector('.hide_media')

show.addEventListener('click', () => {
    media.style.display = "block"
    show.style.display = "none"
    hide.style.display = "block"
})

hide.addEventListener('click', () => {
    media.style.display = "none"
    show.style.display = "block"
    hide.style.display = "none"
})