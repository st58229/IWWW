<link rel="stylesheet" href="./css/nabidka.css">

<h2>Výběr nabízených kurzů</h2>
<?php

try {

    $stmt = $conn->prepare("SELECT thumb, shrt FROM kurzy");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $array = $stmt->fetchAll();

    $maxSizeofRow = 4;
    $rows = 0;
    foreach ($array as $k => $v) {

        if ($rows == 0) echo ("<div id =\"list\">");

        if ($rows < $maxSizeofRow){

            echo '<a href="./index.php?page=detail&kurz='.$v["shrt"].'"><img class="nahledy" src="data:image/png;charset=utf8;base64,'.base64_encode($v["thumb"]).'"></a>';
            $rows++;
            if ($rows == $maxSizeofRow)
            {
                echo ("</div>");
                $rows = 0;
            }
        }

    }
    echo ("</div>");
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>

