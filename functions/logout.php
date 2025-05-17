<?php
require_once __DIR__ . "/auth.php";

requireAuth();
logoutUser();
header('Location: /dean_office/sections/login.php');
exit();
?>