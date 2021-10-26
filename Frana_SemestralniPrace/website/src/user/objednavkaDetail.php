<html>

<link rel="stylesheet" href="./css/objednavkaDetail.css">

<?php

try {
    if(isset($_GET["obj"])){

        $stmt = $conn->prepare("SELECT * FROM ordersitems JOIN kurzy k 
                                            on k.kurz_id = ordersitems.product_id WHERE order_id = " . $_GET["obj"]);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $array = $stmt->fetchAll();

        $stmt = $conn->prepare("SELECT price FROM orders WHERE id_order = " . $_GET["obj"]);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totalPrice = $stmt->fetch()["price"];
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

$payLiboAPIserver = "https://api.paylibo.com/paylibo/";
$QRgenerator = "generator/czech/image";

$prefix = "115";
$number = "9728000207";
$code = "0100";

$orderNumber = $_GET["obj"] ; // TODO Přesun do objednávky

$message = "Objednávka kurzů - Ez kurzy.cz";

?>

<section class="platba">
    <h2>Děkujeme za vaši objednávku</h2>
<?php
    echo '<img src="'.$payLiboAPIserver.$QRgenerator."?accountPrefix=".$prefix."&accountNumber=".$number.
        "&bankCode=".$code."&amount=".$totalPrice.".00&currency=CZK&vs=".$orderNumber."&message=".$message.'"></img>';
?>
    <div id="detailPay">
        <p>Číslo účtu: <?php echo $number?></p>
        <p>VS: <?php echo $orderNumber?></p>
        <p>Částka: <?php echo $totalPrice?></p>
    </div>

    <div>
        <p>Uvedenou částku uhraďte do pěti pracovních dní na výše uvedený účet.
            Jako Variabilní symbol (VS) používáme číslo vaší objednávky. Objednávka bude dokončena,
            jakmile dorazí peníze na náš bankovní účet. Můžete také využít možnost QR platby skrze QR kód.</p>
    </div>

</section>

<section class="detailObjednavka">
    <?php
            echo '<div id="items">';
            echo '<p>Položka</p>';
            foreach ($array as $v) {
                echo '<p>'.$v["k_name"].'</p>';
            }
            echo '</div>';

            echo '<div id="prices">';
            echo '<p>Cena</p>';
            foreach ($array as $v) {
                echo '<p>'.$v["paid"].' Kč</p>';
            }
            echo '</div>';

            echo '<div id="total">';
            echo '<p>Celková cena: '.$totalPrice.' Kč</p>';
            echo '</div>';
            $_SESSION["detailPrice"] = $v["price"];
?>
</section>

<a href ="./src/user/printObjednavka.php?obj=<?php echo($_GET["obj"])?>" style="text-align: center">Tisknutelná stránka</a>

</html>


