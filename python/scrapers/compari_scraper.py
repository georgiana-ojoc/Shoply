from bs4 import BeautifulSoup
from credentials import *
from mysql.connector import pooling

import re
import requests
import json
import mysql.connector

connection_pool = mysql.connector.pooling.MySQLConnectionPool(pool_name="shoply_pool",
                                                              pool_size=3,
                                                              pool_reset_session=True,
                                                              host=host,
                                                              user=user,
                                                              password=password,
                                                              database=database)


def transform_to_int(price_string):
    number = 0
    for character in price_string:
        if character == "," or character == ".":
            break
        if character.isdigit():
            number = number * 10 + int(character)

    return number


def add_data_in_database(database_connection):
    categories = [
        # "electronice",
        "calculatoare",
        "electrocasnice",
        "moda",
        "sport",
        "cosmetica",
        "bebelusi",
        "gradina",
        "auto",
        "jucarii",
        "birou",
        "constructie"
    ]
    categories_links = [
        # electronice
        # [
        #     "https://www.compari.ro/telefoane-mobile-c3277/",
        #     "https://www.compari.ro/televizoare-c3164/",
        #     "https://www.compari.ro/husa-telefon-mobil-pda-gps-c4202/",
        #     "https://smartwatch-bratara-fitness.compari.ro/",
        #     "https://www.compari.ro/aparate-foto-c40/",
        #     "https://boxa-portabila.compari.ro/",
        #     "https://www.compari.ro/headset-c3740/",
        #     "https://www.compari.ro/boxe-active-c4060/",
        #     "https://www.compari.ro/microfoane-si-casti-c3109/",
        #     "https://www.compari.ro/obiectiv-aparat-foto-c4191/",
        #     "https://folie-de-protectie.compari.ro/"
        # ],

        # calculatoare
        [
            # "https://www.compari.ro/notebook-laptop-c3100/",
            # "https://tablet-pc.compari.ro/",
            # "https://www.compari.ro/imprimante-si-multifunctionale-c3134/",
            # "https://www.compari.ro/monitoare-c3126/",
            # "https://www.compari.ro/placi-video-c3142/",
            # "https://www.compari.ro/software-jocuri-c3255/",
            "https://www.compari.ro/cartuse-si-tonere-c3138/",
            "https://www.compari.ro/procesoare-c3139/",
            "https://www.compari.ro/placi-de-baza-c3128/",
            "https://www.compari.ro/console-c3154/",
            "https://www.compari.ro/routere-c3144/",
            "https://solid-state-drive-ssd.compari.ro/"
        ],

        # electrocasnice
        [
            "https://www.compari.ro/centrale-termice-si-sobe-c4026/",
            "https://www.compari.ro/frigidere-congelatoare-c3168/",
            "https://www.compari.ro/masini-de-spalat-c3167/",
            "https://www.compari.ro/aspiratoare-c3170/",
            "https://www.compari.ro/cafetiere-filtre-de-cafea-c3174/",
            "https://www.compari.ro/plita-c4172/",
            "https://www.compari.ro/aragaze-plite-cuptoare-c3169/",
            "https://www.compari.ro/hote-c3873/",
            "https://www.compari.ro/aer-conditionat-c3172/",
            "https://www.compari.ro/masini-de-spalat-vase-c3171/",
            "https://www.compari.ro/cuptoare-cu-microunde-c3179/"
        ],

        # moda
        [
            "https://www.compari.ro/ochelari-de-soare-c4077/",
            "https://pantof-dama.compari.ro/",
            "https://pantof-barbati.compari.ro/",
            "https://genti-dama.compari.ro/",
            "https://geanta-de-umar.compari.ro/",
            "https://valiza.compari.ro/",
            "https://www.compari.ro/rucsac-c3925/",
            "https://papuc-dama.compari.ro/",
            "https://pantof-copii.compari.ro/",
            "https://ciorapi.compari.ro/",
            "https://geanta-diplomat.compari.ro/",
            "https://sandale-dama.compari.ro/"
        ],

        # sport
        [
            "https://www.compari.ro/biciclete-c104/",
            "https://www.compari.ro/trotineta-c3008/",
            "https://ceas-sport-computer.compari.ro/",
            "https://www.compari.ro/benzi-de-alergare-c4073/",
            "https://www.compari.ro/biciclete-fitness-c3044/",
            "https://geanta-frigorifica.compari.ro/",
            "https://proteina.compari.ro/"
        ],

        # cosmetica
        [
            "https://www.compari.ro/parfumuri-c3262/",
            "https://www.compari.ro/epilatoare-c3185/",
            "https://www.compari.ro/placa-de-intins-parul-c4209/",
            "https://www.compari.ro/aparat-de-tuns-c4208/",
            "https://www.compari.ro/ondulator-de-par-electric-c3184/",
            "https://www.compari.ro/aparate-de-ras-c3186/"
        ],

        # bebelusi
        [
            "https://www.compari.ro/anvelope-c3615/",
            "https://www.compari.ro/carucioare-c3919/",
            "https://www.compari.ro/scaune-auto-copii-c3950/",
            "https://pat-pentru-bebelusi.compari.ro/",
            "https://www.compari.ro/scutece-c3241/",
            "https://marsupiu-bebelusi.compari.ro/",
            "https://aparat-supraveghere-bebelus.compari.ro/",
            "https://premergator.compari.ro/"
        ],

        # gradina
        [
            "https://www.compari.ro/motosapa-c4186/",
            "https://drujba.compari.ro/",
            "https://www.compari.ro/motocoasa-c4184/",
            "https://aparat-de-spalat-cu-presiune.compari.ro/",
            "https://www.compari.ro/masina-de-tuns-iarba-c4185/",
            "https://pompa.compari.ro/",
            "https://generator.compari.ro/",
            "https://pulverizator.compari.ro/"
        ],

        # auto
        [
            "https://www.compari.ro/anvelope-c3615/",
            "https://www.compari.ro/lubrifiante-c3678/",
            "https://www.compari.ro/acumulatoare-auto-c3660/",
            "https://www.compari.ro/jante-c4074/",
            "https://ulei-cutie-de-viteza.compari.ro/",
            "https://acumulator-moto.compari.ro/",
            "https://incarcator-baterii-auto.compari.ro/",
            "https://husa-scaun-auto.compari.ro/"
        ],

        # jucarii
        [
            "https://lego.compari.ro/",
            "https://tricicleta.compari.ro/",
            "https://www.compari.ro/vehicule-biciclete-triciclete-c4043/",
            "https://tobogan.compari.ro/",
            "https://www.compari.ro/playmobile-c3830/",
            "https://www.compari.ro/joc-de-societate-c3805/",
            "https://masinuta-electrica-vehicul-electric.compari.ro/",
            "https://bucatarie-copii.compari.ro/",
            "https://www.compari.ro/papusi-c3831/",
            "https://www.compari.ro/spatiu-de-joaca-in-gradina-c4042/",
            "https://jucarie-interactiva.compari.ro/",
            "https://www.compari.ro/spatiu-de-joaca-in-gradina-c4042/"
        ],

        # birou
        [
            "https://www.compari.ro/copiatoare-c83/",
            "https://scaun-de-birou-rotativ.compari.ro/",
            "https://www.compari.ro/telefoane-c89/",
            "https://www.compari.ro/ghiozdane-c3991/",
            "https://stilou.compari.ro/",
            "https://pix.compari.ro/",
            "https://www.compari.ro/calculator-de-birou-c3414/",
            "https://penar.compari.ro/"
        ],

        # constructie
        [
            "https://aparat-de-sudura-invertor.compari.ro/",
            "https://polizor-unghiular.compari.ro/",
            "https://bormasina-ciocan-rotopercutor.compari.ro/",
            "https://masina-de-insurubat-cu-impact.compari.ro/",
            "https://camera-ip.compari.ro/",
            "https://foto-tapet.compari.ro/",
            "https://termostat.compari.ro/",
            "https://www.compari.ro/camere-de-supraveghere-c3871/",
            "https://fierastrau-circular-manual.compari.ro/"
        ]
    ]
    cursor = database_connection.cursor()
    category_index = -1
    for category in categories_links:
        category_index = category_index + 1
        for page in category:
            products = []
            try:
                products = json.loads(scrape_products(page, False))
                print(page)
            except Exception as exception:
                print(exception)
                exit()
            for product in products:
                add_product_in_database(database_connection, cursor, product)
                genre = categories[category_index]
                query = "INSERT IGNORE INTO categories(category, link) VALUES (%s, %s)"
                values = (genre, product["link"])
                try:
                    cursor.execute(query, values)
                    database_connection.commit()
                except Exception as exception:
                    print(exception)
                    exit()
    cursor.close()
    database_connection.close()


def scrape_products(page, extension):
    page = requests.get(page)
    soup = BeautifulSoup(page.content, "html.parser")
    product_elements = soup.find_all(class_="product-box clearfix")
    products = []
    for product_element in product_elements:
        product_link = product_element.find(class_="image")["href"]
        product_title = product_element.find(class_="name ulined-link").find("a").get_text()
        product_characteristics = ""
        if product_element.find(class_="description clearfix hidden-xs"):
            characteristics_element = product_element.find(class_="description clearfix hidden-xs").findAll("ul")
            if len(characteristics_element) > 1:
                product_characteristics = str(characteristics_element[0]) + str(characteristics_element[1])
            elif len(characteristics_element) == 1:
                product_characteristics = str(characteristics_element[0])
        product_price = product_element.find(class_="price").get_text()
        while not product_price[0].isdigit():
            product_price = product_price[1:]
        product_offers_number = product_element.find(class_="offer-num").get_text().lstrip().rstrip()
        product_image_url = product_element.find(class_="img-responsive lazy")
        if product_image_url:
            product_image_url = product_image_url["data-lazy-src"]
        else:
            product_image_url = product_element.find(class_="img-responsive")["src"]
        if extension:
            products.append({"link": product_link, "title": product_title, "description": product_characteristics,
                             "price": transform_to_int(product_price), "offers": product_offers_number,
                             "image": product_image_url})
        else:
            products.append({"link": product_link, "title": product_title, "characteristics": product_characteristics,
                             "description": scrape_description(product_link),
                             "price": transform_to_int(product_price), "offers": product_offers_number,
                             "image": product_image_url})
    return json.dumps(products)


def scrape_description(link):
    description_link = link + "#descrierea-produsului"
    page = requests.get(description_link)
    soup = BeautifulSoup(page.content, "html.parser")
    product_elements = soup.find(class_="text property-sheet").getText()
    product_elements = re.sub("\n+", "\n", product_elements.replace("   ", "\n"))
    escape_index = product_elements.find("Galerie")
    if escape_index != -1:
        product_elements = product_elements[:escape_index]
    return product_elements


def scrape_vendors_database(product_link):
    return json.dumps(scrape_vendors(product_link))


def scrape_vendors(product_link):
    page = requests.get(product_link)
    soup = BeautifulSoup(page.content, "html.parser")
    product_elements = soup.find_all(itemprop="offers")
    vendors = []
    for i in range(1, len(product_elements)):
        product = product_elements[i]
        logo_raw_url = product.find(class_="col-logo")
        if logo_raw_url:
            logo_raw_url = logo_raw_url.find(class_="img-responsive logo-host")
        if logo_raw_url:
            logo_url = logo_raw_url["src"]
        else:
            logo_url = "NO_LOGO"
        vendor_link = product.findNext("a")["href"]
        vendor_name = product.find(itemprop="seller").get("content")
        offer_price = product.find(itemprop="price").get("content")
        offer_currency = product.find(itemprop="priceCurrency").get("content")
        vendors.append({"logo": logo_url, "link": vendor_link, "name": vendor_name,
                        "price": transform_to_int(offer_price), "currency": offer_currency})
    return vendors


def add_products_in_database(products):
    database_connection = connection_pool.get_connection()
    cursor = database_connection.cursor()
    for product in products:
        add_product_in_database(database_connection, cursor, product)
    cursor.close()
    database_connection.close()


def add_product_in_database(database_connection, cursor, product):
    if product["price"] == 0 or "oferte" not in product["offers"]:
        return
    response_vendors = scrape_vendors_database(product["link"])
    query = "INSERT IGNORE INTO products (link, title, characteristics, description, price, offers, image, vendors) " \
            "VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
    values = (product["link"], product["title"], product["characteristics"], product["description"],
              product["price"], product["offers"], product["image"], response_vendors,)
    try:
        cursor.execute(query, values)
        database_connection.commit()
    except Exception as exception:
        print(exception)
        exit()
    query = "INSERT IGNORE INTO product_log(link, price) VALUES (%s, %s)"
    values = (product["link"], product["price"])
    try:
        cursor.execute(query, values)
        database_connection.commit()
    except Exception as exception:
        print(exception)
        exit()


if __name__ == "__main__":
    add_data_in_database(connection_pool.get_connection())
