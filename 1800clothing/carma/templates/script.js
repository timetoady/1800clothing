{/* <header class="header">
  <a href="" class="logo">CSS Nav</a>
  <input class="menu-btn" type="checkbox" id="menu-btn" />
  <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
  <ul class="menu">
    <li><a href="#work">Our Work</a></li>
    <li><a href="#about">About</a></li>
    <li><a href="#careers">Careers</a></li>
    <li><a href="#contact">Contact</a></li>
  </ul>
</header> */}

let menuButton = document.querySelector(".menu-btn")
let menuIcon = document.querySelector(".navicon")
let menu = document.querySelector(".menu")

// menuButton.addEventListener("click", () => {
//    menu.style.display = "block"
//   console.log("Clicked!")
// })

// menuIcon.addEventListener("click", () => {
//   menu.style.display = "none" ? menu.style.display = "block" : menu.style.display = "none"
//   console.log("Clicked!")
// })

menuButton.addEventListener('click', (e) => {
   if (window.getComputedStyle(menu).display === 'block') {
    menu.style.display = 'none';
  } else  {
    menu.style.display = 'block';
  }
});