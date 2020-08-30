<?php

require_once "ses.php";
require_once "auth.php";
require_once "const.php";

$action = $_GET[A_ACTN] ?? 'main';

switch ($action) {
    case A_LOG_IN:
        sw_lgoin();
        break;
    case A_LOG_REG:
        sw_register();
        break;
    case A_LOG_OUT:
        sw_logout();
        break;
    case "session":
        print_r($_SESSION);
        break;
    case A_ADDP:
        addPhone($_SESSION['user'][A_MAIL], $_POST[A_PONM], $_POST[A_PHON]);
        break;
    case "main":
    default:
        sw_base();
}