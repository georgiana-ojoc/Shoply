fetch("../html/head.html")
    .then(response => {
        return response.text()
    })
    .then(data => {
        document.querySelector("head").innerHTML += data;
    });

let header = document.getElementsByTagName("header")[0];
let navigationBar = document.createElement("nav");
navigationBar.setAttribute("class", "navigation-bar");

let logoAnchor = document.createElement("a");
logoAnchor.setAttribute("href", "../html/index.html");
let logoLink = document.createElement("img");
logoLink.setAttribute("src", "../images/logo.png");
logoLink.setAttribute("alt", "Shoply");
logoLink.setAttribute("class", "logo");
logoAnchor.appendChild(logoLink);
navigationBar.appendChild(logoAnchor);

let searchBox = document.createElement("div");
searchBox.setAttribute("class", "search-box");
let searchLabel = document.createElement("label");
let searchText = document.createElement("input");
searchText.setAttribute("type", "text");
searchText.setAttribute("placeholder", "Cauta un produs");
searchText.setAttribute("class", "search-text");
searchLabel.appendChild(searchText);
searchBox.appendChild(searchLabel);
let searchButtonBigAnchor = document.createElement("a");
searchButtonBigAnchor.setAttribute("class", "search-button-big");
let searchButtonBigIcon = document.createElement("i");
searchButtonBigIcon.setAttribute("class", "fa fa-search");
searchButtonBigAnchor.appendChild(searchButtonBigIcon);
searchBox.appendChild(searchButtonBigAnchor);
navigationBar.appendChild(searchBox);

let smallMenu = document.createElement("div");
smallMenu.setAttribute("class", "small-menu");
let searchButtonSmallAnchor = document.createElement("a");
searchButtonSmallAnchor.setAttribute("class", "search-button-small");
let searchButtonSmallIcon = document.createElement("i");
searchButtonSmallIcon.setAttribute("class", "fa fa-search");
searchButtonSmallAnchor.appendChild(searchButtonSmallIcon);
smallMenu.appendChild(searchButtonSmallAnchor);
let hamburgerMenu = document.createElement("div");
hamburgerMenu.setAttribute("class", "hamburger-menu");
hamburgerMenu.appendChild(document.createElement("span"));
hamburgerMenu.appendChild(document.createElement("span"));
hamburgerMenu.appendChild(document.createElement("span"));
smallMenu.appendChild(hamburgerMenu);
navigationBar.appendChild(smallMenu);

let navigationBarLinks = document.createElement("div");
navigationBarLinks.setAttribute("class", "navigation-bar-links");
let bigMenu = document.createElement("ul");
bigMenu.setAttribute("class", "big-menu");
let productsElement = document.createElement("li");
let productsAnchor = document.createElement("a");
productsAnchor.setAttribute("href", "../html/products.html?category=calculatoare&sort-by=most-popular");
let productsIcon = document.createElement("i");
productsIcon.setAttribute("class", "fa fa-tags");
productsElement.appendChild(productsIcon);
productsAnchor.textContent = "Produse";
productsElement.appendChild(productsAnchor);
bigMenu.appendChild(productsElement);

let extensionElement = document.createElement("li");
let extensionAnchor = document.createElement("a");
extensionAnchor.setAttribute("href", "../html/extension.html");
let extensionIcon = document.createElement("i");
extensionIcon.setAttribute("class", "fa fa-puzzle-piece");
extensionElement.appendChild(extensionIcon);
extensionAnchor.textContent = "Extensie";
extensionElement.appendChild(extensionAnchor);
bigMenu.appendChild(extensionElement);

let accountElement = document.createElement("li");
let accountAnchor = document.createElement("a");
accountAnchor.setAttribute("href", "../php/login.php");
let accountIcon = document.createElement("i");
accountIcon.setAttribute("class", "fa fa-user");
accountElement.appendChild(accountIcon);
accountAnchor.textContent = "Contul meu";
accountElement.appendChild(accountAnchor);
bigMenu.appendChild(accountElement);
navigationBarLinks.appendChild(bigMenu);
navigationBar.appendChild(navigationBarLinks);

header.appendChild(navigationBar);

let headerLine = document.createElement("hr");
headerLine.setAttribute("class", "header-line");
header.appendChild(headerLine);

document.addEventListener("error", function () {
    document.querySelectorAll("img").forEach(img => {
        img.onerror = function () {
            this.src = "../images/broken.png";
        };
    })
});

let footer = document.getElementsByTagName("footer")[0];
let footerLine = document.createElement("hr");
footerLine.setAttribute("class", "footer-line");
footer.appendChild(footerLine);
let copyrightText = document.createElement("div");
copyrightText.setAttribute("class", "copyright-text");
footer.appendChild(copyrightText);

hamburgerMenu.addEventListener("click", () => {
    hamburgerMenu.classList.toggle("active");
    navigationBarLinks.classList.toggle("active");
});

searchButtonSmallIcon.addEventListener("click", () => {
    searchButtonSmallIcon.classList.toggle("active");
    searchBox.classList.toggle("active");
});

copyrightText.innerHTML = "&copy; Copyright " + new Date().getFullYear() + " shoply.herokuapp.com";

function scrapePathName(link) {
    let pathName = link.split(".ro/")[1].replace(/\//g, "-");
    if (pathName[pathName.length - 1] === "-") {
        pathName = pathName.slice(0, -1);
    }
    if (link.includes("compari.ro") && !link.includes("https://www.compari.ro")) {
        let doubleSlash = "//";
        return link.substring(link.indexOf(doubleSlash) + doubleSlash.length,
            link.indexOf(".compari.ro")) + "_" + pathName;
    }
    return pathName;
}

function parseTitle(title) {
    let words = title.split(/,| |-|\(|\)/);
    if (words.length <= 6) {
        return title;
    }
    let newTitle = words[0];
    for (let i = 1; i < 6; i++) {
        if (words[i] !== "cu") {
            newTitle = newTitle.concat(" " + words[i]);
        }
    }
    return newTitle;
}

function addPoint(numberString) {
    let number = numberString;
    numberString = numberString.toString();
    if (numberString.length >= 4) {
        number = numberString.substr(0, numberString.length - 3) + "." +
            numberString.substr(numberString.length - 3);
        [numberString.slice(0, numberString.length - 3), ".", numberString.slice(numberString.length - 3)].join("");
        return number;
    }
    return number;
}
