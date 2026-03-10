# Mitsuki Database Migrations

**Mitsuki Database Migrations** is a lightweight, fluent schema builder for PHP. Built as a powerful wrapper around Phinx, it allows you to define your database structure using a clean, expressive syntax inspired by Laravel's migration system.

---

# ✨ Features

* **Fluent API**: Define tables and columns using clean method chaining.
* **Smart Migrations**: Automatically detects if a table exists to choose between `CREATE` or `ALTER`.
* **Standardized Fields**: Quick helpers for primary keys (`id()`) and tracking (`timestamps()`).
* **Relationship Helpers**: Intelligent foreign key resolution with `foreignIdFor()`.
* **Phinx Powered**: Seamlessly integrates with your existing Phinx environments and commands.
* **CLI Integration**: Optional Mitsuki CLI commands for managing migrations easily.
* **Fully Tested**: High-reliability codebase tested with Pest PHP and Mockery.

---

# 🚀 Installation

## 1. Install the migration library

```bash
composer require mitsuki/db-migrations
```

---

## 2. Install Mitsuki CLI Commands (required if not using the Mitsuki framework)

If you are using this library **outside of the Mitsuki framework**, you must install the command package to access the migration CLI.

```bash
composer require mitsuki/commands
```

This package provides the following commands:

```
migrate
migrate:create
migrate:rollback
migrate:fresh
migrate:status
migrate:init
```

---

# ⚙️ Create the Mitsuki CLI File

To run the migration commands, you must create a **`mitsuki` file at the root of your project**.

Example project structure:

```
project-root
 ├── mitsuki
 ├── composer.json
 ├── vendor/
 └── phinx.php
```

### Example `mitsuki` file

```php
#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Mitsuki\Command\ServerCommand;
use Mitsuki\Console\ConsoleApplication;
use Mitsuki\Database\Command\MigrateCommand;
use Mitsuki\Database\Command\MigrateCreateCommand;
use Mitsuki\Database\Command\MigrateFreshCommand;
use Mitsuki\Database\Command\MigrateRollbackCommand;
use Mitsuki\Database\Command\MigrateStatusCommand;
use Mitsuki\Database\Command\MigrationInit;

$app = new ConsoleApplication([
    new ServerCommand(),
    new MigrateCommand(),
    new MigrateCreateCommand(),
    new MigrateFreshCommand(),
    new MigrateRollbackCommand(),
    new MigrateStatusCommand(),
    new MigrationInit()
]);

$app->run();
```

Then make the file executable:

```bash
chmod +x mitsuki
```

You can now run commands like:

```bash
php mitsuki migrate
php mitsuki migrate:create CreateUsersTable
php mitsuki migrate:rollback
php mitsuki migrate:fresh
php mitsuki migrate:status
```

---

# 🛠 Usage

## Basic Table Creation

Use the `Schema` facade. It coordinates with the Phinx migration instance to execute your definitions.

```php
use Mitsuki\Database\Schema\Schema;
use Mitsuki\Database\Table;
use Mitsuki\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Table $table) {
            $table->string('username', 100);
            $table->string('email')->nullable();
            $table->timestamps();
        }, $this);
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}
```

---

# 🔗 Foreign Keys & Relationships

The `foreignIdFor()` helper simplifies relationship creation:

```php
$table->foreignIdFor(\App\Models\User::class)
      ->constrained()
      ->onDelete('cascade');
```

---

# 💻 Console Commands

## Initialize Migration Environment

Creates the initial Phinx configuration.

```bash
php mitsuki migrate:init
```

---

## Create a Migration

```bash
php mitsuki migrate:create CreateUsersTable
```

---

## Run Migrations

```bash
php mitsuki migrate
```

---

## Rollback Migrations

Rollback the last migration:

```bash
php mitsuki migrate:rollback
```

Rollback multiple migrations:

```bash
php mitsuki migrate:rollback --step=3
```

---

## Refresh Database

Rollback **all migrations** and run them again.

```bash
php mitsuki migrate:fresh
```

---

## Migration Status

```bash
php mitsuki migrate:status
```

---

# ⚙️ Configuration (`phinx.php`)

Mitsuki automatically switches environments based on `.env`.

| Env Var         | Default      | Description                         |
| --------------- | ------------ | ----------------------------------- |
| `ENVIRONEMENT`  | `testing`    | Selects the migration environment   |
| `DB_CONNECTION` | `mysql`      | Database driver used in development |
| `DB_DATABASE`   | `mitsuki_db` | Database name or SQLite path        |

---

# 🧠 Smart Save Logic

When calling `save()`:

1. **If the table does not exist**
   → Mitsuki executes a `CREATE TABLE`.

2. **If the table already exists**
   → Mitsuki switches automatically to `ALTER TABLE` to safely modify the schema.

---

# 🧪 Testing

Run the tests:

```bash
composer test
```

---

# 📄 License

The Mitsuki Database Migrations library is open-sourced software licensed under the **MIT License**.

Developed with ❤️ by **Zgeniuscoders**

📧 [zgeniuscoders@gmail.com](mailto:zgeniuscoders@gmail.com)
