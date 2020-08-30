<?php

require_once "const.php";
require_once "table.php";
function sw_lgoin()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        makeLogin($_POST[A_MAIL], $_POST[A_PASS]);
        header('Location: /');
        die();
    }
    formLogin();
}

function formLogin()
{
    $form = '';
    $email = $_GET[A_MAIL] ?? '';
    $form .= '<h2>Login</h2>
    <form action="/?action=' . A_LOG_IN . '" method="POST">
    <input name="' . A_MAIL . '" type="email" placeholder="Email" value="' . $email . '"/>
    <input name="' . A_PASS . '" type="password" placeholder="Password"/>
    <button type="submit">Login</button></form>';
    echo sprintf(content(), $form);
}

function sw_logout()
{
    session_destroy();
    header("Location: /");
}

function sw_register()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        makeRegistration($_POST[A_MAIL], $_POST[A_PASS], $_POST[A_PASS_CONF]);
        die();
    }
    formRegister();
}

function makeLogin(string $email, string $password)
{
    $users = [];
    if (strlen($password) < 4) {
        header("Location: /?action=" . A_LOG_IN . "&email=$email");
        return;
    }
    if (file_exists(A_JSON_USER_SAVE)) $users = json_decode(file_get_contents(A_JSON_USER_SAVE), true);

    foreach ($users as $user)
        if ($email === $user[A_MAIL] && md5($user[A_SALT] . $password) === $user[A_PASS]) {
            $_SESSION[A_USER] = $user;
            header("Location: /");
            return;
        }

    header("Location: /?action=" . A_LOG_IN . "&email=$email");
}

function makeRegistration(string $email, string $password, string $paccword)
{
    if (strlen($password) < 8 || $paccword !== $password) {
        header("Location: /?action=" . A_LOG_REG);
        return;
    }
    $users = [];
    if (file_exists(A_JSON_USER_SAVE)) $users = json_decode(file_get_contents(A_JSON_USER_SAVE), true);

    foreach ($users as $user) {
        if ($user[A_MAIL] === $email) {
            header("Location: /?action=" . A_LOG_REG);
            return;
        }
    }
    $salt = createSalt();
    $users[] = $user = [
        A_MAIL => $email,
        A_SALT => $salt,
        A_PASS => md5($salt . $password)];
    $_SESSION[A_USER] = $user;
    file_put_contents(A_JSON_USER_SAVE, json_encode($users));
    header("Location: /?action=" . A_LOG_IN . "&email=$email");
    header("Location: /");
}

function formRegister()
{
    $form = '<h2>Register</h2>
    <form action="/?action=' . A_LOG_REG . '" method="POST">
    <input name="' . A_MAIL . '" type="email" placeholder="Email"/>
    <input name="' . A_PASS . '" type="password" placeholder="Password"/>
    <input name="' . A_PASS_CONF . '" type="password" placeholder="Confirm password"/>
    <button type="submit">Register</button></form>';
    echo sprintf(content(), $form);
}

function content(): string
{
    return '<div class="auth_table">'
        . (($user = getAuthUser()) ?
            '<span>' . $user . '</span> | <a href="/?action=' . A_LOG_OUT . '">LOGOUT</a>' :
            '<a href="/?action=' . A_LOG_IN . '">LOGIN</a> | <a href="/?action=' . A_LOG_REG . '">REGISTER</a>')
        . '</div><div>%s</div>';
}

function sw_base()
{
    $user = getAuthUser() ?? 'unknown';
    echo sprintf(content(), drawTable($user, false));
}

function createSalt(int $length = 32)
{
    $abc = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9), ['!']);
    $hash = '';
    $absLen = count($abc);
    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, $absLen - 1);
        $hash .= $abc[$index];
    }
    return $hash;
}

function getAuthUser(): ?string
{
    return $_SESSION[A_USER][A_MAIL] ?? null;
}