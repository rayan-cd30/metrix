<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/DashboardData.php';

$user = Auth::requirePageAuth('login.php');
$database = new Database();

$dashboardData = (new DashboardData($database))->build();
extract($dashboardData, EXTR_SKIP);

require __DIR__ . '/components/pages/dashboard.php';
?>
