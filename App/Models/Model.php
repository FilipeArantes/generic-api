<?php

namespace App\Models;

use App\Database\QueryBuilder;

abstract class Model extends QueryBuilder
{
    protected string $table = null;
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function save(): int
    {
        $instance = new static();

        return $instance->table($instance->getTable())->insert($this->attributes);
    }

    public function update(array $data): bool
    {
        $instance = new static();

        return $instance->table($instance->getTable())->where("id = {$this->attributes['id']}")->update($data);
    }

    public function delete(): bool
    {
        $instance = new static();

        return $instance->table($instance->getTable())->where("id = {$this->attributes['id']}")->delete();
    }

    public static function all()
    {
        $instance = new static();
        $query = $instance->table($instance->getTable())->select('*');

        return $query->get();
    }

    public static function find(int $id)
    {
        $instance = new static();
        $query = $instance->table($instance->getTable())
            ->where("id = {$id}")
            ->select('*')
        ;

        $result = $query->first();

        if ($result) {
            $instance->attributes = $result;
        }

        return $instance;
    }

    protected function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }

        return strtolower((new \ReflectionClass($this))->getShortName()).'s';
    }
}
