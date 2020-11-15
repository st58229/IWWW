
<head><?php include "headInfo.php" ?></head>
<?php
session_start();



if ($_SESSION["loggedin"] === true && trim($_SESSION["role"]) === "ADMIN") {

    echo "<table style='border: solid 1px black;'>";
    echo "<tr><th>ID</th><th>LOGIN</th><th>NAME</th><th>SURNAME</th><th>EMAIL</th>
    <th>PHONE</th><th>CREATED</th><th>ROLE</th><th></th><th></th></tr>";

    class TableRows extends RecursiveIteratorIterator
    {

        private $id;

        function __construct($it)
        {
            parent::__construct($it, self::LEAVES_ONLY);
        }

        function current()
        {
            return "<td style='width:150px;border:1px solid black;'>" . parent::current() . "</td>";
        }

        function beginChildren()
        {
            $this->id = parent::current();
            echo "<tr>";
        }

        function endChildren()
        {
            echo "<td><a href='userEdit.php?id=$this->id' title='Editovat záznam'>&#x270e</a></td>" . "<td><a href='userDelete.php?id=$this->id' title='Vymazat záznam'>&#x1F5D1</a></td>" . "</tr>" . "\n";
        }
    }


    $Dservername = "localhost";
    $Dusername = "root";
    $Dpassword = "";
    $db = "webdb";

    try {
        $conn = new PDO("mysql:host=$Dservername; dbname=$db", $Dusername, $Dpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT u_id, u_login, u_name, u_surname, u_email, u_phone, u_created, u_role 
                                        FROM users");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $array = $stmt->fetchAll();
        //echo $array[0]["u_id"];

        foreach (new TableRows(new RecursiveArrayIterator($array)) as $k => $v) {
            echo $v;

        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo "</table>";
    echo '<a href="index.php">Vrátit zpět</a>';

}
?>
<p><a href="logout.php">Odhlasit se</a></p>