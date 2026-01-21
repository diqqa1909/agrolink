<?php
trait Database
{
    private function connect()
    {
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new PDO($string, DBUSER, DBPASS);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }

    public function query($query, $data = [])
    {
        try {
            $con = $this->connect();
            $stm = $con->prepare($query);

            $check = $stm->execute($data);
            if ($check) {
                $result = $stm->fetchAll(PDO::FETCH_OBJ);
                if (is_array($result) && count($result)) {
                    return $result;
                }
            }
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
        }
        return false;
    }

    public function get_row($query, $data = [])
    {
        try {
            $con = $this->connect();
            $stm = $con->prepare($query);

            $check = $stm->execute($data);
            if ($check) {
                $result = $stm->fetchAll(PDO::FETCH_OBJ);
                if (is_array($result) && count($result)) {
                    return $result[0];
                }
            }
        } catch (PDOException $e) {
            error_log("Get Row Error: " . $e->getMessage());
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
                return $id ? (int)$id : true;
            }
        } catch (PDOException $e) {
            error_log("Write Error: " . $e->getMessage() . " | Query: " . $query . " | Data: " . json_encode($data));
        }
        return false;
    }
}
