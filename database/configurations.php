<?php
require_once "credentials.php";
require_once "database.php";

class Table
{
    public static function createTable($name, $statement)
    {
        try {
            Database::getConnection()->exec($statement);
            echo "Table \"" . $name . "\" was created successfully. <br>";
        } catch (PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    public static function createInsertProductTrigger()
    {
        try {
            $statement = "CREATE TRIGGER insert_product 
                    BEFORE INSERT ON products FOR EACH ROW
                    INSERT IGNORE INTO product_log VALUES (NEW.link, NOW(), NEW.price)";
            Database::getConnection()->exec($statement);
            echo "Trigger \"insert_product\" on table \"products\" was created successfully.";
        } catch (PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    public static function createTables()
    {
        Table::createTable("users", "CREATE TABLE IF NOT EXISTS users
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(64) NOT NULL UNIQUE,
                password VARCHAR(256) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");

        Table::createTable("products", "CREATE TABLE IF NOT EXISTS products
                (link VARCHAR(256) NOT NULL PRIMARY KEY,
                title VARCHAR(256) NOT NULL,
                characteristics TEXT,
                description TEXT,
                price VARCHAR(64) NOT NULL,
                currency VARCHAR(64) DEFAULT 'RON',
                offers VARCHAR(64) DEFAULT NULL,
                image VARCHAR(256) DEFAULT NULL,
                vendors TEXT DEFAULT NULL,
                views INT DEFAULT 0)");

        Table::createTable("product_log", "CREATE TABLE IF NOT EXISTS product_log
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                link VARCHAR(256) NOT NULL,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                price INT NOT NULL,
                FOREIGN KEY (link) REFERENCES products(link) ON DELETE CASCADE)");

        Table::createTable("categories", "CREATE TABLE IF NOT EXISTS categories
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                link VARCHAR(256) NOT NULL,
                category VARCHAR(256) NOT NULL,
                INDEX categories_index (category), 
                FOREIGN KEY (link) REFERENCES products (link) ON DELETE CASCADE)");

        Table::createTable("history", "CREATE TABLE IF NOT EXISTS history
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user VARCHAR(64) NOT NULL,
                link VARCHAR(256) NOT NULL,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX users_index (user),
                FOREIGN KEY (user) REFERENCES users(username) ON DELETE CASCADE,
                FOREIGN KEY (link) REFERENCES products(link) ON DELETE CASCADE)");

        Table::createTable("rating", "CREATE TABLE IF NOT EXISTS rating
                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user VARCHAR(64) NOT NULL,
                link VARCHAR(256) NOT NULL,
                rating INT(1) NOT NULL,
                INDEX users_index (user),
                INDEX products_index(link),
                FOREIGN KEY (user) REFERENCES users(username) ON DELETE CASCADE,
                FOREIGN KEY (link) REFERENCES products(link) ON DELETE CASCADE)");
    }
}

Table::createTables();
//Table::createInsertProductTrigger();
