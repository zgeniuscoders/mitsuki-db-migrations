<?php

namespace Mitsuki\Database\Definitions;

/**
 * Defines a foreign key constraint for a database column.
 * * This class extends ColumnDefinition to provide specific methods for
 * defining relationship constraints such as cascading deletes and updates.
 * * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 */
class ForeignKeyDefinition extends ColumnDefinition
{
    /** @var string Action to take when the referenced row is deleted */
    protected string $onDelete = 'CASCADE';

    /** @var string Action to take when the referenced row is updated */
    protected string $onUpdate = 'CASCADE';

    /** @var string|null The name of the table being referenced */
    protected ?string $referencedTable = null;

    /**
     * Define the table that this foreign key references.
     *
     * @param string|null $table
     * @return $this
     */
    public function constrained(?string $table = null): self
    {
        if ($table !== null) {
            $this->referencedTable = $table;
        }
        return $this;
    }

    /**
     * Set the "ON DELETE" constraint action.
     *
     * @param string $action Commonly CASCADE, SET NULL, RESTRICT, or NO ACTION
     * @return $this
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    /**
     * Set the "ON UPDATE" constraint action.
     *
     * @param string $action Commonly CASCADE, SET NULL, RESTRICT, or NO ACTION
     * @return $this
     */
    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    /**
     * Get the defined ON DELETE action.
     *
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    /**
     * Get the defined ON UPDATE action.
     *
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdate;
    }

    /**
     * Get the name of the referenced table.
     *
     * @return string|null
     */
    public function getReferencedTable(): ?string
    {
        return $this->referencedTable;
    }
}