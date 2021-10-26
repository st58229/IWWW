<html>
<link rel="stylesheet" href="css/objednavky.css">

<section id="detailObjednavky">
    <?php

    try {

        $stmt = $conn->prepare("SELECT * FROM orders JOIN ordersitems o on orders.id_order = o.order_id WHERE id_user = ".$_SESSION["id"]);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $array = $stmt->fetchAll();

        echo '<table>
            <th>Číslo objednávky</th>
            <th>Datum a čas objednávky</th>
            <th>Celková hodnota objednávky</th>
            <th>Stav objednávky</th>
            <th>Detail objednávky</th>
            ';
        foreach ($array as $v) {

            echo '
                <tr>
                <td style="text-align: left"><span>'.$v["id_order"].'</span></td>
                <td><span>'.date_format(date_create($v["date"]), "d. m. Y | h:m:s").'</span></td>
                <td><span>'.$v["price"].' Kč</span></td>
                <td><span>'.$v["status"].'</span></td>
                <td><a href = "./index.php?page=userPage&dat=objednavkaDetail&obj='.$v["id_order"].
                '">🔍</a></td>
                </tr>';
        }
        echo '</table>';

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    ?>
</section>
</body>
</html>
