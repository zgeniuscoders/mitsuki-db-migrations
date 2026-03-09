<?php 

namespace Mitsuki\Database\Schema;

use Closure;
use Mitsuki\Database\Table;
use Mitsuki\Database\Migrations\Migration;

/**
 * The Schema class provides a static interface for defining database tables.
 * * It acts as the entry point for the migration builder, coordinating the 
 * interaction between Mitsuki's Table blueprint and the underlying Phinx migration.
 * * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 */
class Schema {
    /**
     * Create a new table on the schema.
     *
     * @param string $tableName The name of the table to be created.
     * @param Closure $callback A closure receiving a Table instance to define columns.
     * @param Migration $migration The current Mitsuki migration instance.
     * @return void
     */
    public static function create(string $tableName, Closure $callback, Migration $migration): void 
    {

        $phinxTable = $migration->table($tableName);

        $blueprint = new Table($phinxTable);

        $callback($blueprint);

        $blueprint->save();
    }
}