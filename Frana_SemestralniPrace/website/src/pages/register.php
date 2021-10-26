<?php

$username = $password = $confirm_password = $name = $surname = $email = $phone = "";
$street = $number = $postalCode = $town = $state = "";
$username_err = $password_err = $confirm_password_err = $email_err = $phone_err = $name_err = $surname_err ="";

if($_SERVER["REQUEST_METHOD"] == "POST"){


    if(empty(trim($_POST["username"]))){
        $username_err = "Vyplňte uživatelské jméno.";
    } else{

        $sql = "SELECT u_id FROM users WHERE u_login = :username";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);


            $param_username = trim($_POST["username"]);


            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Uživatelské jméno je již obsazené.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Jejda! Něco se nepovedlo. Zkuste to později.";
            }

            unset($stmt);
        }
    }


    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Heslo musí obsahovat alespoň 6 znaků.";
    } else{
        $password = trim($_POST["password"]);
    }


    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Prosím potvrďte heslo.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Hesla se neshodují, zkontrolujte hesla.";
        }
    }


    if(empty(trim($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))){
        $email_err = "Zadejte korektní email.";
    } else{

        $sql = "SELECT u_email FROM users WHERE u_email = :email";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);


            $param_email = trim($_POST["email"]);


            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "Email se již používá.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Něco se nepovedlo. Zkuste to později.";
            }


            unset($stmt);
        }
    }

        $sql = "SELECT u_phone FROM users WHERE u_phone = :phone";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);


            $param_phone = trim($_POST["phone"]);

            if($stmt->execute()){
                if($stmt->rowCount() == 1){
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

    $street = trim($_POST["street"]);
    $number = trim($_POST["number"]);
    $postalCode = trim($_POST["postalCode"]);
    $town = trim($_POST["town"]);
    $state = trim($_POST["state"]);



    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)
                            && empty($email_err)    && empty($phone_err))
    {


    //    $sql = "INSERT
    //            INTO users (u_login, u_passwd, u_name, u_surname, u_email, u_phonePrefix, u_phone, u_created, u_role /*u_adress*/)
    //            VALUES (:username, :password, :namev, :surname, :email, :prefix, :phone, :created, :rolev /*:adress*/)";

    //    $sql = "EXECUTE CreateNewUser USING   :username :password, :namev, :surname, :email, :prefix,
    //                                          :phone, :street, :number, :postalCode, :town, :state";

        $sql = "
                INSERT INTO address     (street, number, postalCode, town, state)
                VALUES                  (:street, :numbr, :postalCode, :town, :stat);";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":street", $param_street, PDO::PARAM_STR);
            $stmt->bindParam(":numbr", $param_number, PDO::PARAM_STR);
            $stmt->bindParam(":postalCode", $param_postalCode, PDO::PARAM_STR);
            $stmt->bindParam(":town", $param_town, PDO::PARAM_STR);
            $stmt->bindParam(":stat", $param_state, PDO::PARAM_STR);

            $param_street = $street;
            $param_number = $number;
            $param_postalCode = $postalCode;
            $param_town = $town;
            $param_state = $state;

            if($stmt->execute()){
                $_SESSION["newUserAdress"] = $conn->lastInsertId();
                //header("login.php");
            } else{
                echo "Něco se nepovedlo. Zkus to později. Asi tohle:" . $stmt.error_log();
            }
            //unset($stmt);
        }

        $sql = "INSERT INTO users (u_login, u_passwd, u_name, u_surname, u_email, u_phonePrefix, u_phone, u_created, u_role, u_address)
                VALUES (:username, :password, :namev, :surname, :email, :prefix, :phone, :created, :rolev, :adress)";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":namev", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":surname", $param_surname, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindValue(":prefix", "+420", PDO::PARAM_STR);
            $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);
            $stmt->bindValue(":created", date("Y/m/d"), PDO::PARAM_STR);
            $stmt->bindValue(":rolev", "USER", PDO::PARAM_STR);
            $stmt->bindValue(":adress", $_SESSION["newUserAdress"] , PDO::PARAM_STR);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_name = $name;
            $param_surname = $surname;
            $param_email = $email;
            $param_phone = $phone;


            if($stmt->execute()){
                header("./index.php?page=login"); //TODO Přesměrovat na stránku loginu
            } else{
                echo "Něco se nepovedlo. Zkus to později. Asi tohle:" . $stmt.error_log();
            }
            unset($stmt);
    }

    unset($conn);
}}
//TODO s použitím required se asi můžu vykašlat na ošetřovací ify, uvidíme, ale asi neublíží

?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="./css/login.css">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<body>


<div class="wrapper">
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form method="post">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder="Přihlašovací jméno" required>
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" placeholder="Heslo" required>
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
            <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" placeholder="Potvrzení hesla" required>
            <span class="help-block"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="Email" required>
            <span class="help-block"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" placeholder="Křestní jméno" required>
            <span class="help-block"><?php echo $name_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($surname_err)) ? 'has-error' : ''; ?>">
            <input type="text" name="surname" class="form-control" value="<?php echo $surname; ?>" placeholder="Příjmení" required>
            <span class="help-block"><?php echo $surname_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
            <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>" placeholder="Telefonní číslo" required>
            <span class="help-block"><?php echo $phone_err; ?></span>
        </div>
        <div class="form-group" required">
        <input type="text" name="street" class="form-control" value="<?php echo $street; ?>" placeholder="Ulice" required>
        <span class="help-block"></span>
        </div>
            <div class="form-group" required">
            <input type="text" name="number" class="form-control" value="<?php echo $number; ?>" placeholder="Číslo popisné" required>
            <span class="help-block"></span>
        </div>
        <div class="form-group" required">
        <input type="text" name="postalCode" class="form-control" value="<?php echo $postalCode; ?>" placeholder="PSČ" required>
        <span class="help-block"></span>
        </div>
        <div class="form-group" required">
        <input type="text" name="town" class="form-control" value="<?php echo $town; ?>" placeholder="Město/Vesnice" required>
        <span class="help-block"></span>
        </div>
        <div class="form-group" required">
        <input type="text" name="state" class="form-control" value="<?php echo $state; ?>" placeholder="Stát" required list="states">
        <datalist id="stetes">
            <option value="CZ">Česká republika</option>
            <option value="SK">Slovenská republika</option>
            <option value="EU">Jiné státy EU</option>
            <option value="XX">Mimo EU</option>
        </datalist>
        <span class="help-block"></span>
        </div>

<div class="form-group">
    <input type="submit" class="btn btn-primary" value ="Registrovat se">
    <input type="reset" class="btn btn-default" value="Reset">
</div>
        <p>Máš účet? <a href="./index.php?page=login">Přihlaš se tady</a>.</p>
    </form>
</div>
</body>
</html>
