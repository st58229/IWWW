<?php

?>
<!DOCTYPE html>
<html lang="cs">


<head><?php include "headInfo.php" ?></head>

<header>

</header>

<body>

<?php
if (isset($_GET["form"]))
    $form = $_GET["form"];
else
    $form = "loginForm";

if (preg_match('/^[a-z0-9]+$/', $form))
{
    if ($form == "login") include("loginForm.php");
    else if ($form == "register") include("registerForm.php");
    else echo("Formulář neexistuje!");
}
else
    include("loginForm.php");
?>
<a href="index.php">Vrátit zpět na domovskou stránku</a>

</body>
<footer>

</footer>
</html>
