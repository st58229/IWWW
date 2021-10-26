<?php

/**
 * @var $conn
 */
session_start();

include("../core/dbconnection.php");
?>

<link rel="stylesheet" href="./css/officePage.css">

<table id="waitPayTable">
<tr>
    <th>Uživatel</th>
    <th>Datum objednávky</th>
    <th>Cena objednávky</th>
    <th>Číslo objednávky (VS)</th>
</tr>

<?php
    try {

        $sql = "SELECT id_order, u_login, price, date FROM orders JOIN users u on u.u_id = orders.id_user WHERE status = :status";

        if ($stmt = $conn->prepare($sql)){

            $status = "ongoing";
            $stmt->bindParam(":status", $status , PDO::PARAM_STR);

            if($stmt->execute()){
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $array = $stmt->fetchAll();

                foreach ($array as $k => $v) {

                echo '
                <tr>
                <td><span>'.$v["u_login"].'</span></td>
                <td><span>'.date_format(date_create($v["date"]), "d. m. Y | h:m:s").'</span></td>
                <td><span>'.$v["price"].' Kč</span></td>
                <td><span>'.$v["id_order"].'</span></td>    
                <td><span><input type="submit" value ="Potvrdit zaplacení"></span></td>              
                </tr>';

                }
            }
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>
</table>

<a id="lgout" href="../core/logout.php">Odhlasit se</a>
