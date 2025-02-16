<?php

namespace App\Models;

use Medoo\Medoo;
use App\Core\Database;

/**
 * Base model class providing common CRUD operations
 * for models using Medoo as the database layer.
 *
 * @package App\Models
 */
abstract class BaseModel
{
    /**
     * The Medoo database instance.
     *
     * @var Medoo
     */
    protected Medoo $db;

    /**
     * The database table name.
     *
     * @var string
     */
    protected string $table;

    /**
     * Initializes the database instance.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Retrieves all records from the table.
     *
     * @param array $columns The columns to select (defaults to ['*']).
     * @return array An array of all records.
     */
    public function all(?array $columns = null): array
    {
        // Si columnas es null, entonces queremos todas, con '*'
        $columns = $columns ?? '*';

        return $this->db->select($this->table, $columns);
    }

    /**
     * Finds a record by its ID.
     *
     * @param int   $id      The ID of the record.
     * @param array $columns The columns to select (defaults to ['*']).
     * @return array The record as an associative array, or an empty array if not found.
     */
    public function find(int $id, $columns = null): array
    {
        // Si $columns es null, queremos todas las columnas: '*'
        $columns = $columns ?? '*';

        return $this->db->get($this->table, $columns, ['id' => $id]) ?? [];
    }

    /**
     * Creates a new record.
     *
     * @param array $data The data to insert into the table.
     * @return bool True if the insert operation affected at least one row; false otherwise.
     */
    public function create(array $data): bool
    {
        return $this->db->insert($this->table, $data)->rowCount() > 0;
    }

    /**
     * Updates an existing record by its ID.
     *
     * @param int   $id   The ID of the record to update.
     * @param array $data The data to update.
     * @return bool True if the update operation affected at least one row; false otherwise.
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update($this->table, $data, ['id' => $id])->rowCount() > 0;
    }

    /**
     * Deletes a record by its ID.
     *
     * @param int $id The ID of the record to delete.
     * @return bool True if the delete operation affected at least one row; false otherwise.
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, ['id' => $id])->rowCount() > 0;
    }

    /**
     * Performs a custom search based on the given conditions.
     *
     * @param array $conditions The conditions to match in the query.
     * @param array $columns    The columns to select (defaults to ['*']).
     * @return array An array of matching records.
     */
    public function where(array $conditions, $columns = null): array
    {
        $columns = $columns ?? '*';
        return $this->db->select($this->table, $columns, $conditions);
    }
}
