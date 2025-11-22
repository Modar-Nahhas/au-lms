<?php

namespace LMS_Website\Models;

use LMS_Website\Containers\Authorize;

class Book extends BaseModel
{

    public static string $table = 'book';

    public ?int $id = null;
    public string $isbn;
    public string $title;
    public ?string $description;
    public string $author;
    public string $publisher;
    public string $language;
    public string $category;
    public ?string $coverImage = null;
    public ?string $deletedAt = null;

    public BookStatus $bookStatus;


    /**
     * Create a new book.
     *
     * @param string $isbn
     * @param string $title
     * @param string $author
     * @param string $publisher
     * @param string $language
     * @param string $category
     * @param string|null $coverImage
     * @return Book|BaseModel
     * @throws \Exception
     */
    public static function create(
        string  $isbn,
        string  $title,
        string  $author,
        string  $publisher,
        string  $language,
        string  $category,
        ?string $description = null,
        ?string $coverImage = null
    ): Book
    {
        if (!Authorize::isAdmin()) {
            throw new \Exception("You are not authorized to create a book.");
        }
        self::prepareDatabase();
        $table = self::$table;
        $sql = "INSERT INTO $table (isbn, title, author, publisher, language, category, description, cover_image)
                  VALUES (?, ?, ?, ?, ?, ?,?, ?)";

        /** @var \PDOStatement $stmt */
        $stmt = self::$pdo->prepare($sql);

        try {
            $success = $stmt->execute([
                $isbn,
                $title,
                $author,
                $publisher,
                $language,
                $category,
                $description ?? '',
                $coverImage ?? 'default_book.png'
            ]);

            if ($success) {
                $lastInsertId = (int)self::$pdo->lastInsertId();
                BookStatus::create($lastInsertId);
                return self::fromArray([
                    'id' => $lastInsertId,
                    'isbn' => $isbn,
                    'title' => $title,
                    'author' => $author,
                    'publisher' => $publisher,
                    'language' => $language,
                    'category' => $category,
                    'description' => $description,
                    'cover_image' => $coverImage ?? 'default_book.png',
                ]);
            }

            throw new \Exception("Error creating book");
        } catch (\PDOException $e) {
            throw new \Exception("Error creating book: " . $e->getMessage());
        }
    }

    /**
     * Find a book by primary key.
     *
     * @param int $id
     * @param bool $withTrashed
     * @return Book|BaseModel|null
     */
    public static function find(int $id, bool $withTrashed = false): ?Book
    {
        if (!Authorize::isAdmin()) {
            $withTrashed = false;
        }
        self::prepareDatabase();
        $table = static::$table;
        //Get book details and book latest status in one query to avoid N+1 problem
        $sql = "SELECT book.*, JSON_OBJECT(
                             'id', bs.id,
                             'status', bs.status,
                             'applied_date', bs.applied_date,
                             'member_id', bs.member_id,
                             'book_id', bs.book_id
                ) as book_status FROM $table INNER JOIN book_status as bs 
                    on bs.book_id = book.id 
                                   AND
                       bs.id = (
                        SELECT MAX(id)
                        FROM book_status b
                        WHERE b.book_id = book.id
                    )
                  WHERE book.id = ?";
        if (!$withTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        $stmt->execute([$id]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }
        $row['book_status'] = json_decode($row['book_status'], true);

        return static::fromArray($row);
    }

    /**
     * Get paginated list of books with optional filters and latest status.
     *
     * @param array $filters [
     *     'title'    => string|null,
     *     'author'   => string|null,
     *     'language' => string|null,
     *     'category' => string|null,
     * ]
     * @param int $limit
     * @param int $page
     * @param bool $withTrashed Include soft-deleted records
     * @param bool $andFilter Apply filters with AND logic (default) or OR
     * @param bool $exactMatch Apply exact match for language and category string filters
     *
     * @return array{
     *     data: Book[],
     *     total: int,
     *     page: int,
     *     limit: int
     * }
     */
    public static function all(
        array $filters = [],
        int   $limit = 9,
        int   $page = 1,
        bool  $withTrashed = false,
        bool  $andFilter = true,
        bool  $exactMatch = true,
    ): array
    {
        if (!Authorize::isAdmin()) {
            $withTrashed = false;
        }
        self::prepareDatabase();
        $table = static::$table;

        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;

        $whereParts = [];
        $params = [];


        // Dynamic filters (same logic as your old filter() method)
        if (!empty($filters['isbn'])) {
            $whereParts[] = "isbn LIKE :isbn";
            $params[':isbn'] = '%' . $filters['isbn'] . '%';
        }

        if (!empty($filters['title'])) {
            $whereParts[] = "title LIKE :title";
            $params[':title'] = '%' . $filters['title'] . '%';
        }

        if (!empty($filters['author'])) {
            $whereParts[] = "author LIKE :author";
            $params[':author'] = '%' . $filters['author'] . '%';
        }

        if (!empty($filters['language'])) {
            $whereParts[] = "language " . ($exactMatch ? '=' : 'LIKE') . " :language";
            $params[':language'] = $exactMatch ? $filters['language'] : '%' . $filters['language'] . '%';
        }

        if (!empty($filters['category']) && $filters['category'] !== 'Category') {
            $whereParts[] = "category " . ($exactMatch ? '=' : 'LIKE') . " :category";
            $params[':category'] = $exactMatch ? $filters['category'] : '%' . $filters['category'] . '%';
        }

        // Build WHERE SQL
        $whereSql = '';
        $filtersExists = !empty($whereParts);
        if ($filtersExists) {
            $whereSql = ' WHERE ' . implode($andFilter ? ' AND ' : ' OR ', $whereParts);
        }

        // Soft delete handling
        if (!$withTrashed) {
            $whereSql .= ($filtersExists ? ' AND ' : ' WHERE ') . " deleted_at IS NULL";
        }

        // 1) Total count (no join needed)
        $countSql = "SELECT COUNT(*) FROM $table" . $whereSql;
        $countStmt = static::$pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // 2) Data query with latest status joined + JSON_OBJECT
        $sql = "
        SELECT 
            book.*,
            JSON_OBJECT(
                'id',          bs.id,
                'status',      bs.status,
                'applied_date',bs.applied_date,
                'member_id',   bs.member_id,
                'book_id',     bs.book_id
            ) AS book_status
        FROM $table AS book
        LEFT JOIN book_status AS bs
            ON bs.book_id = book.id
           AND bs.id = (
                SELECT MAX(b2.id)
                FROM book_status AS b2
                WHERE b2.book_id = book.id
           )
        $whereSql
        LIMIT :limit OFFSET :offset
    ";

        $stmt = static::$pdo->prepare($sql);

        // Bind filter params
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind pagination params
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $books = [];
        foreach ($rows as $row) {
            // Parse JSON to PHP array (or null if no status)
            $row['book_status'] = $row['book_status']
                ? json_decode($row['book_status'], true)
                : null;

            $books[] = static::fromArray($row);
        }

        return [
            'data' => $books,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }


    /**
     * Persist changes for this book.
     *
     * @return bool
     * @throws \Exception
     */
    public function save(): bool
    {
        if (!Authorize::isAdmin()) {
            throw new \Exception("You are not authorized to edit a book.");
        }
        self::prepareDatabase();
        if ($this->id === null) {
            throw new \Exception("Cannot save book without id");
        }

        $table = static::$table;

        $sql = "UPDATE $table
                SET isbn = ?, title = ?, author = ?, publisher = ?, language = ?, category = ?, description = ?, cover_image = ?
                
                WHERE id = ? AND deleted_at IS NULL";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        return $stmt->execute([
            $this->isbn,
            $this->title,
            $this->author,
            $this->publisher,
            $this->language,
            $this->category,
            $this->description,
            $this->coverImage,
            $this->id,
        ]);
    }

    /**
     * Delete this book.
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(): bool
    {
        if (!Authorize::isAdmin()) {
            throw new \Exception("You are not authorized to delete a book.");
        }
        self::prepareDatabase();
        if ($this->id === null) {
            throw new \Exception("Cannot delete book without id");
        }

        return $this->deleteById($this->id);
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public static function deleteById(int|string $id): bool
    {
        if (!Authorize::isAdmin()) {
            throw new \Exception("You are not authorized to delete a book.");
        }
        self::prepareDatabase();
        $table = static::$table;
        $sql = "UPDATE $table
                SET deleted_at = NOW()
                WHERE id = ? AND deleted_at IS NULL";

        /** @var \PDOStatement $stmt */
        $stmt = static::$pdo->prepare($sql);

        return $stmt->execute([$id]);
    }


}
