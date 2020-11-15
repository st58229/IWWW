<?php

?>

<head><?php include "headInfo.php" ?></head>
<header><?php include "headerMenu.php"; ?></header>

<body>
<?php
if (isset($_GET["page"]))
    $page = $_GET["page"];
else
   $page = "home";

if (preg_match('/^[a-z0-9]+$/', $page))
{
    $included = include($page . ".php");
    if (!$included)
        echo("404 Podstránka nenalezena");
}
else
    echo("Neplatný parametr.");
?>
</body>

<footer><?php include "footerInfo.php";?></footer>