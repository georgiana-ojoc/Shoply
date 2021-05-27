<!DOCTYPE html>
<html lang="ro">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../js/login.js" defer></script>
    <title>Shoply</title>
</head>
<body>
<a href="../index.php"><img src="../images/logo.png" alt="Shoply" class="logo"></a>
<div class="form">
    <div class="button-box">
        <div id="button"></div>
        <button type="button" id="login-button" class="toggle-button" name="action" value="login" onclick="login()">
            Autentificare
        </button>
        <button type="button" id="register-button" class="toggle-button" name="action" value="register" onclick="register()">Înregistrare
        </button>
    </div>
    <form id="login" class="credentials" method="POST" action="../php/login.php">
        <label>
            <input type="hidden" name="action" value="login">
        </label>
        <label>
            <input type="text" name="username" autocomplete="off" class="input-field" placeholder="Introdu numele de utilizator" title="Numele de utilizator este obligatoriu." required>
        </label>
        <label>
            <input type="password" name="password" autocomplete="off" class="input-field" placeholder="Introdu parola" title="Parola trebuie să conțină cel puțin 6 caractere, o cifră, o literă mică și o literă mare." required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}">
        </label>
        <button type="submit" class="submit-button" name="action" value="login">Autentifică-te</button>
    </form>
    <div class="error" id="login-error"></div>
    <form id="register" class="credentials" method="POST" autocomplete="off" action="../php/login.php">
        <label>
            <input type="hidden" name="action" value="register">
        </label>
        <label>
            <input type="text" name="username" autocomplete="off" class="input-field" placeholder="Introdu un nume de utilizator" title="Numele de utilizator este obligatoriu." required>
        </label>
        <label>
            <input type="password" name="password" autocomplete="off" class="input-field" placeholder="Introdu o parolă" title="Parola trebuie să conțină cel puțin 6 caractere, o cifră, o literă mică și o literă mare." required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}">
        </label>
        <button type="submit" class="submit-button" name="action" value="register">Înregistrează-te</button>
    </form>
    <div class="error" id="register-error"></div>
</div>
</body>
</html>
