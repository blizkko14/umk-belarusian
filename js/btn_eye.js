const btn = document.querySelector(".img2");
const eye_open = document.querySelector(".eye");
const eye_close = document.querySelector(".close__eye");
const password = document.querySelector(".password");
btn.addEventListener("click", () => {
  console.log("я нажала на кнопку");
  if (eye_open.classList.contains("eye__active")) {
    eye_open.classList.remove("eye__active");
    eye_close.classList.add("eye__active");
    password.setAttribute("type", "text")
  } else {
    eye_close.classList.remove("eye__active");
    eye_open.classList.add("eye__active");
    password.setAttribute("type","password")
  }
});
