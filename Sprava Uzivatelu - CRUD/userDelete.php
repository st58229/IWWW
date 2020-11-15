<head><?php include "headInfo.php" ?></head>
<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$db = "webdb";

try {
    $conn = new PDO("mysql:host=$servername; dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    echo $e->getMessage();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
$sql = "DELETE FROM users WHERE u_id = :id";

if($stmt = $conn->prepare($sql)){

    $stmt->bindParam(":id", $id_param, PDO::PARAM_INT);

    $id_param = $_SESSION["delID"];

    if($stmt->execute()){

        if($stmt->rowCount() == 1){
            header("location: adminPage.php");

        } else{
            echo  "Id se nenašlo, což je hodně divný.";
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }

    unset($stmt);
    exit;

}
}


$login = "";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && trim($_SESSION["role"]) === "ADMIN"){

$sql = "SELECT u_login FROM users WHERE u_id = :id";

$id = $_GET["id"];
$_SESSION["delID"] = $id;

if($stmt = $conn->prepare($sql)){

    $stmt->bindValue(":id", $_GET["id"], PDO::PARAM_INT);


    if($stmt->execute()){

        if($stmt->rowCount() == 1){
            if($row = $stmt->fetch()){
                $login = $row["u_login"];
            }
        } else{
            echo  "Id se nenašlo, což je hodně divný.";
        }
    } else{
        echo "Jejda. Něco se nepovedlo, zkus to za chvíli..";
    }

    unset($stmt);
}
}

?>

Jste si jistí, že chcete smazat uživatele <?php echo $login ?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"><input type="submit" name="btn" class="btn btn-primary" value="Smazat"></form>

<a href="adminPage.php" >Vrátit zpět</a>


