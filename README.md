# Mitsuki Database Migrations

**Mitsuki Database Migrations** is a lightweight, fluent schema builder for PHP. Built as a powerful wrapper around [Phinx](https://phinx.org/), it allows you to define your database structure using a clean, expressive syntax inspired by Laravel's Migration system.

## ✨ Features

* **Fluent API**: Define tables and columns using clean method chaining.
* **Smart Migrations**: Automatically detects if a table exists to choose between `CREATE` or `ALTER` (Add column).
* **Relationship Helpers**: Intelligent foreign key resolution with `foreignIdFor()`.
* **Phinx Powered**: Seamlessly integrates with your existing Phinx environments and commands.
* **Fully Tested**: High-reliability codebase tested with Pest PHP and Mockery.

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require mitsuki/mitsuki-db-migrations

```

---

## 🛠 Usage

### 1. Basic Table Creation

In your Phinx migration file, use the `Schema` facade. It coordinates with the Phinx migration instance to execute your definitions.

```php
use Mitsuki\Database\Schema\Schema;
use Mitsuki\Database\Table;
use Mitsuki\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Table $table) {
            $table->string('username', 100);
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('bio');
        }, $this);
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}

```

### 2. 🔗 Foreign Keys & Relationships

The `foreignIdFor()` method simplifies relationship management by automatically guessing column and table names based on your model classes.

#### Automatic Resolution

By default, it assumes a singular class name maps to a plural table (e.g., `User` -> `users`).

```php
Schema::create('posts', function (Table $table) {
    $table->string('title');
    
    // Creates an integer column 'user_id' and links it to 'users.id'
    $table->foreignIdFor(\App\Models\User::class)
          ->constrained()
          ->onDelete('cascade')
          ->onUpdate('restrict');
}, $this);

```

#### Manual Configuration

If you follow a different naming convention, you can specify everything manually:

```php
$table->foreignIdFor(\App\Models\User::class, 'author_id')
      ->constrained('staff_members')
      ->onDelete('set null');

```

| Method | Description |
| --- | --- |
| `constrained(table)` | Defines the target table (optional if following conventions). |
| `onDelete(action)` | Sets SQL action: `cascade`, `restrict`, `set null`, `no action`. |
| `onUpdate(action)` | Sets the action when the parent primary key is updated. |

### 3. Column Options

Every column type supports additional modifiers to refine your database schema:

```php
$table->string('code')->limit(10)->nullable();
$table->decimal('price', 10, 2)->default(0.00);
$table->text('content')->comment('The main body of the article');

```

---

## 🧠 Smart Save Logic

Mitsuki is designed to be safe. When you call `save()`, the library checks the database state:

1. **If the table doesn't exist**: It executes a `CREATE TABLE` statement.
2. **If the table exists**: It automatically switches to `ALTER TABLE` to add your new columns without destroying existing data.

---

## 🧪 Testing

We take stability seriously. The library is fully covered by **Pest PHP** unit tests.

```bash
composer test

```

---

## 📄 License

The Mitsuki Database Migrations library is open-sourced software licensed under the [MIT license](https://www.google.com/search?q=LICENSE).

**Developed with ❤️ by [Zgeniuscoders**](mailto:zgeniuscoders@gmail.com)

---