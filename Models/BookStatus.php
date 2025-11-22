<?php

namespace LMS_Website\Models;

use LMS_Website\Containers\DB;
use LMS_Website\Enums\BookStatusEnum;

class BookStatus extends BaseModel
{

    public static string $table = 'book_status';

    public ?int $id = null;
    public int $bookId;
    public ?int $memberId = null;
    public string $status;
    public ?string $appliedDate = null;

    /**
     * Create a new book status record.
     *
     * @param int $bookId
     * @param string $status
     * @param int|null $memberId
     * @return BookStatus|BaseModel
     * @throws \Exception
     */
    public static function create(int $bookId, string $status = BookStatusEnum::Available->value, ?int $memberId = null): BookStatus
    {
        self::prepareDatabase();
        $table = self::$table;
        $sql = "INSERT INTO $table (book_id, member_id, status)
                  VALUES (?, ?, ?)";

        /** @var \PDOStatement $stmt */
        $stmt = self::$pdo->prepare($sql);

        try {
            $success = $stmt->execute([
                $bookId,
                $memberId,
                $status,
            ]);

            if ($success) {
                return self::fromArray([
                    'id' => (int)self::$pdo->lastInsertId(),
                    'book_id' => $bookId,
                    'member_id' => $memberId,
                    'status' => $status,
                    // Let DB set applied_date; we can fetch it later if needed
                    'applied_date' => date('Y-m-d H:i:s'),
                ]);
            }

            throw new \Exception("Error creating book status");
        } catch (\PDOException $e) {
            throw new \Exception("Error creating book status: " . $e->getMessage());
        }
    }

    /**
     * Find a book_status record by primary key.
     *
     * @param int $id
     * @return BookStatus|null
     */
    public static function find(int $id): ?BookStatus
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
     * Find status history for a given book.
     *
     * @param int  $bookId
     * @param bool $latestOnly  If true, return only the most recent record.
     * @return BookStatus[]|BookStatus|null
     */
    public static function findByBookId(int $bookId, bool $latestOnly = false)
    {
        self::prepareDatabase();
        $table = static::$table;

        // base SQL
        $sql = "SELECT *
            FROM $table
            WHERE book_id = ?
            ORDER BY applied_date DESC, id DESC";

        // if only the latest record is needed, limit the query
        if ($latestOnly) {
            $sql .= " LIMIT 1";
        }

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute([$bookId]);

        if ($latestOnly) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? static::fromArray($row) : null;
        }

        // return all records
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $statuses = [];

        foreach ($rows as $row) {
            $statuses[] = static::fromArray($row);
        }

        return $statuses;
    }


    /**
     * Get all status records (optionally filtered by book).
     *
     * @param int|null $bookId
     * @return BookStatus[]
     */
    public static function all(?int $bookId = null): array
    {
        self::prepareDatabase();
        $table = static::$table;

        if ($bookId === null) {
            $sql = "SELECT * FROM $table";
            $stmt = static::$pdo->query($sql);
        } else {
            $sql = "SELECT * FROM $table WHERE book_id = ? ORDER BY applied_date DESC, id DESC";
            $stmt = static::$pdo->prepare($sql);
            $stmt->execute([$bookId]);
        }

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $result = [];

        foreach ($rows as $row) {
            $result[] = static::fromArray($row);
        }

        return $result;
    }

    /**
     * Persist changes for this status record (if you ever need to edit status).
     *
     * @return bool
     * @throws \Exception
     */
    public function save(): bool
    {
        self::prepareDatabase();
        if ($this->id === null) {
            throw new \Exception("Cannot save book status without id");
        }

        $table = static::$table;

        $sql = "UPDATE $table
                SET book_id = ?, member_id = ?, status = ?
                WHERE id = ?";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        return $stmt->execute([
            $this->bookId,
            $this->memberId,
            $this->status,
            $this->id,
        ]);
    }

    /**
     * Delete this status record.
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(): bool
    {
        self::prepareDatabase();
        if ($this->id === null) {
            throw new \Exception("Cannot delete book status without id");
        }

        return $this->deleteById($this->id);
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public function deleteById(int|string $id): bool
    {
        self::prepareDatabase();
        $table = static::$table;
        $sql = "DELETE FROM $table WHERE id = ?";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        return $stmt->execute([$id]);
    }
}
