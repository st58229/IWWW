<?php
//Zabezpečení - nepřihlášený nemusí vidět prvky stránky
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != true)
{
    header("location: index.php");
    exit();
}
?>
<link rel="stylesheet" href="./css/userPage.css">


<div>
    <ul id="nav-lists">
        <li><a href="index.php?page=userPage&dat=kurzy">Moje kurzy</a></li>
        <li><a href="index.php?page=userPage&dat=objednavky">Objednávky</a></li>
        <li><a href="index.php?page=userPage&dat=nastaveni">Správa účtu</a></li>
        <li><a href="./src/core/logout.php">Odhlasit se</a></li>
    </ul>
</div>

<?php

$dat = isset($_GET["dat"]) ? $_GET["dat"] : "kurzy";

function startsWiths($text, $prefix) { // popř nahradit za str_starts_with v PHP 8
    $possiblePrefix = mb_substr($text, 0, mb_strlen($prefix));
    return $possiblePrefix === $prefix;
}

function isValidDat($dat) {
    $pageUrl = realpath("./src/user/$dat.php");
    $pagesFolderUrl = realpath("./src/user/");
    return startsWiths($pageUrl, $pagesFolderUrl);
}

$included = (isValidDat($dat)) ? include("./src/user/$dat.php") : $included = include("./src/core/404.php");

?>




