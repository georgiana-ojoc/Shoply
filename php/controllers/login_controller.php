<?php
require_once __DIR__ . "/../models/login_model.php";
require_once __DIR__ . "/../views/login_view.php";

class LoginController
{
    private LoginModel $model;
    private string $error = "";

    public function __construct($action, $parameters)
    {
        $this->model = new LoginModel();
        switch ($action) {
            case "login":
                $this->validateUser($parameters[0], $parameters[1]);
                break;
            case "register":
                $this->addUser($parameters[0], $parameters[1]);
                break;
            default:
                $this->display("login");
        }
    }

    private function addUser($username, $password)
    {
        $this->error = $this->model->addUser($username, $password);
        if ($this->error == "") {
            $_SESSION["username"] = $username;
            header("Location: ../html/index.html");
        } else {
            $this->display("register");
        }
    }

    private function validateUser($username, $password)
    {
        $this->error = $this->model->validateUser($username, $password);
        if ($this->error == "") {
            $_SESSION["username"] = $username;
            header("Location: ../html/index.html");
        } else {
            $this->display("login");
        }
    }

    private function display($page)
    {
        $view = new LoginView();
        echo $view->getView($page, $this->error);
    }
}
