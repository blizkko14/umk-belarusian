const cross = document.querySelector(".menu__burger");
const menu = document.querySelector(".menu__header");
console.log(cross);
cross.addEventListener("click", () => {
     cross.classList.toggle("menu__burger__active");
  menu.classList.toggle("menu__header__burger__active");
  if (cross.classList.contains("menu__burger__active")) {
    document.body.style.overflow = "hidden";
    console.log("Отработал if");
    return null;
  }
  console.log("!if");
  document.body.style.overflow = "auto";
  //   if (cross.classList.contains("menu__burger__active")) {
  //     cross.classList.remove("menu__burger__active");
  //     menu.classList.remove("menu__header__burger__active");
  //     document.body.style.overflow = "auto"
  //   } else {
  //     cross.classList.add("menu__burger__active");
  //     menu.classList.add("menu__header__burger__active");
  //     document.body.style.overflow = "hidden"
  //   }
});
