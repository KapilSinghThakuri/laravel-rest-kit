# RestKit (Laravel REST API Toolkit)

RestKit is a reusable, framework-level REST API infrastructure package for Laravel.
It provides standardized API responses, exception handling, request control, pagination, versioning, and other API best practices out-of-the-box.

> ⚠️ **Status:**  
> RestKit is currently under active development.  
> APIs may change until the first stable release (`v1.0.0`).

---

## ✨ What is RestKit?

RestKit is not a business-logic package.  
It is an **API infrastructure layer** that gives your Laravel projects:

- Standardized API responses
- API-friendly exception handling
- Request normalization
- Pagination & metadata
- Versioning support
- Security & consistency

This allows you to build APIs that behave the same way across all your projects.

---

## 📦 Installation (Development Version)

Because RestKit is still in development, you must install it from GitHub.

### 1. Add the repository to your Laravel project

In your Laravel project’s `composer.json`:

```json
   "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/KapilSinghThakuri/laravel-rest-kit"
        }
    ]
```

### 2. Require the package

```bash
    composer require kapilsinghthakuri/rest-kit:@dev
```

Composer will fetch the package from GitHub and install it into your project.

### 3. Publishing Configuration

RestKit ships with a default configuration file.

To publish it into your Laravel project:

```bash
php artisan vendor:publish --tag=rest-kit-config
```

This will create:

config/rest-kit.php

You can now customize how RestKit behaves for your project.

### 4. Using RestKit

After installation, you can:

Extend the base API controller

Use standardized API responses

Read configuration via the RestKit helper

Example:

```php
use Kapilsinghthakuri\RestKit\Responses\ApiResponse;

return ApiResponse::success(['user' => $user]);
```

Or inside a controller extending the package controller:

```php
return $this->success($data, 'Data loaded');
```

### 5. Updating RestKit

When new features or fixes are added to RestKit:

```bash
composer update kapilsinghthakuri/rest-kit
```

This will pull the latest commits from the `main` branch.

### 🚧 Development Status

RestKit is currently:

Not versioned (pre-v1)

Subject to breaking changes

Intended for early adopters and testing

If you are using it in production, it is recommended to lock to a specific commit:

```bash
composer require kapilsinghthakuri/rest-kit:dev-main#<commit-hash>
```

### 🤝 Contributing

RestKit is designed to grow as a professional API infrastructure layer.
Contributions, feedback, and suggestions are welcome.

### 🧑‍💻 Author

Kapil Singh Thakuri
GitHub: <https://github.com/KapilSinghThakuri>
