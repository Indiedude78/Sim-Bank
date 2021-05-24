<head>
    <title>Simulation Bank</title>
</head>

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
    <div id="welcome-text">
        <h2 id="introduction-header">
            Welcome to the Simulation Bank.<br>This is not a real bank, but you can learn online banking using this bank.
        </h2>
        <h2 id="login-header">
            Please
            <span class="welcome-links"><a href="register.php">register</a></span> or
            <span class="welcome-links"><a href="login.php">login</a></span>
            to continue
        </h2>
    </div>
</div>


<script src="jquery/jquery.js"></script>

<script src="static/js/indexAnimation.js"></script>