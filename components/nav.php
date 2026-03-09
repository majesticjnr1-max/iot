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
                        <a href="/iot/">Home</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">
                        <a href="/iot/about">About Us</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'whatwedo.php') ? 'active' : ''; ?>">
                        <a href="/iot/whatwedo">What We Do</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'initiatives.php') ? 'active' : ''; ?>">
                        <a href="/iot/initiatives">Initiatives</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'ourworks.php') ? 'active' : ''; ?>">
                        <a href="/iot/ourworks">Portfolio</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'progressbar.php') ? 'active' : ''; ?>">
                        <a href="/iot/progressbar">Focus Area</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'ourteam.php') ? 'active' : ''; ?>">
                        <a href="/iot/ourteam">Team</a>
                    </li>

                    <li class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">
                        <a href="/iot/contact">Contact</a>
                    </li>
                </ul>
                <!--mega menu end-->

            </div>
        </div>
    </div>

</header>
<!--header end-->