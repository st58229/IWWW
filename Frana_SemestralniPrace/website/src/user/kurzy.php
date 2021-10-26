<html>
<section>
    <?php

    try {

        $stmt = $conn->prepare("SELECT * FROM ownercourses JOIN kurzy k on ownercourses.kurz_id = k.kurz_id WHERE user_id = " . $_SESSION["id"]);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $array = $stmt->fetchAll();

        foreach ($array as $v) {

            //TODO Link na daný kurz - až bude aplikace
            echo '<a href="./../application/free.php"><img style="padding: 15px" src="data:image/png;charset=utf8;base64,'.base64_encode($v["thumb"]).'"></a>';

        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    ?>
</section>
</body>
</html>

