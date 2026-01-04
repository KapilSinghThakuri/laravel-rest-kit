# Laravel Rest Kit

**Laravel Rest Kit** is a reusable REST API toolkit for Laravel projects, providing standardized responses, exception handling, validation, pagination, authentication helpers, versioning, and API best practices out-of-the-box.

## Features

- 🚀 **Standardized API Responses**: Consistent JSON structure for success and error responses.
- 🛡️ **Exception Handling**: Custom exception classes that automatically render uniform error responses.
- 🛠️ **Base API Controller**: A ready-to-use controller class with helper methods.
- 🔌 **Middleware**: Utilities like `ForceJsonResponse` to ensure proper API header handling.
- ⚙️ **Configurable**: extensive configuration for debugging, pagination defaults, and API versioning.

## Installation

You can install the package via composer:

```bash
composer require kapilsinghthakuri/rest-kit
```

After installation, you can publish the configuration file:

```bash
php artisan vendor:publish --tag=rest-kit-config
```

## Configuration

The published config file `config/rest-kit.php` allows you to customize the behavior:

```php
return [
    // Force all requests to accept JSON
    'force_json' => true,

    // Add debug information (timestamp, memory usage) to responses
    'debug' => env('APP_DEBUG', false),

    // Pagination settings
    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    // Versioning settings
    'versioning' => [
        'enabled' => true,
        'default' => 'v1',
        'header' => 'X-API-Version',
    ],
];
```

## Usage

### 1. Standardized Responses

Use the `ApiResponse` class to return consistent JSON responses anywhere in your application.

```php
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;

// Success Response
return ApiResponse::success($data, 'User created successfully', 201);

// Error Response
return ApiResponse::error('User not found', 404);
```

**Response Structure:**

```json
{
    "status": "success",
    "message": "User created successfully",
    "data": { ... }
}
```

If debug mode is enabled, it appends a `debug` object with timestamp and memory usage.

### 2. Base API Controller

Extend the `ApiController` to gain access to helper methods `$this->success()` and `$this->error()`.

```php
namespace App\Http\Controllers;

use Kapilsinghthakuri\RestKit\Http\Controllers\ApiController;

class UserController extends ApiController
{
    public function index()
    {
        $users = User::all();
        return $this->success($users, 'Users retrieved successfully');
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success($user);
    }
}
```

### 3. Exception Handling

Throw `ApiException` to automatically return a formatted error response.

```php
use Kapilsinghthakuri\RestKit\Exceptions\ApiException;

throw new ApiException('Invalid transaction', 400);
```

### 4. Middleware

The `ForceJsonResponse` middleware ensures that incoming requests have the `Accept: application/json` header header set, which helps Laravel treat them as API requests (e.g., for validation errors).

You can register it in your application's middleware stack (usually in `bootstrap/app.php` for Laravel 11 or `app/Http/Kernel.php` for older versions).

## License

Proprietary. Please check composer.json for more information.
