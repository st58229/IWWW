<link rel="stylesheet" href="css/index.css">

<?php
/**
 * @var $pdo
 * @var $conn
 */
session_start();
include("./src/core/dbconnection.php")
?>

<div class="dyn">
<head>
    <?php include "./src/core/headInfo.php" ?>
</head>

<header>
    <?php include "./src/core/headerMenu.php"; ?>
</header>

<body>
<main>
<?php
$page = isset($_GET["page"]) ? $_GET["page"] : "home";

function startsWith($text, $prefix) { // popř nahradit za str_starts_with v PHP 8
    $possiblePrefix = mb_substr($text, 0, mb_strlen($prefix));
    return $possiblePrefix === $prefix;
}

function isValidPage($page) {
    $pageUrl = realpath("./src/pages/$page.php");
    $pagesFolderUrl = realpath("./src/pages");
    return startsWith($pageUrl, $pagesFolderUrl);
}

$included = (isValidPage($page)) ? include("./src/pages/$page.php") : $included = include("./src/core/404.php");
?>
</main>
</body>

<footer>
    <?php include "./src/core/footerInfo.php";?>
</footer>
</div>