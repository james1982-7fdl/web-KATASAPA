<?php
class Database
{
    public PDO $pdo;

    public function __construct()
    {
        $host = '127.0.0.1';
        $dbname = 'katasapa_db';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function sql(string $sql, array $params = []): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function show(string $table): array
    {
        $this->assertIdentifier($table);
        return $this->sql("SELECT * FROM `$table`");
    }

    public function execute(string $sql, array $params = []): bool
    {
        $statement = $this->pdo->prepare($sql);
        return $statement->execute($params);
    }

    public function insert(string $table, array $data): bool
    {
        $this->assertIdentifier($table);
        if ($data === []) {
            throw new InvalidArgumentException('Insert data cannot be empty.');
        }

        $columns = array_keys($data);
        foreach ($columns as $column) {
            $this->assertIdentifier($column);
        }

        $columnList = '`' . implode('`, `', $columns) . '`';
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        return $this->execute(
            "INSERT INTO `$table` ($columnList) VALUES ($placeholders)",
            array_values($data)
        );
    }

    public function countWhere(string $table, array $where): int
    {
        $this->assertIdentifier($table);
        if ($where === []) {
            throw new InvalidArgumentException('A condition is required.');
        }

        $conditions = [];
        foreach (array_keys($where) as $column) {
            $this->assertIdentifier($column);
            $conditions[] = "`$column` = ?";
        }

        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) FROM `$table` WHERE " . implode(' AND ', $conditions)
        );
        $statement->execute(array_values($where));
        return (int) $statement->fetchColumn();
    }

    public function get(string $table, array $where): ?array
    {
        $this->assertIdentifier($table);
        if ($where === []) {
            throw new InvalidArgumentException('A condition is required.');
        }

        $conditions = [];
        foreach (array_keys($where) as $column) {
            $this->assertIdentifier($column);
            $conditions[] = "`$column` = ?";
        }

        $statement = $this->pdo->prepare(
            "SELECT * FROM `$table` WHERE " . implode(' AND ', $conditions) . ' LIMIT 1'
        );
        $statement->execute(array_values($where));
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    private function assertIdentifier(string $identifier): void
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid database identifier.');
        }
    }
}

function katasapa_db(): PDO
{
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db->pdo;
}