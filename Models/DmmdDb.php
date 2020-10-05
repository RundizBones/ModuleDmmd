<?php
/**
 * Demo management dialog model.
 *
 * @TODO[dmmd]: create your own models. The code below is just for demonstration, please remove on your real project.
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\DemoManagementDialog\Models;


/**
 * Demo management dialog model.
 * 
 * @author mr.v
 */
class DmmdDb extends \Rdb\System\Core\Models\BaseModel
{


    /**
     * @var array Allowed sort columns in db.
     */
    protected $allowedSort = ['id', 'title'];


    /**
     * @var string The table name.
     */
    protected $tableName;


    public function __construct(\Rdb\System\Container $Container)
    {
        parent::__construct($Container);

        $this->tableName = $this->Db->tableName('demo_management_dialog');
    }// __construct


    /**
     * Add an item.
     * 
     * @param array $data The associative array where key is column name and value is its value.
     * @return mixed Return inserted ID if successfully inserted, return `0` (zero), or `false` if failed to insert.
     */
    public function add(array $data)
    {
        $insertResult = $this->Db->insert($this->tableName, $data);

        if ($insertResult === true) {
            $id = $this->Db->PDO()->lastInsertId();

            return $id;
        }
        return false;
    }// add


    /**
     * Delete an item.
     * 
     * @param int $id The ID matched `id` column (PK) in DB.
     * @return bool Return `\PDOStatement::execute()`. Return `true` on success, `false` for otherwise.
     */
    public function delete(int $id): bool
    {
        $where = [];
        $where['id'] = $id;
        $output = $this->Db->delete($this->tableName, $where);
        unset($where);

        return $output;
    }// delete


    /**
     * Get an item.
     * 
     * @param int $id The ID.
     * @return mixed Return `\PDOStatement::fetchObject()`.
     */
    public function get(int $id)
    {
        $sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `id` = :id';
        $Sth = $this->Db->PDO()->prepare($sql);
        unset($sql);
        $Sth->bindValue(':id', $id, \PDO::PARAM_INT);
        $Sth->execute();
        $result = $Sth->fetchObject();
        $Sth->closeCursor();
        unset($Sth);

        return $result;
    }// get


    /**
     * List items.
     * 
     * @param array $options The associative array options. Available options keys:<br>
     *                          `search` (string) the search term,<br>
     *                          `where` (array) the where conditions where key is column name and value is its value,<br>
     *                          `sortOrders` (array) the sort order where `sort` key is column name, `order` key is mysql order (ASC, DESC),<br>
     *                          `unlimited` (bool) set to `true` to show unlimited items, unset or set to `false` to show limited items,<br>
     *                          `limit` (int) limit items per page. maximum is 1000,<br>
     *                          `offset` (int) offset or start at record. 0 is first record,<br>
     * @return array Return associative array with `total` and `items` in keys.
     */
    public function listItems(array $options = []): array
    {
        // prepare options and check if incorrect.
        if (!isset($options['offset']) || !is_numeric($options['offset'])) {
            $options['offset'] = 0;
        }
        if (!isset($options['unlimited']) || (isset($options['unlimited']) && $options['unlimited'] !== true)) {
            if (!isset($options['limit']) || !is_numeric($options['limit'])) {
                $ConfigDb = new ConfigDb($this->Container);
                $options['limit'] = $ConfigDb->get('rdbadmin_AdminItemsPerPage', 20);
                unset($ConfigDb);
            } elseif (isset($options['limit']) && $options['limit'] > 1000) {
                $options['limit'] = 1000;
            }
        }

        // SQL_CALC_FOUND_ROWS is slower than [SELECT COUNT() and then SELECT *] in many cases.
        // see https://stackoverflow.com/questions/186588/which-is-fastest-select-sql-calc-found-rows-from-table-or-select-count for more details.
        $bindValues = [];
        $output = [];
        // sql left join is required for user listing that filter role.
        $sql = 'SELECT %*% FROM `' . $this->tableName . '` AS `table`
            WHERE 1';
        if (array_key_exists('search', $options) && is_scalar($options['search']) && !empty($options['search'])) {
            $sql .= ' AND (';
            $sql .= '`table`.`title` LIKE :search';
            $sql .= ')';
            $bindValues[':search'] = '%' . $options['search'] . '%';
        }

        if (isset($options['where'])) {
            // where conditions.
            $placeholders = [];
            $genWhereValues = $this->Db->buildPlaceholdersAndValues($options['where']);
            if (isset($genWhereValues['values'])) {
                $bindValues = array_merge($bindValues, $genWhereValues['values']);
            }
            if (isset($genWhereValues['placeholders'])) {
                $placeholders = array_merge($placeholders, $genWhereValues['placeholders']);
            }
            unset($genWhereValues);
            $sql .= ' AND ' . implode(' AND ', $placeholders);
            unset($placeholders);
        }

        // prepare and get 'total' records while not set limit and offset.
        $Sth = $this->Db->PDO()->prepare(str_replace('%*%', 'COUNT(`table`.`id`) AS `total`', $sql));
        // bind whereValues
        foreach ($bindValues as $placeholder => $value) {
            $Sth->bindValue($placeholder, $value);
        }// endforeach;
        unset($placeholder, $value);
        $Sth->execute();
        $output['total'] = $Sth->fetchColumn();
        $Sth->closeCursor();
        unset($Sth);

        // sort and order.
        if (array_key_exists('sortOrders', $options) && is_array($options['sortOrders']) && !empty($options['sortOrders'])) {
            $orderby = [];
            foreach ($options['sortOrders'] as $sort) {
                if (
                    is_array($sort) && 
                    array_key_exists('sort', $sort) && 
                    in_array($sort['sort'], $this->allowedSort) && 
                    array_key_exists('order', $sort) && 
                    in_array(strtoupper($sort['order']), $this->allowedOrders)
                ) {
                    $orderby[] = '`table`.`' . $sort['sort'] . '` ' . strtoupper($sort['order']);
                }
            }// endforeach;
            unset($sort);

            if (!empty($orderby)) {
                $sql .= ' ORDER BY ';
                $sql .= implode(', ', $orderby);
            }
            unset($orderby);
        }

        // limited or unlimited.
        if (!isset($options['unlimited']) || (isset($options['unlimited']) && $options['unlimited'] !== true)) {
            // if limited.
            $sql .= ' LIMIT ' . $options['limit'] . ' OFFSET ' . $options['offset'];
        }

        // prepare and get 'items'.
        $Sth = $this->Db->PDO()->prepare(str_replace('%*%', '*', $sql));
        // bind whereValues
        foreach ($bindValues as $placeholder => $value) {
            $Sth->bindValue($placeholder, $value);
        }// endforeach;
        unset($placeholder, $value);
        $Sth->execute();
        $result = $Sth->fetchAll();
        $Sth->closeCursor();
        unset($bindValues, $sql, $Sth);

        $output['items'] = $result;

        unset($result);
        return $output;
    }// listItems


    /**
     * Update an item.
     * 
     * @param array $data The associative array where its key is column name and value is its value to update.
     * @param array $where The associative array where its key is column name and value is its value.
     * @return bool Return `true` on success update, `false` for otherwise.
     */
    public function update(array $data, array $where): bool
    {
        $output = $this->Db->update($this->tableName, $data, $where);

        return $output;
    }// update


}// DmmdDb
