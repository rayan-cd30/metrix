<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['entity'] = 'clause';
}

require __DIR__ . '/create_entity.php';
