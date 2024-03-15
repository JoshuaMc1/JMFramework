<?php

namespace Lib\Database\Contracts;

use Lib\Database\ColumnDefinition;

interface InputInterface
{
    public function string(string $name, int $length = 255): self;

    public function char(string $name, int $length = 255): self;

    public function text(string $name): self;

    public function fullText(): self;

    public function tinyText(string $name): self;

    public function mediumText(string $name): self;

    public function longText(string $name): self;

    public function integer(string $name, int $length = 11): self;

    public function tinyInteger(string $name, int $length = 1): self;

    public function unsignedBigInteger(string $name): self;

    public function comment(string $comment): self;

    public function nullable(): self;

    public function notNullable(): self;

    public function autoIncrement(): self;

    public function primary(): self;

    public function unique(): self;

    public function index(): self;

    public function real(string $name, int $precision = 10, int $scale = 0): self;

    public function spatial(): self;

    public function spatialIndex(): self;

    public function spatialKey(): self;

    public function spatialReferenceSystem(): self;

    public function spatialReferenceSystemId(): self;

    public function blob(): self;

    public function tinyBlob(): self;

    public function dateTime(string $name): self;

    public function date(string $name): self;

    public function time(string $name): self;

    public function decimal(string $name, int $precision = 10, int $scale = 0): self;

    public function double(string $name, int $precision = 10, int $scale = 0): self;

    public function float(string $name, int $precision = 10, int $scale = 0): self;

    public function json(string $name): self;

    public function boolean(string $name): self;

    public function enum(string $name, array $values): self;

    public function set(string $name, array $values): self;

    public function mediumInteger(string $name, int $length = 7): self;

    public function mediumBlob(string $name): self;

    public function longBlob(string $name): self;

    public function timestamps(): self;

    public function timestamp(string $name): self;

    public function default(string $value): self;

    public function id(): self;

    public function foreign(string $name): self;

    public function references(string $tableName, string $columnName = 'id'): self;

    public function onUpdate(string $action = ColumnDefinition::CASCADE): self;

    public function onDelete(string $action = ColumnDefinition::CASCADE): self;

    public function generate(): string;
}
