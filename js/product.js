let parameters = new URLSearchParams(window.location.search);
let name;
if (parameters.has("name")) {
    name = parameters.get("name");
} else {
    name = "copiatoare-c83-xerox-workcentre-6515v-dn-p352968539";
}

let historyRequest = new XMLHttpRequest();
historyRequest.open("GET", "../php/controllers/history_controller.php?name=" + name, true);
historyRequest.send();

let generalInformationRequest = new XMLHttpRequest();
generalInformationRequest.onreadystatechange = addProductInformation;
generalInformationRequest.open("GET", "../php/controllers/product_controller.php?name=" + name, true);
generalInformationRequest.send();

let vendorsRequest = new XMLHttpRequest();
vendorsRequest.onreadystatechange = addVendors;
vendorsRequest.open("GET", "../php/controllers/product_controller.php?name=" + name +
    "&vendors=true", true);
vendorsRequest.send();

let chartRequest = new XMLHttpRequest();
chartRequest.onreadystatechange = addChart;
chartRequest.open("GET", "../php/controllers/product_controller.php?name=" + name +
    "&chart=true", true);
chartRequest.send();

let shareButton = document.getElementById("facebook-share-button").addEventListener("click", function () {
    let facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" + "https://shoply-web.herokuapp.com/html/product.html?name=" + name + "&hashtag=%23comit&title=" + name;
    openFacebookWindow(facebookUrl);
});

function openFacebookWindow(facebook_url) {
    window.open(
        facebook_url, "share-facebook", "width = 580, height = 296"
    );
}

function addProductInformation() {
    if (this.readyState === generalInformationRequest.DONE && this.status === 200) {
        let productInformation = JSON.parse(this.responseText);
        let link = document.getElementsByClassName("link")[0];
        link.setAttribute("href", decodeURIComponent(productInformation.link));
        let image = document.getElementsByClassName("image")[0];
        image.setAttribute("src", decodeURIComponent(productInformation.image));

        let characteristics = document.getElementsByClassName("characteristics")[0];
        if (productInformation.characteristics === "") {
            characteristics.setAttribute("style", "display:none;");
        } else {
            characteristics.innerHTML = productInformation.characteristics;
        }

        let title = document.getElementsByClassName("title")[0];
        title.textContent = productInformation.title;

        let starElement = document.getElementById("rating-" + productInformation.rating);
        starElement.checked = true;
        for (let stars = 1; stars <= 5; ++stars) {
            let starElement = document.getElementById("rating-" + stars);
            starElement.addEventListener("click", () => {
                let ratingRequest = new XMLHttpRequest();
                ratingRequest.onreadystatechange = addMessage;
                ratingRequest.open("GET", "../php/controllers/rating_controller.php?name="
                    + name + "&" + "stars=" + stars, true);
                ratingRequest.send();
            });
        }

        updateRatings(productInformation);

        let price = document.getElementsByClassName("price")[0];
        let priceText = document.createTextNode("de la " + addPoint(productInformation.price));
        price.appendChild(priceText);
        let decimals = document.createElement("sup");
        decimals.textContent = "99";
        price.appendChild(decimals);
        if (parseInt(productInformation.price) < 20) {
            let currency = document.createTextNode(" Lei");
            price.appendChild(currency);
        } else {
            let currency = document.createTextNode(" de Lei");
            price.appendChild(currency);
        }

        let offers = document.getElementsByClassName("offers")[0];
        let words = productInformation.offers.split(" ");
        if (words[0] === "o" || parseInt(words[0]) < 1) {
            offers.textContent = "(o oferta)";
        } else if (parseInt(words[0]) < 20) {
            offers.textContent = "(" + words[0] + " oferte)";
        } else {
            offers.textContent = "(" + words[0] + " de oferte)";
        }
        offers.addEventListener("click", () => {
            let vendors = document.getElementsByClassName("vendors-text")[0];
            vendors.scrollIntoView({behavior: "smooth", block: "center"});
        });

        updateViews(productInformation);

        let description = document.getElementsByClassName("description")[0];
        description.textContent = productInformation.description;
    }
}

function addVendors() {
    if (this.readyState === vendorsRequest.DONE && this.status === 200) {
        let vendorsInformation = JSON.parse(this.responseText);
        vendorsInformation = JSON.parse(vendorsInformation.vendors);
        let vendors = document.getElementsByClassName("vendors")[0];
        vendorsInformation.sort(function (first, second) {
            return parseFloat(first.price) - parseFloat(second.price);
        });
        let vendorInformation;
        for (vendorInformation of vendorsInformation) {
            let vendor = document.createElement("div");
            vendor.setAttribute("class", "vendor");
            let logoAnchor = document.createElement("a");
            logoAnchor.setAttribute("href", vendorInformation.link);
            let logo = document.createElement("img");
            if (vendorInformation.logo === "NO_LOGO") {
                logo.setAttribute("src", "../images/broken.png");
            } else {
                logo.setAttribute("src", vendorInformation.logo);
            }

            logo.setAttribute("class", "vendorLogo");
            logoAnchor.appendChild(logo);
            vendor.appendChild(logoAnchor);

            let name = document.createElement("div");
            name.setAttribute("class", "name");
            name.textContent = vendorInformation.name;
            vendor.appendChild(name);

            let price = document.createElement("div");
            price.setAttribute("class", "price");
            let priceText = document.createTextNode(addPoint(vendorInformation.price));
            price.appendChild(priceText);
            let decimals = document.createElement("sup");
            decimals.textContent = "99";
            price.appendChild(decimals);
            if (parseInt(vendorInformation.price) < 20) {
                let currency = document.createTextNode(" Lei");
                price.appendChild(currency);
            } else {
                let currency = document.createTextNode(" de Lei");
                price.appendChild(currency);
            }
            vendor.appendChild(price);

            let button = document.createElement("a");
            button.setAttribute("href", vendorInformation.link);
            button.setAttribute("class", "button");
            let buttonIcon = document.createElement("i");
            buttonIcon.setAttribute("aria-hidden", "true");
            buttonIcon.setAttribute("class", "fa fa-shopping-cart");
            button.appendChild(buttonIcon);
            let buttonText = document.createTextNode(" Cumpara");
            button.appendChild(buttonText);
            vendor.appendChild(button);
            vendors.appendChild(vendor);
        }
    }
}

function addChart() {
    if (this.readyState === chartRequest.DONE && this.status === 200) {
        let logs = JSON.parse(this.responseText);
        let prices = [];
        let dates = [];
        let log;
        for (log of logs) {
            prices.push(parseInt(log.price, 10));
            dates.push(log.date);
        }
        let context = document.getElementById("chart").getContext("2d");
        const chart = new Chart(context, {
            type: "line",
            data: {
                labels: dates,
                datasets: [{
                    label: "Prices",
                    data: prices,
                    borderColor: "coral",
                    fill: false,
                    borderWidth: 1
                }]
            },
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMax: 1000000
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}

function addMessage() {
    if (this.readyState === generalInformationRequest.DONE) {
        let response = JSON.parse(this.responseText);
        let ratingText = document.getElementsByClassName("rating-text")[0];
        ratingText.textContent = response.message;
        generalInformationRequest = new XMLHttpRequest();
        generalInformationRequest.onreadystatechange = updateRating;
        generalInformationRequest.open("GET", "../php/controllers/product_controller.php?name=" + name +
            "&rating=true", true);
        generalInformationRequest.send();
    }
}

function updateRating() {
    if (this.readyState === generalInformationRequest.DONE && this.status === 200) {
        let productInformation = JSON.parse(this.responseText);
        let starElement = document.getElementById("rating-" + productInformation.rating);
        starElement.checked = true;
        updateRatings(productInformation);
    }
}

function updateRatings(productInformation) {
    let ratings = document.getElementsByClassName("ratings")[0];
    let ratingsNumber = parseInt(productInformation.ratings);
    if (ratingsNumber === 1) {
        ratings.textContent = "(un vot)";
    } else if (ratingsNumber < 20) {
        ratings.textContent = "(" + ratingsNumber + " voturi)";
    } else {
        ratings.textContent = "(" + ratingsNumber + " de voturi)";
    }
}

function updateViews(productInformation) {
    let views = document.getElementsByClassName("views")[0];
    let viewsNumber = parseInt(productInformation.views);
    if (viewsNumber === 1) {
        views.textContent = "o vizualizare";
    } else if (viewsNumber < 20) {
        views.textContent = viewsNumber + " vizualizari";
    } else {
        views.textContent = viewsNumber + " de vizualizari";
    }
}
