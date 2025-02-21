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
        $values = array_values($data);
        $types = [];
        $placeholders = [];
    
        foreach ($values as $value) {
            if (is_null($value)) {
                $types[] = \PDO::PARAM_NULL;
                $placeholders[] = '?';
            } elseif (is_bool($value)) {
                $types[] = \PDO::PARAM_BOOL;
                $placeholders[] = '?';
            } elseif (is_int($value)) {
                $types[] = \PDO::PARAM_INT;
                $placeholders[] = '?';
            } elseif (is_float($value)) {
                // PDO doesn't have a specific float type, handled as string
                $types[] = \PDO::PARAM_STR;
                $placeholders[] = '?';
            } elseif ($value instanceof \DateTime) {
                $types[] = \PDO::PARAM_STR;
                $values[key($values)] = $value->format('Y-m-d H:i:s');
                $placeholders[] = '?';
            } elseif (is_array($value) || is_object($value)) {
                $types[] = \PDO::PARAM_STR;
                $values[key($values)] = json_encode($value);
                $placeholders[] = '?';
            } else {
                $types[] = \PDO::PARAM_STR;
                $placeholders[] = '?';
            }
        }
    
        $sql = "INSERT INTO {$this->table} ($columns) VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
    
        foreach ($values as $index => $value) {
            $stmt->bindValue($index + 1, $value, $types[$index]);
        }
    
        $stmt->execute();
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

    public function startTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }
}
