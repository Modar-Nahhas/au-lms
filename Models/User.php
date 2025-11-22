<?php

namespace LMS_Website\Models;

use LMS_Website\Containers\DB;
use LMS_Website\Enums\UserTypeEnum;

class User extends BaseModel
{

    public static string $table = 'user';

    public ?int $id = null;
    public string $memberType;
    public string $firstName;
    public string $lastName;
    public string $email;
    // You likely don't want to expose the raw password; keep as nullable
    public ?string $password = null;

    public function fullName(): string
    {
        return "$this->firstName $this->lastName";
    }


    /**
     * Create a new user
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param string|null $memberType
     * @return User|BaseModel
     * @throws \Exception
     */
    public static function create(string $firstName, string $lastName, string $email, string $password, ?string $memberType = null): User
    {
        self::prepareDatabase();
        $memberType = $memberType ?? UserTypeEnum::Member->value;
        $table = self::$table;
        $sql = "INSERT INTO $table (member_type, first_name, last_name, email, password)
                VALUES (?, ?, ?, ?, MD5(?))";
        /**
         * @var $stmt \PDOStatement
         */
        $stmt = self::$pdo->prepare($sql);
        try {

            $success = $stmt->execute([$memberType, $firstName, $lastName, $email, $password]);
            if ($success) {
                return self::fromArray([
                    'id' => (int)self::$pdo->lastInsertId(),
                    'member_type' => $memberType,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ]);
            }
            throw new \Exception("Error creating user");

        } catch (\PDOException $e) {
            throw new \Exception("Error creating user: " . $e->getMessage());
        }
    }

    /**
     * Find a user by primary key.
     *
     * @param int $id
     * @return User|null
     */
    public static function find(int $id): ?User
    {
        self::prepareDatabase();
        $table = static::$table;
        $sql = "SELECT * FROM $table WHERE id = ? LIMIT 1";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return static::fromArray($row);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public static function findByEmail(string $email): ?User
    {
        self::prepareDatabase();
        $table = static::$table;
        $sql = "SELECT * FROM $table WHERE email = ? LIMIT 1";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute([$email]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return static::fromArray($row);
    }

    /**
     * Get all users with pagination.
     *
     * @param int $limit
     * @param int $page
     * @return array{
     *     data: User[],
     *     total: int,
     *     page: int,
     *     limit: int
     * }
     */
    public static function all(int $limit = 5, int $page = 1): array
    {
        self::prepareDatabase();
        $table = static::$table;

        // Normalise pagination values
        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;

        // 1) Count total users
        $countSql = "SELECT COUNT(*) FROM $table";
        $countStmt = static::$pdo->prepare($countSql);
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // 2) Fetch paginated data
        $sql = "SELECT * FROM $table LIMIT :limit OFFSET :offset";

        $stmt = static::$pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $users[] = static::fromArray($row);
        }

        return [
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }


    /**
     * Persist changes for this user (simple example).
     *
     * @return bool
     * @throws \Exception
     */
    public function save(): bool
    {
        self::prepareDatabase();
        if ($this->id === null) {
            throw new \Exception("Cannot save user without id");
        }

        $table = static::$table;

        $sql = "UPDATE $table
                SET member_type = ?, first_name = ?, last_name = ?, email = ?
                WHERE id = ?";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        return $stmt->execute([
            $this->memberType,
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->id,
        ]);
    }

    /**
     * Delete this user.
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(): bool
    {
        self::prepareDatabase();
        if ($this->id === null) {
            throw new \Exception("Cannot delete user without id");
        }

        return self::deleteById($this->id);
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public static function deleteById(int|string $id): bool
    {
        self::prepareDatabase();
        $table = static::$table;
        $sql = "DELETE FROM $table WHERE id = ?";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        return $stmt->execute([$id]);
    }

    /**
     * @param string $password
     * @param string $email
     * @return User|null
     */
    public static function checkUserCredentials(string $password, string $email): ?User
    {
        static::prepareDatabase();
        // Hash password same way as stored in DB
        $hashed = md5($password);

        // Prepare SQL
        $sql = "SELECT * FROM user WHERE email = ? AND password = ? LIMIT 1";


        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute([$email, $hashed]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null; // Login failed
        }

        // Convert DB row to User object
        return User::fromArray($row);
    }
}
