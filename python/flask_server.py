from credentials import *
from flask import Flask, jsonify, request
from flask_cors import CORS
from mysql.connector import pooling
from scrapers.compari_scraper import scrape_products, scrape_vendors

import mysql.connector

connection_pool = mysql.connector.pooling.MySQLConnectionPool(pool_name="shoply_pool",
                                                              pool_size=3,
                                                              pool_reset_session=True,
                                                              host=host,
                                                              user=user,
                                                              password=password,
                                                              database=database)
application = Flask(__name__)
CORS(application)


@application.route("/search", methods=["GET"])
def scrape_search():
    search_query = request.args
    search_query = search_query["search"]
    main_url = "https://www.compari.ro/CategorySearch.php?st=" + search_query.replace(" ", "+").replace("%20", "+")
    response = jsonify(scrape_products(main_url, False))
    response.headers.add("Access-Control-Allow-Origin", "*")
    return response


@application.route("/search_extension", methods=["GET"])
def scrape_search_extension():
    search_query = request.args
    search_query = search_query["search"]
    main_url = "https://www.compari.ro/CategorySearch.php?st=" + search_query.replace(" ", "+").replace("%20", "+")
    response = jsonify(scrape_products(main_url, True))
    response.headers.add("Access-Control-Allow-Origin", "*")
    return response


@application.route("/vendors", methods=["GET"])
def scrape_vendors_request():
    product_query = request.args
    product_link = product_query["product_link"]
    vendors = scrape_vendors(product_link)
    response = jsonify(vendors)
    response.headers.add("Access-Control-Allow-Origin", "*")
    return response


@application.route("/data", methods=["GET"])
def get_data():
    database_connection = connection_pool.get_connection()
    cursor = database_connection.cursor()
    product_query = request.args
    product_link = product_query["product_link"]
    query = "SELECT price, updated_at FROM product_log WHERE link = %s"
    value = (product_link,)
    cursor.execute(query, value)
    result = cursor.fetchall()
    data = []
    for log in result:
        date = log[1].strftime("%m/%d/%Y")
        data.append(dict({"x": log[0], "y": date}))
    cursor.close()
    database_connection.close()
    response = jsonify(data)
    response.headers.add("Access-Control-Allow-Origin", "*")
    return response


if __name__ == "__main__":
    application.run(ssl_context="adhoc")
