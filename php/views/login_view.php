<?php

class LoginView
{
    public function getView($page, $errorMessage)
    {
        ob_start();
        $domDocument = new DOMDocument;
        @$domDocument->loadHTMLfile(__DIR__ . "/../../html/login.tpl");
        if ($page == "login") {
            $loginErrorDivision = $domDocument->getElementById("login-error");
            $loginErrorDivision->nodeValue = $errorMessage;
            $registerErrorDivision = $domDocument->getElementById("register-error");
            $registerErrorDivision->nodeValue = "";
            $loginButton = $domDocument->getElementById("login-button");
            $loginButton->removeAttribute("style");
            $registerButton = $domDocument->getElementById("register-button");
            $registerButton->removeAttribute("style");
            $loginElement = $domDocument->getElementById("login");
            $loginElement->removeAttribute("style");
            $registerElement = $domDocument->getElementById("register");
            $registerElement->removeAttribute("style");
            $buttonElement = $domDocument->getElementById("button");
            $buttonElement->removeAttribute("style");
        } else if ($page == "register") {
            $registerErrorDivision = $domDocument->getElementById("register-error");
            $registerErrorDivision->nodeValue = $errorMessage;
            $loginErrorDivision = $domDocument->getElementById("login-error");
            $loginErrorDivision->nodeValue = "";
            $loginButton = $domDocument->getElementById("login-button");
            $loginButton->setAttribute("style", "color: coral;");
            $registerButton = $domDocument->getElementById("register-button");
            $registerButton->setAttribute("style", "color: white;");
            $loginElement = $domDocument->getElementById("login");
            $loginElement->setAttribute("style", "left: -400px;");
            $registerElement = $domDocument->getElementById("register");
            $registerElement->setAttribute("style", "left: 50px;");
            $buttonElement = $domDocument->getElementById("button");
            $buttonElement->setAttribute("style", "left: 110px;");
        }
        $domDocument->saveHTMLFile(__DIR__ . "/../../html/login.tpl");
        include __DIR__ . "/../../html/login.tpl";
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}
