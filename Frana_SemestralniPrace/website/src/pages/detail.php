<link rel="stylesheet" href="./css/detail.css">

<?php

// Načíst z DB data, uložit je do prvků stránky (viz návrh)

$kurz = isset($_GET["kurz"]) ? $_GET["kurz"] : "err";
$param_shrt = "";

if ($kurz != "err") {
    $stmt = $conn->prepare("SELECT * FROM kurzy WHERE shrt = :kurz");

    $stmt->bindParam(":kurz", $kurz, PDO::PARAM_STR);

    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $data = $stmt->fetch();

    if ($data == NULL)
    {
        header("index.php");
        exit();
    }

} else {
    echo("Chybný parametr.");
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="generator" content="WYSIWYG Web Builder 14 - http://www.wysiwygwebbuilder.com">
    <link href="detail.css" rel="stylesheet">
</head>

<div id="wb_Image1" style="position:absolute;left:21px;top:159px;width:224px;height:298px;z-index:1;">
    <img src="data:image/png;charset=utf8;base64,<?php echo base64_encode($data["thumb"])?>" id="Image1" alt=""></div>
<div id="wb_Shape1" style="position:absolute;left:281px;top:159px;width:1015px;height:92px;z-index:2;">
    <img src="images/img0001.png" id="Shape1" alt="" style="width:1015px;height:92px;"></div>
<div id="wb_Shape2" style="position:absolute;left:281px;top:266px;width:1015px;height:201px;z-index:3;">
    <img src="images/img0002.png" id="Shape2" alt="" style="width:1015px;height:201px;"></div>
<div id="wb_Shape3" style="position:absolute;left:21px;top:484px;width:1275px;height:233px;z-index:4;">
    <img src="images/img0003.png" id="Shape3" alt="" style="width:1275px;height:233px;"></div>
<a href="./src/core/cart.php?action=add&id=<?php echo ($data["kurz_id"]);?>""><input type="submit" id="Button1"
   name="" value="<?php echo "Zakoupit kurz za ".$data["price"]." Kč"?>"
   style="position:absolute;left:21px;top:733px;width:1275px;height:58px;z-index:5;"></a>
<div id="wb_Text1" style="position:absolute;left:293px;top:171px;width:991px;height:76px;text-align:center;z-index:6;">
    <span style="color:#000000;font-family:Arial;font-size:53px;"><?php echo $data["k_name"]?><br></span></div>
<div id="wb_Text2" style="position:absolute;left:293px;top:277px;width:991px;height:112px;z-index:7;">
    <span style="color:#000000;font-family:Arial;font-size:13px;"><?php echo $data["descr"]?></span></div>
<div id="wb_Text3" style="position:absolute;left:31px;top:496px;width:1253px;height:176px;z-index:8;">
    <span style="color:#000000;font-family:Arial;font-size:13px;"><?php echo $data["detail"]?></span></div>

</html>




<!--<div class="v18_119">
    <div class="name"></div>
    <div class="v23_0"></div>
    <span class="v23_1"><?php /*echo '<img src="data:image/png;base64,' . base64_encode($data["thumb"]) . '"/>';*/?></span>
    <div class="v23_2"></div>
    <div class="v23_3"></div>
    <span class="v23_4"><?php /*echo ($data["k_name"]);*/?></span><span class="v23_5"><?php /*echo ($data["descr"]);*/?></span>
    <div class="v23_6"></div>
    <span class="v23_9"><?php /*echo ($data["detail"]);*/?></span>
    <div class="v23_7"></div>
    <span class="v23_8"><?php /*echo ($data["price"]." Kč");*/?></span>
</div>-->

<!--<form method="post" action="addToCart(--><?php //echo ($data["kurz_id"]);?><!--)"><input type="submit" name = "Přidat do košíku"></form>-->

<!--<a href="./src/core/cart.php?action=add&id=<?php /*echo ($data["kurz_id"]);*/?>""><button>Přidat do košíku</button></a>-->

