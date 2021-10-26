<?php
/**
 * @var $conn
 * @var $page
 */
session_start();
include("dbconnection.php");

function getBy($att, $value, $array)
{
    foreach ($array as $key => $val) {
        if ($val[$att] == $value) {
            return $key;
        }
    }
    return null;
}

if (isset($_GET["action"])) {
    if ($_GET["action"] == "add" && !empty($_GET["id"])) {
        addToCart($_GET["id"], $conn);
        header("Location: cart.php");
    }
    if ($_GET["action"] == "delete" && !empty($_GET["id"])) {
        deleteFromCart($_GET["id"]);
        header("Location: cart.php");
    }
    if ($_GET["action"] == "submit") {
        submitFromCart($conn);
        header("Location: ../../index.php?page=userPage&dat=objednavkaDetail&obj=".$_SESSION["ordID"]);
        $_SESSION["ordID"] = "";
        cleanCart();
    }
    $page = "items";

}

//$linkUcet = "";
$linkPrehled = "";

/*if (isset($_POST['pay'])){
    $linkUcet = "cart.php?page=ucet";
}*/

function cleanCart()
{
    unset($_SESSION["cart"]);
    unset($_SESSION["owned"]);
}

function addToCart($productId, $conn)
{
    // Nevlastní - můžeme vkládat
    if (!checkOwnedItem($productId, $conn))
    {
        // Není již v košíku - můžeme vkládat
        if (!isset($_SESSION["cart"][$productId]))
        {
            $_SESSION["cart"][$productId] = $productId;
        }
    }
    // Vlastní - zobrazíme upozornění
    else{

        // Uložíme takovou pložku do separátního seznamu
        if (!isset($_SESSION["owned"][$productId]))
        {
            $_SESSION["owned"][$productId] = $productId;
        }
    }
}

function deleteFromCart($productId)
{
    unset($_SESSION["cart"][$productId]);
}

function submitFromCart($conn)
{
    if (isset($_SESSION["cart"])) {

    $prevod = "Prevod";

    // Vytvoří záznam objednávky
    $sql = "INSERT INTO orders (id_user, price, date, pay) VALUES (:user, :price, :date, :pay)";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":user", $_SESSION["id"], PDO::PARAM_STR);
            $stmt->bindParam(":price", $_SESSION["totalPrice"], PDO::PARAM_STR);
            $stmt->bindParam(":date", date("Y/m/d H:i:s"), PDO::PARAM_STR);
            $stmt->bindParam(":pay", $prevod , PDO::PARAM_STR);

            if(!$stmt->execute()){
                echo "Něco se nepovedlo. Zkus to později.";
            }
            else{
                $_SESSION["ordID"] = $conn->lastInsertId();
            }
        }

    // Zápis jednotlivých položek do relační tabulky
    foreach ($_SESSION["cart"] as $key => $id) {

        $sql = "SELECT price FROM kurzy WHERE kurz_id = :id";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $item = $stmt->fetch();

        $sql = "INSERT INTO ordersitems (order_id, product_id, paid) VALUES (:order, :product, :paid)";
        if ($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":order", $_SESSION["ordID"] , PDO::PARAM_STR); //TODO Potenciálně může vložit někdo další jinou objednávku - podívat
            $stmt->bindParam(":product", $id, PDO::PARAM_STR);
            $stmt->bindParam(":paid", $item["price"], PDO::PARAM_STR);

            if(!$stmt->execute()){
                echo "Něco se nepovedlo. Zkus to později.";
            }
        }

        $sql = "INSERT INTO ownercourses (user_id, kurz_id) VALUES (:user, :kurz)";
        if ($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":user", $_SESSION["id"], PDO::PARAM_STR);
            $stmt->bindParam(":kurz", $id, PDO::PARAM_STR);

            if(!$stmt->execute()){
                echo "Něco se nepovedlo. Zkus to později.";
            }
        }

    }
    }
}

function checkOwnedItem($id_kurz, $conn)
{
    if (!isset($_SESSION["id"])){
        return false;
    }

    $sql = "SELECT id_ownd FROM ownercourses WHERE user_id = :user AND kurz_id = :kurz";

    if($stmt = $conn->prepare($sql)){
        $stmt->bindParam(":user", $_SESSION["id"], PDO::PARAM_STR);
        $stmt->bindParam(":kurz", $id_kurz, PDO::PARAM_STR);
        $stmt->execute();
//        printf('U:'.$id_user);
//        printf('K:'.$id_kurz);
        if($stmt->rowCount() == 1){
            return true;
        }
    }
    return false;
}

function showItem($item)
{
    echo '
    <div class="cart-item">
    <div class="cart-img">
    ' . '<img style="width: 50%" src="data:image/jpeg;base64,' . base64_encode($item["thumb"]) . '"/>' . '
    </div>
    <div style="font-size: 80px;padding: 20px">
    ' . $item["k_name"] . '
    </div>
    <div class="cart-control">
    <div class="cart-price">
    ' . $item["price"] . ' Kč
    </div>
    <a href="cart.php?action=delete&id=' . $item["kurz_id"] . '" class="cart-button">
    x
    </a>
    </div>
    </div>';
}

function showOwnedItem($item)
{
    echo '
    <div class="cart-item-owned">
    <div class="cart-img">
    ' . '<img style="width: 50%" src="data:image/jpeg;base64,' . base64_encode($item["thumb"]) . '"/>' . '
    </div>
    <div style="font-size: 80px;padding: 20px">
    ' . $item["k_name"] . '
    </div>
    <div class="cart-control">
    <div class="cart-price">
    ' . $item["price"] . ' Kč
    </div>    
    </div>
    </div>';
}

function showOwned($conn){

    if (isset($_SESSION["owned"])) {
        echo 'Tyto položky již vlastníte a kupovat je znovu je blbost, takže jsme je z košíku odstranili:';
        foreach ($_SESSION["owned"] as $key => $id) {

            $stmt = $conn->prepare("SELECT * FROM kurzy WHERE kurz_id = :id");

            $stmt->bindParam(":id", $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $item = $stmt->fetch();
            showOwnedItem($item);
        }
    }
}

function showCart($conn)
{
    if (isset($_SESSION["cart"])) {
        $_SESSION["totalPrice"] = 0;
        foreach ($_SESSION["cart"] as $key => $id) {

            $stmt = $conn->prepare("SELECT * FROM kurzy WHERE kurz_id = :id");

            $stmt->bindParam(":id", $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $item = $stmt->fetch();
            showItem($item);

            $_SESSION["totalPrice"] = $_SESSION["totalPrice"] + $item["price"];
        }
    }

}

function showMenu($page){
//
//    if ($page = "items")
//    {
//       echo '<form action="cart.php?action=submit" method="post">
//            <input type="submit" value="Order" name="Order">
//            </form>';
//    }
}

function showPay($conn)
{
   /* echo '  <form action="cart.php?page=ucet" method="post">
            <input type="radio" id="Card" name="pay" value="Card">
            <label for="Card">On-line kartou</label><br>
            <input type="radio" id="Bank" name="pay" value="Bank">
            <label for="Ban">Bankovním převedem</label><br>
            <input type="radio" id="PayPal" name="pay" value="PayPal">
            <label for="PayPal">PayPal</label><br>
            <input type="radio" id="BitCoin" name="pay" value="Bitcoin">
            <label for="BitCoin">Bitcoin</label> 
            <input type="submit" value="Pokračovat" name="paySel">
            </form>';*/
}

function showSum()
{
    if($_SESSION["totalPrice"] != 0) {
        echo '<div id="cart-total-price">Total price: ' . $_SESSION["totalPrice"] . '</div>            
            <form action="cart.php?action=submit" method="post">
            <input type="submit" value="Order" name="Order">
            </form>';
    }
}

function userCheck($conn){
    /*if (isset($_POST['pay'])) {
        $_SESSION["pay"] = $_POST["pay"];
    }*/

    if (!isset($_SESSION["id"])){
        $_POST["src"] = "cart";
        include("../pages/login.php");
    }
    else{

        $sql = "SELECT u_login, u_name, u_surname FROM users WHERE u_id = :id";

        if($stmt = $conn->prepare($sql)){
            $stmt->bindParam(":id", $_SESSION["id"], PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() == 1){
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $item = $stmt->fetch();
                echo 'Jste přihlášen jako uživatel '.$item["u_login"].' ('.$item["u_name"].' '.$item["u_surname"].')';
            }
        }
        echo '<br>';
        if (isset($_SESSION["totalPrice"]))
        {
            $_POST["final"] = "";
            echo '<a href="cart.php?page=prehled" onclick="">K přehledu</a>';
        }

    }

}

?>
<html>
    <link rel="stylesheet" href="../css/cart.css">
<body>
<head>
    <style>
        .cart-button {
            margin: 5px;
            padding: 5px;
            text-align: center;
            text-decoration: none;
            font-size: 30px;
            color: black;

        }

        .cart-header
        {
            justify-content: space-between;
            display: flex;
            margin: 5px;
            padding: 5px;
        }

        .cart-item {
            justify-content: space-between;
            display: flex;
            margin: 5px;
            background-color: #DDDDDD;
            padding: 5px;
        }

        .cart-item-owned
        {
            justify-content: space-between;
            display: flex;
            margin: 5px;
            background-color: lightcoral;
            padding: 5px;
        }

        .cart-price {
            margin: 40px;
            font-size: 50px;
        }

        .cart-control {
            display: flex;
        }

        #cart-total-price {
            font-weight: bold;
        }

        #menuCart
        {
            color: black;
            text-align: center;
            text-decoration: none;
            font-size: 17px;
        }
    </style>
</head>
<header>
    <nav>
        <div id="menuCart">
                <a href="cart.php?page=polozky">Položky</a>
                <a href="cart.php?page=ucet">Účet</a>
                <a href="cart.php?page=<?php isset($_GET["page"]) ? 'prehled' : '' ?>">Přehled</a>
                <a href="./../../index.php?page=nabidka">Zpět do eshopu</a>
        </div>
    </nav>
</header>

<section>
<h2>Cart</h2>
    <?php

    $page = isset($_GET["page"]) ? $_GET["page"] : "polozky";

    if ($page == "polozky") {

        // Když jde ze shopu, ukáže se co už má, ale jinak se to smaže + se smaže záznam,
        // aby se to neukazovalo pořád
        if (isset($_GET["page"]))
        {
            unset($_SESSION["owned"]);
            showCart($conn);
        }
        else
        {
            showOwned($conn);
            showCart($conn);
        }

        //header("Location: cart.php");
    }
    /*if ($page == "platba") {
        showPay($conn);
        //header("Location: cart.php");
    }*/
    if ($page == "ucet") {
        userCheck($conn);
    }
    if ($page == "prehled")
    {
        showSum();
    }

    //showMenu($page);

    ?>

</section>
</body>
</html>