<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SQLite;

use PDO;


// Clase encargada de gestionar la conexión a la base de datos SQLite
class Connection
{
    /**
     * Crea una instancia de PDO conectada a una base de datos SQLite
     * @param string $dbPath Ruta al archivo de la base de datos
     * @return PDO Instancia lista para operar con SQLite
     */
    public static function fromPath(string $dbPath): PDO
    {
        // Obtiene el directorio donde estará la base de datos
        $dir = dirname($dbPath);
        // Si el directorio no existe, lo crea
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        // Crea la conexión PDO a SQLite
        $pdo = new PDO('sqlite:' . $dbPath);
        // Configura PDO para lanzar excepciones en caso de error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Devuelve la instancia lista para usar
        return $pdo;
    }
}


