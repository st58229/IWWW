<link rel="stylesheet" href="./css/home.css">

<?php

?>

<section id="hero">
        <h1 class="pageHeading">Naučte se anglicky pohodlně</h1>
        <h3 style="text-shadow: 5px 5px 20px #FFFFFF">Online kurzy pro začátečníky i pokročilé</h3>
        <a href="./../application/free.php"><button class="main_button">Vyzkoušet</button></a>
</section>

<div id ="popular">
    <h2>Výběr nabízených kurzů</h2>
<?php
try {

    $stmt = $conn->prepare("SELECT thumb, shrt FROM kurzy");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $array = $stmt->fetchAll();

    $maxSize = 4;
    $rows = 0;
    foreach ($array as $k => $v) {

        if ($rows == 0) echo ("<div id =\"list\">");

        if ($rows < $maxSize){

            echo '<a href="./index.php?page=detail&kurz='.$v["shrt"].'"><img id="nahledovky"" src="data:image/png;charset=utf8;base64,'.base64_encode($v["thumb"]).'"></a>';
            $rows++;
            if ($rows == $maxSize)
            {
                break;
            }
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
?>
</div>


<!--<script type="text/javascript" src="./js/slideshow.js"></script>
<body onload="showSlides();">-->

<!-- Slideshow container -->
<!--<div class="slideshow-container">-->

    <!-- Full-width images with number and caption text -->
   <!-- <div class="mySlides fade">
        <div class="numbertext">1 / 3</div>
        <img src="./img/Image1.jpg" style="width:100%">
        <div class="text">Caption Text</div>
    </div>

    <div class="mySlides fade">
        <div class="numbertext">2 / 3</div>
        <img src="./img/Image2.jpg" style="width:100%">
        <div class="text">Caption Two</div>
    </div>

    <div class="mySlides fade">
        <div class="numbertext">3 / 3</div>
        <img src="./img/Image3.jpg" style="width:100%">
        <div class="text">Caption Three</div>
    </div>-->

    <!-- Next and previous buttons -->
    <!--<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
    <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>
<br>-->

<!-- The dots/circles -->
<!--<div style="text-align:center">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>
</div>-->