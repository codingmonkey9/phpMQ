<?php
/**
 * Created by PhpStorm.
 * User: huangshimin
 * Date: 2020/1/18
 * Time: 10:25
 */

namespace driver;


class MysqlDriver implements QueueInterface
{
    private $conn;
    private $config;
    private $table;
    private $select_prefix;
    private $insert_prefix;
    private $delete_prefix;
    private $update_prefix;

    public function __construct($options = [])
    {
        $this->config = $options;
        $this->conn = new \PDO(
            $this->config['dsn'],
            $this->config['user'],
            $this->config['pass']
        );
        $field_string = Job::$field_string;
        $this->table = $this->config['table'];
        $this->select_prefix = "SELECT {$field_string} from {$this->table}";
        $this->insert_prefix = "INSERT INTO {$this->table} ";
        $this->delete_prefix = "DELETE FROM {$this->table}";
        $this->update_prefix = "UPDATE {$this->table} ";
    }

    public function tubes(): array
    {
        // TODO: Implement tubes() method.
        $sql = "SELECT `tube` FROM {$this->table} GROUP BY `tube`";
        $res = $this->conn->query($sql);
        if (!$res) {
            throw new \PDOException('err:'.json_encode($statement->errorInfo()));
        }
        return $res->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete(Job $job): bool
    {
        // TODO: Implement delete() method.
        if (!$job->id) {
            throw new \Exception('job id can\'t empty!', 4000);
        }
        $sql = "{$this->delete_prefix} WHERE id = :id";
        $statement = $this->conn->prepare($sql);
        $res = $statement->execute([':id' => $job->id]);
        return $res;
    }

    public function jobs(string $tube): array
    {
        // TODO: Implement jobs() method.
        $sql = "{$this->select_prefix} WHERE tube = :tube";
        $statement = $this->conn->prepare($sql);
        $res = $statement->execute([':tube' => $tube]);
        if (!$res) {
            throw new \PDOException('err:'.json_encode($statement->errorInfo()));
        }
        return Job::arr2job($statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function put(Job $job): Job
    {
        // TODO: Implement put() method.
        $sql = "{$this->insert_prefix}";
        $field = '';
        $prepare = '';
        $value = [];
        foreach (Job::$field as $v) {
            if ($job->$v) {
                $field .= "{$v},";
                $prepare .= ":{$v},";
                $value[":{$v}"] = $job->$v;
            }
        }
        $field = '('.trim($field, ',').')';
        $prepare = '('.trim($prepare, ',').')';
        $sql = "{$sql} {$field} VALUES {$prepare}";

        $statement = $this->conn->prepare($sql);
        $res = $statement->execute($value);
        if (!$res) {
            throw new \PDOException('err:'.json_encode($statement->errorInfo()));
        }
        $job->id = $this->conn->lastInsertId();
        return $job;
    }

    public function reserve(string $tube): Job
    {
        // TODO: Implement reserve() method.
        $time = time();
        $over_time = $time - $this->config['ttr'];
        $sql = "{$this->select_prefix} WHERE (status = 'ready' OR (status = 'reserved' AND reserved_at <= {$over_time})) 
        AND available_at <= {$time} AND tube = :tube ORDER BY sort limit 1";
        $statement = $this->conn->prepare($sql);
        $statement->bindParam(':tube', $tube);
        $res = $statement->execute();
        if (!$res) {
            throw new \PDOException('err: '.$sql, 5000);
        }
        if ($data = $statement->fetch()) {
            $job = new Job($data);
            $attempts = $job->attempts + 1;
            $sql = "{$this->update_prefix} SET status = 'reserved', attempts = {$attempts}, reserved_at = {$time} WHERE id = {$job->id}";
            $rows = $this->conn->exec($sql);
            if ($rows <= 0) {
                throw new \PDOException('update_err'.$sql.json_encode($statement->errorInfo()));
            }
            return $job;
        }
        return new Job;
    }
}