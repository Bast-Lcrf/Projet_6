// Script mobile menu 
const menuHamburger = document.querySelector(".bx")
const navLinks = document.querySelector(".nav ul")

menuHamburger.addEventListener('click', () => {
navLinks.classList.toggle('mobile-menu')
})