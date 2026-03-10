<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!--header start-->
<header id="header" class="tt-nav nav-border-bottom">

    <div class="header-sticky light-header ">

        <div class="container">

            <div id="materialize-menu" class="menuzord">

                <!--logo start-->
                <a href="index.html" class="logo-brand">
                    <img class="retina" src="assets/img/icons/IoT.png" alt="IoT logo" />
                </a>
                <!--logo end-->

                <!--mega menu start-->
                <ul class="menuzord-menu pull-right">
                    <li class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                        <a href="index.php">Home</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">
                        <a href="about.php">About Us</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'whatwedo.php') ? 'active' : ''; ?>">
                        <a href="whatwedo.php">What We Do</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'initiatives.php') ? 'active' : ''; ?>">
                        <a href="initiatives.php">Initiatives</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'ourworks.php') ? 'active' : ''; ?>">
                        <a href="ourworks.php">Portfolio</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'progressbar.php') ? 'active' : ''; ?>">
                        <a href="progressbar.php">Focus Area</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'ourteam.php') ? 'active' : ''; ?>">
                        <a href="ourteam.php">Team</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">
                        <a href="contact.php">Contact</a>
                    </li>
                </ul>
                <!--mega menu end-->

            </div>
        </div>
    </div>

</header>
<!--header end-->