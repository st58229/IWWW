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

$name = $surname = $email = $phone = $emailOLD = $phoneOLD = "";
$email_err = $phone_err = $name_err = $surname_err = "";


if($_SERVER["REQUEST_METHOD"] == "POST"){

    //TODO uživatel si přčes moje ověření nemůže nechat starý email, if(email = email loggedUser) => pustí to

    if(empty(trim($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))){
        $email_err = "Zadejte korektní email.";
    } else{

        $sql = "SELECT u_email FROM users WHERE u_email = :email";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);


            $param_email = trim($_POST["email"]);

            if($stmt->execute()){
                if($stmt->rowCount() == 1 && $email != $emailOLD){
                    $email_err = "Email se již používá.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Jejda! Něco se nepovedlo. Zkuste to později.";
            }


            unset($stmt);
        }
    }

    $sql = "SELECT u_phone FROM users WHERE u_phone = :phone";

    if($stmt = $conn->prepare($sql)){

        $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);


        $param_phone = trim($_POST["phone"]);


        if($stmt->execute()){
            if($stmt->rowCount() == 1 && $phone != $phoneOLD){
                $phone_err = "Telefonní číslo se již používá.";
            } else{
                $phone = trim($_POST["phone"]);
            }
        } else{
            echo "Jejda! Něco se nepovedlo. Zkuste to později.";
        }


        unset($stmt);

    }

    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);



    if(empty($email_err)  && empty($phone_err))
    {


        $sql = "UPDATE users 
                SET u_name = :namev, u_surname = :surname, u_email = :email, u_phone = :phone 
                WHERE u_id = :id ";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":namev", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":surname", $param_surname, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);
            $stmt->bindValue(":id", $_SESSION["uptID"], PDO::PARAM_INT);
            // TODO Možná adresa, nebo vypustit, pro online není potřeba

            echo $_SESSION["uptID"];


            $param_name = $name;
            $param_surname = $surname;
            $param_email = $email;
            $param_phone = $phone;

            if($stmt->execute()){
                // Redirect to login page
                header("location: adminPage.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }


            unset($stmt);
        }
    }


    unset($conn);
    exit;
}

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && trim($_SESSION["role"]) === "ADMIN"){

    $sql = "SELECT u_name, u_surname, u_email, u_phone FROM users WHERE u_id = :id";

    $id = $_GET["id"];
    $_SESSION["uptID"] = $id;

    if($stmt = $conn->prepare($sql)){

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        if($stmt->execute()){

            if($stmt->rowCount() == 1){
                if($row = $stmt->fetch()){
                    $name = $row["u_name"];
                    $surname = $row["u_surname"];
                    $email = $row["u_email"];
                    $phone = $row["u_phone"];
                }
            } else{
                $username_err = "Id se nenašlo, což je hodně divný.";
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        $emailOLD = $email;
        $phoneOLD = $phone;

        unset($stmt);
    }
}

//TODO s použitím required se asi můžu vykašlat na ošetřovací ify, uvidíme, ale asi neublíží

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>


<div class="wrapper">
    <h2>Úprava prodilu - beta</h2>
    <p>Přepiště údaje pro uložení klikni na to tlačítko.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>" required>
            <span class="help-block"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
            <label>Křestní jméno</label>
            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
            <span class="help-block"><?php echo $name_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($surname_err)) ? 'has-error' : ''; ?>">
            <label>Příjmení</label>
            <input type="text" name="surname" class="form-control" value="<?php echo $surname; ?>">
            <span class="help-block"><?php echo $surname_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
            <label>Telefonní číslo</label>
            <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
            <span class="help-block"><?php echo $phone_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Uložit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>
    </form>
</div>
<a href="adminPage.php" >Vrátit zpět</a>
</body>
</html>



