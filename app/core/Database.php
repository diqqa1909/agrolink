<?php
    Trait Database{
        private function connect(){
            $string = "mysql:hostname=".DBHOST.";dbname=".DBNAME;
            $con = new PDO($string, DBUSER, DBPASS);
            // throw exceptions for DB errors to make debugging easier
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        }

        public function query($query, $data=[]){
            try {
                $con = $this->connect();
                $stm = $con->prepare($query);

                $check = $stm->execute($data);
                if($check){
                    // Try to fetch results (for SELECT queries)
                    $result = $stm->fetchAll(PDO::FETCH_OBJ);
                    if (is_array($result) && count($result)) {
                        return $result;
                    }

                    // If execute succeeded but there are no rows (INSERT/UPDATE/DELETE), return true
                    return true;
                }
                return false;
            } catch (PDOException $e) {
                // Log the error to PHP error log for debugging
                error_log('Database error: ' . $e->getMessage());
                return false;
            }
        }

        public function get_row($query, $data=[]){
            $con = $this->connect();
            $stm = $con->prepare($query);

            $check = $stm->execute($data);
            if($check){
                $result = $stm->fetchAll(PDO::FETCH_OBJ);
                if (is_array($result) && count($result)) {
                    return $result[0];
                }
            }
            return false;
        }

        // NEW: execute INSERT/UPDATE/DELETE
        public function write($query, $data=[]){
            $con = $this->connect();
            $stm = $con->prepare($query);
            if ($stm->execute($data)) {
                // return insert id if available, otherwise true
                $id = $con->lastInsertId();
                return $id ? (int)$id : true;
            }
            return false;
        }
    }
    
