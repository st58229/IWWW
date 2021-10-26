<!DOCTYPE html>
<link rel="stylesheet" href="../../css/adminPage.css">

<head>
    <meta charset="UTF-8">
    <title>Administrativa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type = "image/x-icon" href="../../img/admin.ico">
</head>

<header>
    <ul id="nav-lists">
        <li><a href="adminPage.php?dat=listUsers">Správa uživatelksých účtů</a></li>
        <li><a href="adminPage.php?dat=listCourses">Správa nabídky kurzů</a></li>
        <li><a href="../../index.php">Vrátit zpět na hlavní stránku</a></li>
        <li><a href="../core/logout.php">Odhlasit se</a></li>
    </ul>
</header>

<body>
<?php
session_start();
include("../core/dbconnection.php");

$dat = isset($_GET["dat"]) ? $_GET["dat"] : "listUsers";

function startsWith($text, $prefix) { // popř nahradit za str_starts_with v PHP 8
    $possiblePrefix = mb_substr($text, 0, mb_strlen($prefix));
    return $possiblePrefix === $prefix;
}

function isValidDat($dat) {
    $pageUrl = realpath("$dat.php");
    $pagesFolderUrl = realpath("");
    return startsWith($pageUrl, $pagesFolderUrl);
}

$included = (isValidDat($dat)) ? include("$dat.php") : $included = include("../core/404.php");

?>

</body>