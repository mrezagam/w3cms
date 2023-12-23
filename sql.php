<?php

class sql
{
    private $dbInfo;

    public function __construct($dbInfo)
    {
        $this->dbInfo = $dbInfo;
    }

    private function connect()
    {
        $conn = new mysqli($this->dbInfo['host'], $this->dbInfo['user'], $this->dbInfo['pass'], $this->dbInfo['name']);
        if ($conn->connect_error) {
            die("connection failed: " . $conn->connect_error);
        }
        mysqli_set_charset($conn, "utf8");
        return $conn;
    }

    public function create_database($name)
    {
        $conn = $this->connect();

        $sql = "CREATE DATABASE $name";
        if ($conn->query($sql) === TRUE) {
            $return = TRUE;
        } else {
            $return = FALSE . $conn->error;
        }

        $conn->close();
        return $return;
    }

    public function create_table($name, $column, $idSpan = 6)
    {
        $conn = $this->connect();

        $sql = "CREATE TABLE $name (id INT($idSpan) UNSIGNED AUTO_INCREMENT PRIMARY KEY, " . implode(", ", array_values($column)) . ")";

        if ($conn->query($sql) === TRUE) {
            $return = TRUE;
        } else {
            $return = FALSE;
        }

        $conn->close();
        return $return;
    }

    public function insert($table, $row)
    {
        $conn = $this->connect();

        $columns = implode(", ", array_keys($row));
        $values = "'" . implode("', '", array_values($row)) . "'";
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        if ($conn->query($sql) === TRUE) {
            $return = $conn->insert_id;
        } else {
            $return = FALSE;
        }

        $conn->close();
        return $return;
    }

    public function insert_multiple($table, $data)
    {
        $conn = $this->connect();

        $time = time();
        $columns = implode(", ", array_keys($data[0]));
        $values = [];
        foreach ($data as $row) {
            $rowValues = "'" . implode("', '", array_values($row)) . "'";
            $values[] = "($rowValues)";
        }
        $valuesString = implode(", ", $values);
        $sql = "INSERT INTO $table ($columns) VALUES $valuesString";

        if ($conn->query($sql) === TRUE) {
            $return = TRUE;
        } else {
            $return = FALSE . $conn->error;
        }

        $conn->close();
        return $return;
    }

    public function select($table, $columns = "*", $condition = "")
    {
        $conn = $this->connect();

        $sql = "SELECT $columns FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }

        $result = $conn->query($sql);
        $return = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $return[] = $row;
            }
        }

        $conn->close();
        return $return;
    }

    public function select_dot($table, $column, $key, $value)
    {
        $conn = $this->connect();

        $sql = "SELECT $column FROM $table WHERE $key='$value'";
        $result = $conn->query($sql);

        $return = 'no';
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $return = $row[$column];
        }

        $conn->close();
        return $return;
    }

    public function delete($table, $condition)
    {
        $conn = $this->connect();


        $sql = "DELETE FROM $table WHERE $condition";

        if ($conn->query($sql) === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }

        $conn->close();
    }

    public function update($table, $data, $condition)
    {
        $conn = $this->connect();

        $updates = [];
        foreach ($data as $column => $value) {
            $updates[] = "$column = '$value'";
        }
        $updatesString = implode(", ", $updates);

        $sql = "UPDATE $table SET $updatesString WHERE $condition";

        if ($conn->query($sql) === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }

        $conn->close();
    }
}

?>