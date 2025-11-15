# CRUDL DDD Hexagonal (PHP + SQLite)

Backend en PHP aplicando Domain-Driven Design con Arquitectura Hexagonal. Implementa CRUDL de Usuario y login con token HMAC (tipo JWT sencillo). Persistencia en SQLite.

## Requisitos
- PHP 8.1+ con extensión PDO_SQLITE habilitada

## Estructura
- `src/Domain` Entidades e interfaces del dominio
- `src/Application` Casos de uso/servicios de aplicación
- `src/Infrastructure` Implementacion (SQLite)
- `public/index.php` Router HTTP simple
- `config/config.php` Configuración DB
- `scripts/migrate.php` Migración de base de datos
- `var/db/app.sqlite` Base de datos (ojo se crea al migrar)

## Primer uso
1. Aplicar migración (crea la base de datos y tabla):

```powershell
php .\scripts\migrate.php
```

2. Iniciar servidor embebido:

```powershell
php -S localhost:8000 -t public
```

## Endpoints
- `GET /` salud
- `POST /login` body: `{ "email": "", "password": "" }` → `{ token }`
- `POST /users` crear: `{ name, email, phone?, password }`
- `GET /users` listar todos
- `GET /users/{id}` obtener uno
- `PUT /users/{id}` actualizar: `{ name?, email?, phone?, password? }`
- `DELETE /users/{id}` eliminar

## Postman

Instrucciones y ejemplos para crear/importar las peticiones.

1. Crear usuario (POST `/users`) - Body JSON:

```json
{
	"name": "Alice",
	"email": "alice@example.com",
	"phone": "555-1234",
	"password": "Secret123"
}
```

2. Login (POST `/login`) - Body JSON:

```json
{
	"email": "alice@example.com",
	"password": "Secret123"
}
```

3. Obtener lista de usuarios (GET `/users`).

4. Obtener usuario por id (GET `/users/{id}`).

5. Actualizar usuario (PUT `/users/{id}`) - Body JSON de ejemplo:

```json
{
	"phone": "32856595"
}
```

6. Eliminar usuario (DELETE `/users/{id}`).