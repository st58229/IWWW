<link rel="stylesheet" href="./css/headerMenu.css">


<div class="container">
    <div class="logo">
        <a href="index.php?page=home"><img src="./img/logo.png" alt="logo"></a>
    </div>
    <div class="navbar">

        <div class="icon-bar" onclick="Show()">
            <i>ii</i>
            <i></i>
            <i></i>
        </div>

        <ul id="nav-lists">
            <li class="close"><span onclick="Hide()">×</span></li>
            <li><a href="index.php?page=nabidka">📚 Nabídka kurzů</a></li>
            <?php
            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) echo '<li><a href="index.php?page=login">👨‍🎓 Profil</a></li>';
            else echo '<li><a href="index.php?page=login">🔑 Přihlášení</a></li>';
            ?>

            <li><a href="./src/core/cart.php?">🛒 Košík</a></li>
        </ul>

    </div>
</div>


<script>
    var navList = document.getElementById("nav-lists");
    function Show() {
        navList.classList.add("_Menus-show");
    }

    function Hide(){
        navList.classList.remove("_Menus-show");
    }
</script>

