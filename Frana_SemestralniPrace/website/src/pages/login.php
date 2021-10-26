<?php

// Pokud je přihlášený = hodí na userPage
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if (trim($_SESSION["role"]) === "USER") header("location: index.php?page=userPage");
    else if (trim($_SESSION["role"]) === "ADMIN") header("location: ./src/admin/adminPage.php");
    else if (trim($_SESSION["role"]) === "OFFICE") header("location: ./src/office/officePage.php");
    else echo "Jejda, role nemá vlastní prostředí, co budeme dělat?";
    exit;
}

$src = isset($_POST["src"]) ? $_POST["src"] : "index";

//V případě volání formuláře z košíku, aby nedošlo k přesměrování na účet
$headerUSER = "location: index.php?page=userPage";
$headerADMIN = "location: ./src/admin/adminPage.php";
$headerOFFICE = "location: ./src/office/officePage.php";
if ($src == "cart")
{
    $headerUSER = "location: cart.php?page=ucet";
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
                            if (trim($_SESSION["role"]) === "USER") header($headerUSER);
                            else if (trim($_SESSION["role"]) === "ADMIN") header($headerADMIN);
                            else if (trim($_SESSION["role"]) === "OFFICE") header($headerOFFICE);
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

<link rel="stylesheet" href="./css/login.css">

    <section class="wrapper">
        <h2>Přihlášení</h2>
        <form <?php /* action="<?php //echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"*/ ?> method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder="Přihlašovací jméno">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" class="form-control" placeholder="Heslo">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Přihlášení">
            </div>
            <p>Nemáš účet? <a href="./index.php?page=register">Registrace</a>.</p>
        </form>
    </section>