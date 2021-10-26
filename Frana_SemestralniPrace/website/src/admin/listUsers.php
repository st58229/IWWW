<?php

include("../../classes/TableRows.php");

if ($_SESSION["loggedin"] === true && trim($_SESSION["role"]) === "ADMIN") {

    echo "<table style='border: solid 1px black;'>";
    echo "<tr><th>ID</th><th>LOGIN</th><th>NAME</th><th>SURNAME</th><th>EMAIL</th>
    <th>PHONE</th><th>CREATED</th><th>ROLE</th><th></th><th></th></tr>";

    try {

        $stmt = $conn->prepare("SELECT u_id, u_login, u_name, u_surname, u_email, u_phone, u_created, u_role 
                                        FROM users");
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
