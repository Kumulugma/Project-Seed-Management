<?php

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/config/web.php');
new yii\web\Application($config);

// Usuń istniejącego użytkownika admin jeśli istnieje
$existingUser = app\models\User::findOne(['username' => 'admin']);
if ($existingUser) {
    $existingUser->delete();
    echo "Removed existing admin user\n";
}

// Utwórz nowego użytkownika
$user = new app\models\User();
$user->username = 'admin';
$user->email = 'admin@example.com';
$user->setPassword('admin123');
$user->generateAuthKey();

if ($user->save()) {
    echo "Admin user created successfully!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "Password hash: " . $user->password_hash . "\n";
    echo "Auth key: " . $user->auth_key . "\n";
} else {
    echo "Error creating user:\n";
    print_r($user->errors);
}

// Test walidacji hasła
$testUser = app\models\User::findByUsername('admin');
if ($testUser && $testUser->validatePassword('admin123')) {
    echo "Password validation: SUCCESS\n";
} else {
    echo "Password validation: FAILED\n";
}