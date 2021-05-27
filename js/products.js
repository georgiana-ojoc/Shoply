let parameters = new URLSearchParams(window.location.search);
let category;
if (parameters.has("category")) {
    category = parameters.get("category");
} else {
    category = "calculatoare";
}
let sortBy;
if (parameters.has("sort-by")) {
    sortBy = parameters.get("sort-by");
} else {
    sortBy = "most-popular";
}

const selectedOption = document.getElementsByClassName("selected-option")[0];
let arrowOptions = document.createElement("div");
arrowOptions.setAttribute("class", "arrow");
selectedOption.textContent = document.getElementsByClassName(sortBy)[0].textContent;
selectedOption.appendChild(arrowOptions);
const options = document.getElementsByClassName("options")[0];
selectedOption.addEventListener("click", () => {
    options.classList.toggle("active");
    arrowOptions.classList.toggle("active");
});
const optionList = document.querySelectorAll(".option");
optionList.forEach(option => {
    option.addEventListener("click", () => {
        selectedOption.textContent = option.getElementsByTagName("label")[0].textContent;
        arrowOptions = document.createElement("div");
        arrowOptions.setAttribute("class", "arrow");
        selectedOption.appendChild(arrowOptions);
        options.classList.remove("active");

        sortBy = option.getElementsByTagName("input")[0].id;
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname +
            "?category=" + category + "&sort-by=" + sortBy;
        window.history.pushState({path: newUrl}, "", newUrl);

        productsRequest = new XMLHttpRequest();
        productsRequest.onreadystatechange = addProducts;
        productsRequest.open("GET", "../php/controllers/products_controller.php?category="
            + category + "&sort-by=" + sortBy, true);
        productsRequest.send();
    });
});

let categoriesRequest = new XMLHttpRequest();
categoriesRequest.onreadystatechange = addCategories;
categoriesRequest.open("GET", "../php/controllers/products_controller.php?categories=true", true);
categoriesRequest.send();

function addCategories() {
    if (this.readyState === categoriesRequest.DONE && this.status === 200) {
        let categoriesElement = document.getElementsByClassName("categories")[0];
        let categoryObjects = JSON.parse(this.responseText);
        for (let index = 0; index < categoryObjects.length; ++index) {
            let categoryObject = categoryObjects[index];
            let categoryElement = document.createElement("li");
            categoryElement.setAttribute("class", "category");
            let newCategory = categoryObject.category;
            categoryElement.textContent = newCategory[0].toUpperCase() + newCategory.slice(1);
            categoriesElement.appendChild(categoryElement);

            categoryElement.addEventListener("click", () => {
                selectedCategory.textContent = categoryElement.textContent;
                arrowCategories = document.createElement("div");
                arrowCategories.setAttribute("class", "arrow");
                selectedCategory.appendChild(arrowCategories);
                categories.classList.remove("active");

                category = selectedCategory.textContent.toLowerCase();
                sortBy = "most-popular";

                selectedOption.textContent = "Cele mai populare";
                arrowOptions = document.createElement("div");
                arrowOptions.setAttribute("class", "arrow");
                selectedOption.appendChild(arrowOptions);

                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname +
                    "?category=" + category + "&sort-by=" + sortBy;
                window.history.pushState({path: newUrl}, "", newUrl);

                productsRequest = new XMLHttpRequest();
                productsRequest.onreadystatechange = addProducts;
                productsRequest.open("GET", "../php/controllers/products_controller.php?category="
                    + category + "&sort-by=" + sortBy, true);
                productsRequest.send();
            });
        }

        let selectedCategory = document.getElementsByClassName("selected-category")[0];
        selectedCategory.textContent = category[0].toUpperCase() + category.slice(1);
        let arrowCategories = document.createElement("div");
        arrowCategories.setAttribute("class", "arrow");
        selectedCategory.appendChild(arrowCategories);
        let categories = document.getElementsByClassName("categories")[0];
        selectedCategory.addEventListener("click", () => {
            categories.classList.toggle("active");
            arrowCategories.classList.toggle("active");
        });
    }
}

let productsRequest = new XMLHttpRequest();
productsRequest.onreadystatechange = addProducts;
productsRequest.open("GET", "../php/controllers/products_controller.php?category=" + category +
    "&sort-by=" + sortBy, true);
productsRequest.send();

function addProducts() {
    if (this.readyState === productsRequest.DONE && this.status === 200) {
        let productsElement = document.getElementsByClassName("products")[0];
        if (typeof productsElement !== "undefined") {
            productsElement.parentNode.removeChild(productsElement);
        }
        productsElement = document.createElement("div");
        productsElement.setAttribute("class", "products");
        let container = document.getElementsByClassName("container")[0];
        container.appendChild(productsElement);
        let productObjects = JSON.parse(this.responseText);
        let productObject;
        for (productObject of productObjects) {
            let productElement = document.createElement("div");
            productElement.setAttribute("class", "product");
            let productImageAnchor = document.createElement("a");
            productImageAnchor.setAttribute("href", "../html/product.html?name=" +
                scrapePathName(decodeURIComponent(productObject.link)));
            let productImage = document.createElement("img");
            productImage.setAttribute("src", decodeURIComponent(productObject.image));
            productImage.setAttribute("class", "image");
            productImageAnchor.appendChild(productImage);
            productElement.appendChild(productImageAnchor);

            let productTitleAnchor = document.createElement("a");
            productTitleAnchor.setAttribute("href", "../html/product.html?name=" +
                scrapePathName(decodeURIComponent(productObject.link)));
            let productTitle = document.createElement("div");
            productTitle.setAttribute("class", "title");
            productTitle.textContent = parseTitle(productObject.title);
            productTitleAnchor.appendChild(productTitle);
            productElement.appendChild(productTitleAnchor);

            let productRating = document.createElement("div");
            productRating.setAttribute("class", "rating");
            let star = 0;
            for (; star < productObject.rating; ++star) {
                let starElement = document.createElement("i");
                starElement.setAttribute("class", "on fa fa-star");
                productRating.appendChild(starElement);
            }
            for (; star < 5; ++star) {
                let starElement = document.createElement("i");
                starElement.setAttribute("class", "off fa fa-star");
                productRating.appendChild(starElement);
            }
            productElement.appendChild(productRating);

            let productPrice = document.createElement("div");
            productPrice.setAttribute("class", "price");
            let price = document.createTextNode("de la " + addPoint(productObject.price));
            productPrice.appendChild(price);
            let decimals = document.createElement("sup");
            decimals.textContent = "99";
            productPrice.appendChild(decimals);
            if (parseInt(productObject.price) < 20) {
                let currency = document.createTextNode(" Lei");
                productPrice.appendChild(currency);
            } else {
                let currency = document.createTextNode(" de Lei");
                productPrice.appendChild(currency);
            }
            productElement.appendChild(productPrice);

            let productOffers = document.createElement("div");
            productOffers.setAttribute("class", "offers");
            let words = productObject.offers.split(" ");
            if (words[0] === "o" || parseInt(words[0]) < 1) {
                productOffers.textContent = "(o oferta)";
            } else if (parseInt(words[0]) < 20) {
                productOffers.textContent = "(" + words[0] + " oferte)";
            } else {
                productOffers.textContent = "(" + words[0] + " de oferte)";
            }
            productElement.appendChild(productOffers);

            productsElement.appendChild(productElement);
        }
    }
}
