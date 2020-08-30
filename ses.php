<?php

const SES_ID = 'session_id';

if ($_COOKIE[SES_ID] ?? []) {
    session_id($_COOKIE[SES_ID]);
    session_start();
} else {
    $sessionId = md5(rand(1, 100000) . md5(time()));
    session_id($sessionId);
    session_start();
    setcookie(SES_ID, $sessionId, time() + 600);
}