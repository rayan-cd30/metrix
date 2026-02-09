<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

function jsonResponse($payload, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function validateText($value, $label, $minLen, $maxLen) {
    $len = strlen($value);
    if ($len < $minLen || $len > $maxLen) {
        throw new InvalidArgumentException("Le champ {$label} doit contenir entre {$minLen} et {$maxLen} caracteres");
    }
    return $value;
}

function validatePositiveInt($value, $label) {
    if (!preg_match('/^\d+$/', (string)$value)) {
        throw new InvalidArgumentException("Le champ {$label} doit etre un entier positif");
    }

    $intValue = (int)$value;
    if ($intValue <= 0) {
        throw new InvalidArgumentException("Le champ {$label} doit etre superieur a 0");
    }

    return $intValue;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Methode non autorisee'], 405);
}

$entity = isset($_POST['entity']) ? trim((string)$_POST['entity']) : '';
if ($entity === '') {
    jsonResponse(['success' => false, 'message' => 'Entite manquante'], 400);
}

$entityConfigs = [
    'domaine' => [
        'table' => 'domaines',
        'required' => ['nom'],
        'field_map' => [
            'nom' => 'nom',
        ],
        'validators' => [
            'nom' => function ($value) {
                return validateText($value, 'nom', 2, 255);
            },
        ],
        'allowed_roles' => ['admin'],
        'manual_id' => true,
        'id_column' => 'id',
        'success_message' => 'Domaine ajoute avec succes',
    ],
    'clause' => [
        'table' => 'clauses',
        'required' => ['nom', 'objective', 'input', 'domaine'],
        'field_map' => [
            'nom' => 'nom',
            'objective' => 'objective',
            'input' => 'input',
            'domaine' => 'domaineid',
        ],
        'validators' => [
            'nom' => function ($value) {
                return validateText($value, 'nom', 1, 120);
            },
            'objective' => function ($value) {
                return validateText($value, 'objective', 2, 10000);
            },
            'input' => function ($value) {
                return validateText($value, 'input', 2, 10000);
            },
            'domaine' => function ($value) {
                return validatePositiveInt($value, 'domaine');
            },
        ],
        'allowed_roles' => ['admin'],
        'manual_id' => true,
        'id_column' => 'id',
        'success_message' => 'Clause ajoutee avec succes',
    ],
    'user' => [
        'table' => 'users',
        'required' => ['email', 'password', 'role'],
        'field_map' => [
            'email' => 'email',
            'role' => 'role',
        ],
        'validators' => [
            'email' => function ($value) {
                $email = strtolower($value);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException('Email invalide');
                }
                if (strlen($email) > 190) {
                    throw new InvalidArgumentException('Email trop long');
                }
                return $email;
            },
            'password' => function ($value) {
                $len = strlen($value);
                if ($len < 8) {
                    throw new InvalidArgumentException('Le mot de passe doit contenir au moins 8 caracteres');
                }
                if ($len > 255) {
                    throw new InvalidArgumentException('Le mot de passe est trop long');
                }
                return $value;
            },
            'role' => function ($value) {
                $role = strtolower($value);
                $allowedRoles = ['admin', 'user'];
                if (!in_array($role, $allowedRoles, true)) {
                    throw new InvalidArgumentException('Role invalide');
                }
                return $role;
            },
        ],
        'pre_insert' => function ($inputData, $db) {
            $existingUser = $db->getUserByEmail($inputData['email']);
            if ($existingUser) {
                throw new InvalidArgumentException('Cet email est deja utilise');
            }
        },
        'transform' => function ($payload, $inputData) {
            $passwordHash = password_hash($inputData['password'], PASSWORD_DEFAULT);
            if ($passwordHash === false) {
                throw new RuntimeException('Impossible de generer le hash du mot de passe');
            }
            $payload['password_hash'] = $passwordHash;
            return $payload;
        },
        'allowed_roles' => ['admin'],
        'manual_id' => false,
        'success_message' => 'Utilisateur cree avec succes',
    ],
];

if (!array_key_exists($entity, $entityConfigs)) {
    jsonResponse(['success' => false, 'message' => 'Entite non supportee: ' . $entity], 400);
}

try {
    $config = $entityConfigs[$entity];

    Auth::requireApiRole($config['allowed_roles'] ?? ['admin']);

    $db = new Database();
    $inputData = [];

    foreach ($config['required'] as $field) {
        $value = isset($_POST[$field]) ? trim((string)$_POST[$field]) : '';
        if ($value === '') {
            throw new InvalidArgumentException('Champs manquants ou invalides');
        }
        $inputData[$field] = $value;
    }

    foreach (($config['validators'] ?? []) as $field => $validator) {
        if (array_key_exists($field, $inputData)) {
            $inputData[$field] = $validator($inputData[$field]);
        }
    }

    $payload = [];
    foreach ($config['field_map'] as $inputField => $columnName) {
        $payload[$columnName] = $inputData[$inputField] ?? null;
    }

    if (isset($config['transform']) && is_callable($config['transform'])) {
        $payload = $config['transform']($payload, $inputData);
    }

    if (isset($config['pre_insert']) && is_callable($config['pre_insert'])) {
        $config['pre_insert']($inputData, $db);
    }

    $idColumn = $config['id_column'] ?? 'id';
    if (!empty($config['manual_id'])) {
        $payload[$idColumn] = $db->getNextId($config['table'], $idColumn);
    }

    $insertResult = $db->insertRow($config['table'], $payload);
    if ($insertResult === false) {
        throw new RuntimeException('Insertion echouee');
    }

    $entityId = $payload[$idColumn] ?? null;
    if (is_int($insertResult)) {
        $entityId = $insertResult;
    }

    jsonResponse([
        'success' => true,
        'entity' => $entity,
        'message' => $config['success_message'] ?? 'Creation reussie',
        'data' => [
            'id' => $entityId,
        ],
    ]);
} catch (InvalidArgumentException $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
}

