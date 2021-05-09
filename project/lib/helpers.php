<?php
session_start(); //we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in()
{ //Check to see if user is logged in
    return isset($_SESSION["user"]);
}
function has_role($role)
{  //Check to see if user has a role
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_first_name()
{
    if (is_logged_in() and isset($_SESSION["user"]["fname"])) {
        return $_SESSION["user"]["fname"];
    }
}

function get_last_name()
{
    if (is_logged_in() and isset($_SESSION["user"]["lname"])) {
        return $_SESSION["user"]["lname"];
    }
}

function get_username()
{
    if (is_logged_in() and isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
}

function get_email()
{
    if (is_logged_in() and isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
}

function get_id()
{
    if (is_logged_in() and isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
}

function is_private()
{
    if (is_logged_in() and isset($_SESSION["user"]["is_private"])) {
        return $_SESSION["user"]["is_private"];
    }
}

function safer_echo($var)
{
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
    return;
}

function flash($message)
{
    if (isset($_SESSION["flash"])) {
        array_push($_SESSION["flash"], $message);
    } else {
        $_SESSION["flash"] = array();
        array_push($_SESSION["flash"], $message);
    }
}

function get_messages()
{
    if (isset($_SESSION["flash"])) {
        $flashes = $_SESSION["flash"];
        $_SESSION["flash"] = array();
        return $flashes;
    }
    return array();
}

function set_account_num($length)
{
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}

function get_todays_date($timezone)
{
    date_default_timezone_set($timezone);
    $date = date("Y-m-d");
    return $date;
}
