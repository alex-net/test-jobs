<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' =>  ENV_DATA['DB_DSN'] ?? 'mysql:host=localhost;dbname=yii2basic',
    'username' => ENV_DATA['DB_USER'] ?? 'root',
    'password' => '',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
