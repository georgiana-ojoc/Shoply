<?php

class Product
{
    private $connection;
    private $table = "products";
    private $id;
    private $category;
    private $link;
    private $title;
    private $description;
    private $price;
    private $currency;
    private $offers;
    private $image;
    private $vendors;
    private $views;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getOffers()
    {
        return $this->offers;
    }

    public function setOffers($offers)
    {
        $this->offers = $offers;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getVendors()
    {
        return $this->vendors;
    }

    public function setVendors($vendors)
    {
        $this->vendors = $vendors;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function create()
    {
        $query = "INSERT INTO products (link, title, price, image) VALUES (:link, :title, :price, :image)";
        $statement = $this->connection->prepare($query);
        if (!$statement->execute(array("link" => $this->link, "title" => $this->title, "price" => $this->price,
            "image" => $this->image))) {
            return false;
        }
        $query = "INSERT INTO product_log (link, price) VALUES (:link, :price)";
        $statement = $this->connection->prepare($query);
        if (!$statement->execute(array("link" => $this->link, "price" => $this->price))) {
            return false;
        }
        $query = "INSERT INTO categories (link, category) VALUES (:link, :category)";
        $statement = $this->connection->prepare($query);
        return $statement->execute(array("link" => $this->link, "category" => $this->category));
    }

    public function readAll()
    {
        $query = "SELECT * FROM products p JOIN categories c on p.link = c.link ORDER BY p.id";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function readByLink()
    {
        $query = "SELECT * FROM products p JOIN categories c on p.link = c.link WHERE p.link = :link";
        $statement = $this->connection->prepare($query);
        $statement->execute(array("link" => $this->link));
        return $statement;
    }

    public function readByCategory()
    {
        $query = "SELECT * FROM products p JOIN categories c on p.link = c.link WHERE category = :category ORDER BY p.id";
        $statement = $this->connection->prepare($query);
        $statement->execute(array("category" => $this->category));
        return $statement;
    }

    public function updatePrice()
    {
        $query = "UPDATE products SET price = :price WHERE link = :link";
        $statement = $this->connection->prepare($query);
        if (!$statement->execute(array("price" => $this->price, "link" => $this->link))) {
            return false;
        }
        $query = "INSERT INTO product_log (link, price) VALUES (:link, :price)";
        $statement = $this->connection->prepare($query);
        return $statement->execute(array("link" => $this->link, "price" => $this->price));
    }

    public function updateOffers($vendor)
    {
        $query = "SELECT vendors FROM products WHERE link = :link";
        $statement = $this->connection->prepare($query);
        $statement->execute(array("link" => $this->link));
        $vendorsNumber = $statement->rowCount();
        if ($vendorsNumber == 0) {
            return false;
        }
        $vendors = json_decode($statement["vendors"]);
        array_push($vendors, $vendor);
        $query = "UPDATE products SET vendors = :vendors WHERE link = :link";
        $statement = $this->connection->prepare($query);
        return $statement->execute(array("vendors" => $vendors, "link" => $this->link));
    }

    public function deleteByLink()
    {
        $statement = "DELETE FROM products WHERE link = :link";
        $query = $this->connection->prepare($statement);
        return $query->execute(array("link" => $this->link));
    }
}
