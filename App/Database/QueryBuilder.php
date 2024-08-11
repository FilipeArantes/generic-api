<?php

namespace App\Database;

use App\Core\Responses\Exceptions\AppError;
use PDO;

class QueryBuilder
{
    protected string $table;
    protected string $select = '*';
    protected array $wheres = [];
    protected array $joins = [];

    public static function table(string $table): self
    {
        $instance = new self();
        $instance->table = $table;

        return $instance;
    }

    public function select(string $columns): self
    {
        $this->select = $columns;

        return $this;
    }

    public function where(string $where): self
    {
        $this->wheres[] = $where;

        return $this;
    }

    public function join(string $table, string $on): self
    {
        $this->joins[] = "JOIN {$table} ON {$on}";

        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' '.implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE '.implode(' AND ', $this->wheres);
        }

        $stmt = Connection::connect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function first()
    {
        $result = $this->get();

        return $result ? $result[0] : null;
    }

    public function insert(array $data): int
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = Connection::connect()->prepare($sql);
        $stmt->execute(array_values($data));

        return Connection::connect()->lastInsertId();
    }

    public function update(array $data): bool
    {
        $setClause = implode(',', array_map(fn ($col) => "{$col} = ?", array_keys($data)));

        $sql = "UPDATE {$this->table} SET {$setClause}";

        if (empty($this->wheres) && empty($data['id'])) {
            throw new AppError('Chave primaria da tabela não encontrada para alterar registro');
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE '.implode(' AND ', array_map(function ($where) {
                return "{$where[0]} = ?";
            }, $this->wheres));
        }

        if (isset($data['id']) && empty($this->wheres)) {
            $sql .= " WHERE id = {$data['id']}";
        }

        $stmt = Connection::connect()->prepare($sql);
        $stmt->execute(array_merge(array_values($data), array_column($this->wheres, 1)));

        return $stmt->rowCount() > 0;
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE '.implode(' AND ', array_map(function ($where) {
                return "{$where[0]} = ?";
            }, $this->wheres));
        }

        $stmt = Connection::connect()->prepare($sql);
        $stmt->execute(array_column($this->wheres, 1));

        return $stmt->rowCount() > 0;
    }
}
