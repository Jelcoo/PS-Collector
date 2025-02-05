<?php

namespace App\Helpers;

enum QueryType
{
    case QUERY_TYPE_ALL;
    case QUERY_TYPE_COUNT;
}

class QueryBuilder
{
    private \PDO $pdo;
    private string $table;
    private array $columns = [];
    private array $conditions = [];
    private array $bindings = [];
    private string $orderBy = '';
    private string $limit = '';
    private QueryType $queryType = QueryType::QUERY_TYPE_ALL;
    private array $updates = [];

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $placeholder = ':' . str_replace('.', '_', $column) . count($this->conditions);
        if (!empty($this->conditions)) {
            $this->conditions[] = "AND $column $operator $placeholder";
        } else {
            $this->conditions[] = "$column $operator $placeholder";
        }
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self
    {
        $placeholder = ':' . str_replace('.', '_', $column) . count($this->bindings);
        if (!empty($this->conditions)) {
            $this->conditions[] = "OR $column $operator $placeholder";
        } else {
            $this->conditions[] = "$column $operator $placeholder";
        }
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "ORDER BY $column $direction";

        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = "LIMIT $offset, $limit";

        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        $this->queryType = QueryType::QUERY_TYPE_COUNT;
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();

        return empty($result) ? null : $result[0];
    }

    public function insert(array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $values = array_values($data);
        $stmt->execute($values);

        return $this->pdo->lastInsertId();
    }

    public function update(array $data): int
    {
        $this->updates = $data;
        $sql = $this->buildUpdateQuery();
        $stmt = $this->pdo->prepare($sql);

        foreach ($this->updates as $column => $value) {
            $stmt->bindValue(":update_$column", $value);
        }

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    private function buildSelectQuery(): string
    {
        if ($this->queryType === QueryType::QUERY_TYPE_COUNT) {
            $columns = 'COUNT(*) AS count';
        } else {
            $columns = empty($this->columns) ? '*' : implode(', ', $this->columns);
        }
        $sql = "SELECT $columns FROM `{$this->table}`";

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . ltrim(implode(' ', $this->conditions));
        }

        if ($this->orderBy) {
            $sql .= ' ' . $this->orderBy;
        }

        if ($this->limit) {
            $sql .= ' ' . $this->limit;
        }

        return $sql;
    }

    private function buildUpdateQuery(): string
    {
        $updateFields = [];
        foreach ($this->updates as $column => $value) {
            $updateFields[] = "$column = :update_$column";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields);

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . ltrim(implode(' ', $this->conditions));
        }

        return $sql;
    }

    public function delete(): int
    {
        $sql = $this->buildDeleteQuery();
        $stmt = $this->pdo->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    private function buildDeleteQuery(): string
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . ltrim(implode(' ', $this->conditions));
        }

        return $sql;
    }
}
