<?php

namespace Lib\Database;

use Lib\Database\Contracts\InputInterface;

/**
 * Class ColumnDefinition
 * 
 * Provides a set of methods to create a column definition 
 * for a table in the database.
 */
class ColumnDefinition implements InputInterface
{
    /**
     * Column definition
     * 
     * @var array
     */
    private $column = [];

    /**
     * Cascade actions
     * 
     * @var string CASCADE
     */
    public const CASCADE = 'CASCADE';

    /**
     * Restrict actions
     * 
     * @var string RESTRICT
     */
    public const RESTRICT = 'RESTRICT';

    /**
     * Set null actions
     * 
     * @var string SET NULL
     */
    public const SET_NULL = 'SET NULL';

    /**
     * No action
     * 
     * @var string NO ACTION
     */
    public const NO_ACTION = 'NO ACTION';

    /**
     * Add a string column to the table definition with a default length of 255 characters.
     * 
     * @param string $name
     * @param int $length
     * 
     * @return ColumnDefinition
     */
    public function string(string $name, int $length = 255): self
    {
        $this->column[] = "`$name` VARCHAR($length)";

        return $this;
    }

    /**
     * Add an integer column to the table definition with a default length of 11 characters.
     * 
     * @param string $name
     * @param int $length
     * 
     * @return ColumnDefinition
     */
    public function integer(string $name, int $length = 11): self
    {
        $this->column[] = "`$name` INT($length)";

        return $this;
    }

    /**
     * Add a tiny integer column to the table definition with a default length of 1 character.
     * 
     * @param string $name
     * @param int $length
     * 
     * @return ColumnDefinition
     */
    public function tinyInteger(string $name, int $length = 1): self
    {
        $this->column[] = "`$name` TINYINT($length)";

        return $this;
    }

    /**
     * Add a unsigned integer column to the table definition.
     * 
     * @param string $name
     * @param int $length
     * 
     * @return ColumnDefinition
     */
    public function unsignedBigInteger(string $name): self
    {
        $this->column[] = "`$name` BIGINT UNSIGNED";

        return $this;
    }

    /**
     * Add a text column to the table definition.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function text(string $name): self
    {
        $this->column[] = "`$name` TEXT";
        return $this;
    }

    /**
     * Add a comment to the column.
     * 
     * @param string $comment
     * 
     * @return ColumnDefinition
     */
    public function comment(string $comment): self
    {
        $this->column[count($this->column) - 1] .= " COMMENT '$comment'";
        return $this;
    }

    /**
     * Add a nullable column
     * 
     * @return ColumnDefinition
     */
    public function nullable(): self
    {
        $this->column[count($this->column) - 1] .= " NULL";

        return $this;
    }

    /**
     * Add a not nullable column
     * 
     * @return ColumnDefinition
     */
    public function notNullable(): self
    {
        $this->column[count($this->column) - 1] .= " NOT NULL";

        return $this;
    }

    /**
     * Add an auto incrementing column
     * 
     * @return ColumnDefinition
     */
    public function autoIncrement(): self
    {
        $this->column[count($this->column) - 1] .= " AUTO_INCREMENT";

        return $this;
    }

    /**
     * Add a primary key
     * 
     * @return ColumnDefinition
     */
    public function primary(): self
    {
        $this->column[count($this->column) - 1] .= " PRIMARY KEY";

        return $this;
    }

    /**
     * Add a unique key.
     * 
     * @return ColumnDefinition
     */
    public function unique(): self
    {
        $this->column[count($this->column) - 1] .= " UNIQUE";

        return $this;
    }

    /**
     * Add an index key.
     * 
     * @return ColumnDefinition
     */
    public function index(): self
    {
        $this->column[count($this->column) - 1] .= " INDEX";

        return $this;
    }

    /**
     * Add a full text index key.
     * 
     * @return ColumnDefinition
     */
    public function fullText(): self
    {
        $this->column[count($this->column) - 1] .= " FULLTEXT";

        return $this;
    }

    /**
     * Add a spatial index key.
     * 
     * @return ColumnDefinition
     */
    public function spatial(): self
    {
        $this->column[count($this->column) - 1] .= " SPATIAL";

        return $this;
    }

    /**
     * Add a spatial index key.
     * 
     * @return ColumnDefinition
     */
    public function spatialIndex(): self
    {
        $this->column[count($this->column) - 1] .= " SPATIAL INDEX";

        return $this;
    }

    /**
     * Add a spatial index key.
     * 
     * @return ColumnDefinition
     */
    public function spatialKey(): self
    {
        $this->column[count($this->column) - 1] .= " SPATIAL KEY";

        return $this;
    }

    /**
     * Add a spatial index key.
     * 
     * @return ColumnDefinition
     */
    public function spatialReferenceSystem(): self
    {
        $this->column[count($this->column) - 1] .= " SPATIAL REFERENCE SYSTEM";

        return $this;
    }

    /**
     * Add a spatial index key.
     * 
     * @return ColumnDefinition
     */
    public function spatialReferenceSystemId(): self
    {
        $this->column[count($this->column) - 1] .= " SPATIAL REFERENCE SYSTEM ID";

        return $this;
    }

    /**
     * Add a blob column.
     * 
     * @return ColumnDefinition
     */
    public function blob(): self
    {
        $this->column[count($this->column) - 1] .= " BLOB";

        return $this;
    }

    /**
     * Add a tinyblob column.
     * 
     * @return ColumnDefinition
     */
    public function tinyBlob(): self
    {
        $this->column[count($this->column) - 1] .= " TINYBLOB";

        return $this;
    }

    /**
     * Add a datetime column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function dateTime(string $name): self
    {
        $this->column[] = "`$name` DATETIME";

        return $this;
    }

    /**
     * Add a date column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function date(string $name): self
    {
        $this->column[] = "`$name` DATE";

        return $this;
    }

    /**
     * Add a time column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function time(string $name): self
    {
        $this->column[] = "`$name` TIME";

        return $this;
    }

    /**
     * Add a decimal column with a default precision of 10 and scale of 0.
     * 
     * @param string $name
     * @param int $precision
     * @param int $scale
     * 
     * @return ColumnDefinition
     */
    public function decimal(string $name, int $precision = 10, int $scale = 0): self
    {
        $this->column[] = "`$name` DECIMAL($precision, $scale)";

        return $this;
    }

    /**
     * Add a double column with a default precision of 10 and scale of 0.
     * 
     * @return ColumnDefinition
     */
    public function double(string $name, int $precision = 10, int $scale = 0): self
    {
        $this->column[] = "`$name` DOUBLE($precision, $scale)";

        return $this;
    }

    /**
     * Add a float column with a default precision of 10 and scale of 0.
     * 
     * @param string $name
     * @param int $precision
     * @param int $scale
     * 
     * @return ColumnDefinition
     */
    public function float(string $name, int $precision = 10, int $scale = 0): self
    {
        $this->column[] = "`$name` FLOAT($precision, $scale)";

        return $this;
    }

    /**
     * Add a json column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function json(string $name): self
    {
        $this->column[] = "`$name` JSON";

        return $this;
    }

    /**
     * Add a boolean column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function boolean(string $name): self
    {
        $this->column[] = "`$name` TINYINT(1)";

        return $this;
    }

    /**
     * Add an enum column.
     * 
     * @param string $name
     * @param array $values
     * 
     * @return ColumnDefinition
     */
    public function enum(string $name, array $values): self
    {
        $this->column[] = "`$name` ENUM('" . implode("', '", $values) . "')";

        return $this;
    }

    /**
     * Add a set column.
     * 
     * @param string $name
     * @param array $values
     * 
     * @return ColumnDefinition
     */
    public function set(string $name, array $values): self
    {
        $this->column[] = "`$name` SET('" . implode("', '", $values) . "')";

        return $this;
    }

    /**
     * Add a tinytext column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function tinyText(string $name): self
    {
        $this->column[] = "`$name` TINYTEXT";

        return $this;
    }

    /**
     * Add a mediumtext column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function mediumText(string $name): self
    {
        $this->column[] = "`$name` MEDIUMTEXT";

        return $this;
    }

    /**
     * Add a longtext column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function longText(string $name): self
    {
        $this->column[] = "`$name` LONGTEXT";

        return $this;
    }

    /**
     * Add a mediumint column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function mediumInteger(string $name, int $length = 7): self
    {
        $this->column[] = "`$name` MEDIUMINT($length)";

        return $this;
    }

    /**
     * Add a mediumblob column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function mediumBlob(string $name): self
    {
        $this->column[] = "`$name` MEDIUMBLOB";

        return $this;
    }

    /**
     * Add a longblob column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function longBlob(string $name): self
    {
        $this->column[] = "`$name` LONGBLOB";

        return $this;
    }

    /**
     * Add timestamps columns.
     * Sets created_at and updated_at to current timestamp, and updates them on update.
     * 
     * @return ColumnDefinition
     */
    public function timestamps(): self
    {
        $this->column[] = "`created_at` DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->column[] = "`updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";

        return $this;
    }

    /**
     * Add a timestamp column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function timestamp(string $name): self
    {
        $this->column[] = "`$name` DATETIME";

        return $this;
    }

    public function char(string $name, int $length = 255): self
    {
        $this->column[] = "`$name` CHAR($length)";

        return $this;
    }

    public function real(string $name, int $precision = 10, int $scale = 0): InputInterface
    {
        $this->column[] = "`$name` REAL($precision, $scale)";

        return $this;
    }

    /**
     * Add a default value.
     * 
     * @param string $value
     * 
     * @return ColumnDefinition
     */
    public function default(string $value): self
    {
        $this->column[count($this->column) - 1] .= " DEFAULT $value";

        return $this;
    }

    /**
     * Add a primary key
     * 
     * Sets id to be the primary key and increments it by 1 on each 
     * insert into the table.
     * 
     * @return ColumnDefinition
     */
    public function id(): self
    {
        $this->column[] = "`id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT";

        return $this;
    }

    /**
     * Add a foreign key column.
     * 
     * @param string $name
     * 
     * @return ColumnDefinition
     */
    public function foreign(string $name): self
    {
        $this->column[] = "FOREIGN KEY (`$name`)";
        return $this;
    }

    /**
     * Add a references column to another table in the database.
     * Set default value to id and increments it by 1 on each insert into the table.
     * 
     * @param string $tableName
     * @param string $columnName
     * 
     * @return ColumnDefinition
     */
    public function references(string $tableName, string $columnName = 'id'): self
    {
        $this->column[count($this->column) - 1] .= " REFERENCES `$tableName` (`$columnName`)";
        return $this;
    }

    /**
     * Set on update action. 
     * Defaults to CASCADE.
     * 
     * @param string $action
     * 
     * @return ColumnDefinition
     */
    public function onUpdate(string $action = self::CASCADE): self
    {
        $this->column[count($this->column) - 1] .= " ON UPDATE $action";

        return $this;
    }

    /**
     * Set on delete action.
     * Defaults to CASCADE.
     * 
     * @param string $action
     * 
     * @return ColumnDefinition
     */
    public function onDelete(string $action = self::CASCADE): self
    {
        $this->column[count($this->column) - 1] .= " ON DELETE $action";

        return $this;
    }

    /**
     * Get the column definition.
     * This method should be called after all columns have been added.
     * 
     * @return string
     */
    public function generate(): string
    {
        $string = implode(", ", $this->column);

        $this->column = [];

        return $string;
    }
}
