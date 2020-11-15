<head><?php include "headInfo.php" ?></head>
<?php
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

$username = $password = $confirm_password = $name = $surname = $email = $phone = "";
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



    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)
                            && empty($email_err)    && empty($phone_err))
    {


        $sql = "INSERT 
                INTO users (u_login, u_passwd, u_name, u_surname, u_email, u_phonePrefix, u_phone, u_created, u_role /*u_adress*/) 
                VALUES (:username, :password, :namev, :surname, :email, :prefix, :phone, :created, :rolev /*:adress*/)";

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
            // TODO Možná adresa, nebo vypustit, pro online není potřeba


            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_name = $name;
            $param_surname = $surname;
            $param_email = $email;
            $param_phone = $phone;


            if($stmt->execute()){
                header("loginForm.php");
            } else{
                echo "Něco se nepovedlo. Zkus to později.";
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
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label>Přihlašovací jméno</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Heslo</label>
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" required>
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
            <label>Potvrzení hesla</label>
            <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" required>
            <span class="help-block"><?php echo $confirm_password_err; ?></span>
        </div>
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
            <input type="submit" class="btn btn-primary" value ="Registrovat se">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>
        <p>Máš účet? <a href="login.php?form=login">Přihlaš se tady</a>.</p>
    </form>
</div>
</body>
</html>
