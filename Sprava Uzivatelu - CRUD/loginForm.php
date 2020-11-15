<head><?php include "headInfo.php" ?></head>

<?php
session_start();

$Dservername = "localhost";
$Dusername = "root";
$Dpassword = "";
$db = "webdb";

try {
    $conn = new PDO("mysql:host=$Dservername; dbname=$db", $Dusername, $Dpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e)
{
    echo $e->getMessage();
}

//TODO připojení do DB nějak samostatný soubor, aby se to jen volalo, to samé formuláře nějak zobecnit a udělat třídy a tak

// Pokud je přihlášený = hodí na userPage
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if (trim($_SESSION["role"]) === "USER") header("location: userPage.php");
    else if (trim($_SESSION["role"]) === "ADMIN") header("location: adminPage.php");
    else echo "Jejda, role nemá vlastní prostředí, co budeme dělat?";
    exit;
}

$username = $password = $role = "";
$username_err = $password_err = "";

// Sepne se když stikne stačítlko ... při postu
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Teoreticky nepotřebný, když jsem nastavil na povinný údaje, ale zatím nechat, vypíše to problémy
    if(empty(trim($_POST["username"]))){
        $username_err = "Zadejte přihlašovací jméno.";
    } else{
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Zadejte heslo.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Ošetření na výpis chyb - jestrli jsou údaje ok - tady možná není potřeba při povinný položce
    if(empty($username_err) && empty($password_err)){

        $sql = "SELECT u_id, u_login, u_passwd, u_role FROM users WHERE u_login = :username";

        if($stmt = $conn->prepare($sql)){

            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);


            $param_username = trim($_POST["username"]);


            if($stmt->execute()){

                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["u_id"];
                        $username = $row["u_login"];
                        $hashed_password = $row["u_passwd"];
                        $role = $row["u_role"];
                        if(password_verify($password, $hashed_password)){

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;
                            // Switchne podle role, ale možná pro všechny userPage a adminovi tam jen zobrazit link na správu ostatních
                            //TODO Přechod na správu uživatelů asi z userpage admina - vylepšit userpage a udělat CSS
                            if (trim($_SESSION["role"]) === "USER") header("location: userPage.php");
                            else if (trim($_SESSION["role"]) === "ADMIN") header("location: adminPage.php");
                            else echo "Jejda, role nemá vlastní prostředí, co budeme dělat?";

                        } else{
                            $password_err = "Heslo nesouhlasí.";
                        }
                    }
                } else{
                    $username_err = "Uživatelské jméno nenalezeno.";
                }
            } else{
                echo "Jejda, něco se nepovedlo. Zkus to za chvíli..";
            }

            unset($stmt);
        }
    }

    unset($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Přihlášení</h2>
        <p>Vyplňtě údaje pro přihlášení</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Přihlašovací jméno</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Heslo</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Přihlášení">
            </div>
            <p>Nemáš účet? <a href="registerForm.php">Registrace</a>.</p>
        </form>
    </div>
</body>
</html>