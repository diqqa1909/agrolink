<?php
trait Database
{
    private $lastError = null;

    private function connect()
    {
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER, DBPASS);
        return $con;
    }

    public function query($query, $data = [])
    {
        $con = $this->connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result;
            }
        }
        return false;
    }

    public function get_row($query, $data = [])
    {
        $con = $this->connect();
        $stm = $con->prepare($query);

        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result[0];
            }
        }
        return false;
    }

    // NEW: execute INSERT/UPDATE/DELETE
    public function write($query, $data = [])
    {
        try {
            $con = $this->connect();
            $stm = $con->prepare($query);

            if ($stm->execute($data)) {
                // return insert id if available, otherwise true
                $id = $con->lastInsertId();
                $this->lastError = null;
                return $id ? (int)$id : true;
            } else {
                $this->lastError = $stm->errorInfo();
                error_log("Database::write - Execute failed: " . print_r($this->lastError, true));
                return false;
            }
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("Database::write - Exception: " . $e->getMessage());
            return false;
        }
    }

    public function getLastError()
    {
        return $this->lastError;
    }
}
