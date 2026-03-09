<?php

namespace Mitsuki\Database;

use Mitsuki\Database\Definitions\ColumnDefinition;
use Mitsuki\Database\Definitions\ForeignKeyDefinition;
use Phinx\Db\Table as PhinxTable;

/**
 * Blueprint for managing database table structure.
 *
 * This class provides a fluent API to define columns and foreign key constraints,
 * acting as a bridge between Mitsuki's schema builder and the Phinx backend.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 */
class Table
{
    /** @var PhinxTable The underlying Phinx table instance */
    protected PhinxTable $phinxTable;

    /** @var ForeignKeyDefinition[] List of foreign key constraints to apply */
    protected array $foreignKeys = [];

    /** @var ColumnDefinition[] List of columns to be added to the table */
    protected array $columns = [];

    /**
     * Create a new Table blueprint instance.
     *
     * @param PhinxTable $phinxTable
     */
    public function __construct(PhinxTable $phinxTable)
    {
        $this->phinxTable = $phinxTable;
    }

    /**
     * Add a string column to the table.
     *
     * @param string $name
     * @param int $length
     * @return ColumnDefinition
     */
    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'string');
        $column->limit($length);
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Add a text column to the table.
     *
     * @param string $name
     * @return ColumnDefinition
     */
    public function text(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'text');
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Add a boolean column to the table.
     *
     * @param string $name
     * @return ColumnDefinition
     */
    public function boolean(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'boolean');
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Add a decimal column with specific precision and scale.
     *
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return ColumnDefinition
     */
    public function decimal(string $name, int $precision = 10, int $scale = 2): ColumnDefinition
    {
        $column = (new ColumnDefinition($name, 'decimal'))
            ->precision($precision)
            ->scale($scale);
        $this->columns[] = $column;
        return $column;
    }

    /**
     * Automatically define a foreign key column based on a model class name.
     *
     * @param string $modelClass The fully qualified class name of the model
     * @param string|null $columnName Optional custom column name
     * @return ForeignKeyDefinition
     */
    public function foreignIdFor(string $modelClass, ?string $columnName = null): ForeignKeyDefinition
    {
        // Extract table and column names (e.g., App\Models\User -> user_id)
        $baseName = strtolower(basename(str_replace('\\', '/', $modelClass)));
        $resolvedColumnName = $columnName ?? $baseName . '_id';
        $referencedTable = $baseName . 's'; // Simple pluralization logic

        $definition = new ForeignKeyDefinition($resolvedColumnName, 'integer');
        $definition->constrained($referencedTable); // Guess the table by default

        $this->columns[] = $definition;
        $this->foreignKeys[] = $definition;

        return $definition;
    }

    /**
     * Execute the accumulated column and constraint definitions against the database.
     *
     * @return void
     */
    public function save(): void
    {
        foreach ($this->columns as $column) {
            $this->phinxTable->addColumn(
                $column->getName(),
                $column->getType(),
                $column->getOptions()
            );
        }

        foreach ($this->foreignKeys as $fk) {
            $this->phinxTable->addForeignKey(
                $fk->getName(),
                $fk->getReferencedTable(),
                'id',
                [
                    'delete' => $fk->getOnDelete(),
                    'update' => $fk->getOnUpdate()
                ]
            );
        }

        if ($this->phinxTable->exists()) {
            $this->phinxTable->update();
        } else {
            $this->phinxTable->create();
        }
    }
}
