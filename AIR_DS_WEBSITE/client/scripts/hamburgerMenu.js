const hamburger = document.getElementById('hamburger');
const nav = document.querySelector('nav');

hamburger.addEventListener('click', () => {
    hamburger.style.display = "none";
    nav.style.display = "flex";
});

document.addEventListener("click", (e) => {
    if (window.innerWidth <= 800 && nav.style.display === 'flex'
        && e.target !== hamburger
    ) {
        hamburger.style.display = "block";
        nav.style.display = "none";
    }
})