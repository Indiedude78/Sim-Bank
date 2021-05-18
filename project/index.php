<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (is_logged_in()) {
    header("Location: home.php");
}
?>
<div id="image-slider">
    <ul class="slides">
        <li class="image"> <img src="static/images/pennies_jar.jpg" alt="Savings go a long way"> </li>
        <li class="image"> <img src="static/images/online_banking.jpg" alt="Bank online anytime"> </li>
        <li class="image"> <img src="static/images/penny_plant.jpg" alt="Let your money grow"> </li>
        <li class="image"> <img src="static/images/house_loan.jpg" alt="Let your dreams come true"> </li>
    </ul>
</div>

<div id="welcome-text">
    <h2>
        Welcome, please
        <span class="welcome-links"><a href="register.php">register</a></span> or
        <span class="welcome-links"><a href="login.php">login</a></span>
    </h2>
</div>
<script src="jquery/jquery.js"></script>

<script src="static/js/imageSlider.js"></script>