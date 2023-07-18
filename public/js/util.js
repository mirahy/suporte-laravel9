const themeIcon = document.getElementById("themeIcon");
const themeStorage = window.localStorage.getItem("themeSuporte");
const sun = "img/sun.svg";
const moon = "img/moon.svg";
const container = document.getElementById("theme-container");

var theme = window.localStorage.getItem("themeSuporte");

themeIcon.src = moon;

/* verifica se o tema armazenado no localStorage é escuro
se sim aplica o tema escuro ao body */
if (themeStorage === "dark") {
    setDark();
} else {
    setLight();
}


container.addEventListener("click", setTheme);
function setTheme() {
    switch (theme) {
      case "dark":
        setLight();
        theme = "light";
        break;
      case "light":
        setDark();
        theme = "dark";
        break;
    }
}


function setLight() {
    document.body.classList.remove("dark");
    document.body.classList.add("light");
    window.localStorage.setItem("themeSuporte", "light");
    container.classList.remove("shadow-dark");
    setTimeout(() => {
        container.classList.add("shadow-light");
        themeIcon.classList.remove("change");
    }, 300);
    themeIcon.classList.add("change");
    themeIcon.src = sun;
}
function setDark() {
    document.body.classList.remove("light");
    document.body.classList.add("dark");
    window.localStorage.setItem("themeSuporte", "dark");
    container.classList.remove("shadow-light");
    setTimeout(() => {
        container.classList.add("shadow-dark");
        themeIcon.classList.remove("change");
    }, 300);
    themeIcon.classList.add("change");
    themeIcon.src = moon;
}