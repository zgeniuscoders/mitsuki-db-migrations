<?php

namespace Mitsuki\Database\Definitions;

/**
 * Represents a database column definition with a fluent interface.
 * * This class allows for building column attributes like nullability, 
 * default values, and precision before they are committed to the database.
 * * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 */
class ColumnDefinition
{
    /** @var array Configuration options for the column (Phinx compatible) */
    protected array $options = [];

    /** @var string The name of the column */
    protected string $name;

    /** @var string The data type of the column */
    protected string $type;

    /**
     * Create a new column definition instance.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Set the default value for the column.
     *
     * @param mixed $value
     * @return $this
     */
    public function default($value): self
    {
        $this->options['default'] = $value;
        return $this;
    }

    /**
     * Indicate that the column allows NULL values.
     *
     * @param bool $value
     * @return $this
     */
    public function nullable(bool $value = true): self
    {
        $this->options['null'] = $value;
        return $this;
    }

    /**
     * Add a comment to the column.
     *
     * @param string $text
     * @return $this
     */
    public function comment(string $text): self
    {
        $this->options['comment'] = $text;
        return $this;
    }

    /**
     * Set the total number of digits for numeric types.
     *
     * @param int $value
     * @return $this
     */
    public function precision(int $value): self
    {
        $this->options['precision'] = $value;
        return $this;
    }

    /**
     * Set the number of digits to the right of the decimal point.
     *
     * @param int $value
     * @return $this
     */
    public function scale(int $value): self
    {
        $this->options['scale'] = $value;
        return $this;
    }

    /**
     * Set the maximum length/limit for the column (e.g., for strings or integers).
     *
     * @param int $length
     * @return $this
     */
    public function limit(int $length): self
    {
        $this->options["limit"] = $length;
        return $this; // Fixed: Added return to support chaining
    }

    /**
     * Get the column name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the column data type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the gathered Phinx-compatible options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}