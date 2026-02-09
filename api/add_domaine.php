<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['entity'] = 'domaine';
}

require __DIR__ . '/create_entity.php';
