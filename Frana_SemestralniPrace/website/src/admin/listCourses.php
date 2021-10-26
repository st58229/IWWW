<?php

include("../../classes/TableRows.php");

if ($_SESSION["loggedin"] === true && trim($_SESSION["role"]) === "ADMIN") {

    echo "<table style='border: solid 1px black;'>";
    echo "<tr><th>ID</th><th>PREV</th><th>NAME</th><th>SHRT</th><th>DESC</th><th>DETAIL</th>
    <th>PRICE</th><th></th><th></th></tr>";

    try {

        $stmt = $conn->prepare("SELECT kurz_id, thumb, k_name, shrt, descr, detail, price 
                                        FROM kurzy");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $array = $stmt->fetchAll();

        foreach (new TableRows(new RecursiveArrayIterator($array)) as $k => $v) {
            echo $v;
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo "</table>";
}
