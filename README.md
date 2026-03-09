# Mitsuki Database Migrations

**Mitsuki Database Migrations** is a lightweight, fluent schema builder for PHP. Built as a powerful wrapper around [Phinx](https://phinx.org/), it allows you to define your database structure using a clean, expressive syntax inspired by Laravel's Migration system.

## ✨ Features

* **Fluent API**: Define tables and columns using clean method chaining.
* **Smart Migrations**: Automatically detects if a table exists to choose between `CREATE` or `ALTER`.
* **Standardized Fields**: Quick helpers for primary keys (`id()`) and tracking (`timestamps()`).
* **Relationship Helpers**: Intelligent foreign key resolution with `foreignIdFor()`.
* **Phinx Powered**: Seamlessly integrates with your existing Phinx environments and commands.
* **Fully Tested**: High-reliability codebase tested with Pest PHP and Mockery.

---

## 🚀 Installation & Setup

### 1. Install via Composer

```bash
composer require mitsuki/db-migrations

```

### 2. Initialize Phinx

Ensure your `phinx.php` is configured to use the Mitsuki template and environment variables.

---

## 🛠 Usage

### 1. Basic Table Creation

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
            $table->timestamps(); // Adds 'created_at' and 'updated_at'
        }, $this);
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}

```

### 2. 🔗 Foreign Keys & Relationships

The `foreignIdFor()` method simplifies relationship management by automatically guessing column and table names.

```php
$table->foreignIdFor(\App\Models\User::class)
      ->constrained()
      ->onDelete('cascade');

```

---

## 💻 Console Commands

Mitsuki leverages the Phinx CLI. Here are the essential commands for your workflow:

### Create a New Migration

Generate a new migration file using the Mitsuki stub:

```bash
vendor/bin/phinx create MyNewMigration

```

### Run Migrations

Execute all pending migrations:

```bash
# Running in default environment (Testing/SQLite)
vendor/bin/phinx migrate

# Force a specific environment (Development/MySQL)
vendor/bin/phinx migrate -e development

```

### Rollback

Undo the last migration:

```bash
vendor/bin/phinx rollback

```

### Check Status

See the list of migrated and pending files:

```bash
vendor/bin/phinx status

```

---

## ⚙️ Configuration (`phinx.php`)

Mitsuki handles environment switching automatically based on your `.env` file:

| Env Var | Default | Description |
| --- | --- | --- |
| `ENVIRONEMENT` | `testing` | Switches between `testing` (SQLite) and `development` (MySQL). |
| `DB_CONNECTION` | `mysql` | The database driver to use in development. |
| `DB_DATABASE` | `mitsuki_db` | The name of your database or path to SQLite file. |

---

## 🧠 Smart Save Logic

Mitsuki is designed to be safe. When you call `save()`, the library checks the database state:

1. **If the table doesn't exist**: It executes a `CREATE TABLE` statement (with `id => false` to let the Blueprint manage the primary key).
2. **If the table exists**: It automatically switches to `ALTER TABLE` to add your new columns without destroying existing data.

---

## 🧪 Testing

We take stability seriously. The library is fully covered by **Pest PHP** unit tests.

```bash
composer test

```

---

## 📄 License

The Mitsuki Database Migrations library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

**Developed with ❤️ by [Zgeniuscoders**](mailto:zgeniuscoders@gmail.com)

---