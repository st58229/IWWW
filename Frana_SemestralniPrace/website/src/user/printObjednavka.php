<link rel="stylesheet" href="./css/printObjednavka.css">

<body onload="window.print()">
<?php

/**
 * @var $conn
 */
session_start();
include("../core/dbconnection.php");
?>
<?php

$item = $array = "";

try {
    if(isset($_GET["obj"])){

        $sql = "SELECT * FROM orders JOIN users u on u.u_id = orders.id_user WHERE id_order = :obj";

        if ($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":obj", $_GET["obj"] , PDO::PARAM_STR);

            if($stmt->execute()){
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $item = $stmt->fetch();
            }
        }

        $stmt = $conn->prepare("SELECT price FROM orders WHERE id_order = " . $_GET["obj"]);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $totalPrice = $stmt->fetch()["price"];

        $sql = "SELECT * FROM ordersitems JOIN kurzy k on k.kurz_id = ordersitems.product_id WHERE order_id = " . $_GET["obj"];

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $array = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="generator" content="WYSIWYG Web Builder 14 - http://www.wysiwygwebbuilder.com">
</head>
<body>
<div id="wb_Shape1" style="position:absolute;left:13px;top:80px;width:459px;height:171px;z-index:0;">
    <img src="images/img0001.png" id="Shape1" alt="" style="width:459px;height:171px;"></div>
<div id="wb_Shape2" style="position:absolute;left:503px;top:80px;width:459px;height:171px;z-index:1;">
    <img src="images/img0002.png" id="Shape2" alt="" style="width:459px;height:171px;"></div>

<div id="wb_Text1" style="position:absolute;left:26px;top:92px;width:433px;height:147px;z-index:3;">
    <span style="color:#000000;font-family:Arial;font-size:13px;line-height:16px;">Dodavatel:<br><br></span><span style="color:#000000;font-family:'Arial CE';font-size:13px;line-height:23px;"><strong>Ez kurzy s.r.o</strong><br>Fale</span><span style="color:#000000;font-family:Arial;font-size:13px;line-height:23px;">šná ulice 45, 142 06 Neznámov<br>IČ: 12345678 DIČ: CZ12345678<br>Banka: Kanáry CZK, Účet: XXXXXXX<br>Obchodní rejstřík, městký soud v Neznámově, složka 124685</span></div>
<div id="wb_Text2" style="position:absolute;left:516px;top:92px;width:433px;height:124px;z-index:4;">
    <span style="color:#000000;font-family:Arial;font-size:13px;line-height:16px;">Odběratel:<br><br></span><span style="color:#000000;font-family:'Arial CE';font-size:13px;line-height:23px;"><strong><?php echo $item["u_name"].' '.$item["u_surname"]?></strong></span><span style="color:#000000;font-family:Arial;font-size:13px;line-height:23px;"></span><span style="color:#000000;font-family:'Arial CE';font-size:13px;line-height:23px;"><br>Ulice </span><span style="color:#000000;font-family:Arial;font-size:13px;line-height:23px;">č</span><span style="color:#000000;font-family:'Arial CE';font-size:13px;line-height:23px;">.p., <br>PS</span><span style="color:#000000;font-family:Arial;font-size:13px;line-height:23px;">Č</span><span style="color:#000000;font-family:'Arial CE';font-size:13px;line-height:23px;"> M</span><span style="color:#000000;font-family:Arial;font-size:13px;line-height:23px;">ě</span><span style="color:#000000;font-family:'Arial CE';font-size:13px;line-height:23px;">sto<br></span></div>
<table style="position:absolute;left:13px;top:375px;width:952px;height:216px;z-index:5;" id="Table1">

    <tr>
        <td class="cell0"><span style="color:#333333;font-family:Arial;font-size:13px;line-height:16px;"> Položky</span></td>
        <td class="cell1"><span style="color:#333333;font-family:Arial;font-size:13px;line-height:16px;"> Cena</span></td>
    </tr>

    <?php
    foreach ($array as $v) {

        echo '
        <tr>
        <td class="cell2"><span style="color:#333333;font-family:Arial;font-size:13px;line-height:16px;">'.$v["k_name"].'</span></td>
        <td class="cell3"><span style="color:#333333;font-family:Arial;font-size:13px;line-height:16px;">'.$v["paid"].' Kč</span></td>
        </tr>';
    }
    ?>
</table>
<hr id="Line1" style="position:absolute;left:13px;top:591px;width:952px;z-index:6;">
<div id="wb_Text3" style="position:absolute;left:13px;top:609px;width:952px;height:16px;text-align:center;z-index:7;">
    <span style="color:#000000;font-family:Arial;font-size:13px;">Cena celkem: <?php echo $totalPrice.' Kč' ?></span></div>
<div id="wb_Shape3" style="position:absolute;left:15px;top:285px;width:949px;height:44px;z-index:8;">
    <img src="images/img0003.png" id="Shape3" alt="" style="width:949px;height:44px;"></div>
<div id="wb_Text4" style="position:absolute;left:26px;top:291px;width:936px;height:72px;z-index:9;">
    <span style="color:#000000;font-family:Arial;font-size:13px;line-height:24px;">Způsob platby: <?php echo $item["pay"] ?><br></span><span style="color:#000000;font-family:Arial;font-size:13px;line-height:16px;"><br><br></span></div>
<div id="wb_Text5" style="position:absolute;left:13px;top:12px;width:949px;height:49px;text-align:center;z-index:10;">
    <span style="color:#000000;font-family:'Times New Roman';font-size:43px;">Faktura k objednávce č. <?php echo $item["id_order"]?> ze dne <?php echo date_format(date_create($item["date"]), "d. m. Y")?></span></div>
</body>
</html>