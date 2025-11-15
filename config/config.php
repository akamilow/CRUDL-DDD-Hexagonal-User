<?php

return [
    'db_path' => __DIR__ . '/../var/db/app.sqlite',
    // Cambia este secret para producción
    'auth_secret' => 'ejemplo_secret_muy_seguro',
    'token_ttl_seconds' => 86400, // 1 día ejemplo 
];
