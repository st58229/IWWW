<?php

$name = $surname = $email = $phone = $emailOLD = $phoneOLD = $street = $number = $postalCode = $town = $state ="";
$email_err = $phone_err = $name_err = $surname_err = "";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){

    $sql = "SELECT u_name, u_surname, u_email, u_phone, street, number, postalCode, town, state 
            FROM users JOIN address a on a.address_id = users.u_address WHERE u_id = :id";

    if($stmt = $conn->prepare($sql)){

        $stmt->bindValue(":id", $_SESSION["id"], PDO::PARAM_INT);

        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                if($row = $stmt->fetch()){

                    // Kontaktní údaje
                    $name = $row["u_name"];
                    $surname = $row["u_surname"];
                    $email = $row["u_email"];
                    $phone = $row["u_phone"];

                    // Fakturační adresa
                    $street = $row["street"];
                    $number = $row["number"];
                    $postalCode = $row["postalCode"];
                    $town = $row["town"];
                    $state = $row["state"];
                }
            } else{
                $username_err = "Id se nenašlo, což je hodně divný.";
            }
        } else{
            echo "Jedja, něco se nepovedlo. Zkus to za chvíli.";
        }

        $emailOLD = $email;
        $phoneOLD = $phone;

        unset($stmt);
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

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
            echo "Oops! Něco se nepovedlo. Zkuste to později.";
        }


        unset($stmt);

    }

    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);

    if(empty($email_err) && empty($phone_err))
    {


        $sql = "UPDATE users JOIN address a on a.address_id = users.u_address
                SET u_name = :namev, u_surname = :surname, u_email = :email, u_phone = :phone, 
                    street = :street, number = :number, postalCode = :postalCode, town = :town, state = :state 
                WHERE u_id = :id ";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":namev", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":surname", $param_surname, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);
            $stmt->bindValue(":id", $_SESSION["id"], PDO::PARAM_INT);

            $stmt->bindParam(":street", $param_street, PDO::PARAM_STR);
            $stmt->bindParam(":number", $param_number, PDO::PARAM_STR);
            $stmt->bindParam(":postalCode", $param_postalCode, PDO::PARAM_STR);
            $stmt->bindParam(":town", $param_town, PDO::PARAM_STR);
            $stmt->bindParam(":state", $param_state, PDO::PARAM_STR);
            // TODO Možná adresa, nebo vypustit, pro online není potřeba - Je kvůli fakturaci

            $param_name = $name;
            $param_surname = $surname;
            $param_email = $email;
            $param_phone = $phone;

            $param_street = $street;
            $param_number = $number;
            $param_postalCode = $postalCode;
            $param_town = $town;
            $param_state = $state;


            if($stmt->execute()){
                header("location: index.php?page=userPage&dat=nastaveni");
            } else{
                echo "Something went wrong. Please try again later.";
            }


            unset($stmt);
        }
    }


    unset($conn);
}
//TODO s použitím required se asi můžu vykašlat na ošetřovací ify, uvidíme, ale asi neublíží

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="./css/login.css">
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
        <div class="form-group" required">
            <label>Ulice</label>
            <input type="text" name="street" class="form-control" value="<?php echo $street; ?>">
            <span class="help-block"></span>
        </div>
        <div class="form-group" required">
            <label>Číslo popisné</label>
            <input type="text" name="number" class="form-control" value="<?php echo $number; ?>">
            <span class="help-block"></span>
        </div>
        <div class="form-group" required">
            <label>PSČ</label>
            <input type="text" name="postalCode" class="form-control" value="<?php echo $postalCode; ?>">
            <span class="help-block"></span>
        </div>
        <div class="form-group" required">
            <label>Město</label>
            <input type="text" name="town" class="form-control" value="<?php echo $town; ?>">
            <span class="help-block"></span>
        </div>
        <div class="form-group" required">
            <label>Stát</label>
            <input type="text" name="state" class="form-control" value="<?php echo $state; ?>">
            <span class="help-block"></span>
        </div>

<div class="form-group">
            <input type="submit" class="btn btn-primary" value="Uložit">
            <input type="reset" class="btn btn-default" value="Smazat všechny údaje">
        </div>
    </form>
</div>
</body>
</html>
