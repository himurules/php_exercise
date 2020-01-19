<?php
/**
 * Created by PhpStorm.
 * User: himanshukotnala
 * Date: 2020-01-18
 * Time: 16:38
 */

namespace TDD;


class DBConnector
{
    protected $_db_config = array(
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'database'  => 'exercise_test'
    );

    protected $_conn = null;
    protected $_query;
    public $query_count = 0;

    public function __construct()
    {
        $this->_conn = mysqli_connect($this->_db_config['host'], $this->_db_config['user'], $this->_db_config['password'], $this->_db_config['database']);
        if ($this->_conn->connect_error) {
            die('Failed to connect to MySQL - ' . $this->_conn->connect_error);
        }
    }

    public function query($query) {
        if ($this->_query = $this->_conn->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_slice($x, 1);
                $types = '';
                $args_ref = array();
                foreach ($args as $k => &$arg) {
                    if (is_array($args[$k])) {
                        foreach ($args[$k] as $j => &$a) {
                            $types .= $this->_gettype($args[$k][$j]);
                            $args_ref[] = &$a;
                        }
                    } else {
                        $types .= $this->_gettype($args[$k]);
                        $args_ref[] = &$arg;
                    }
                }
                array_unshift($args_ref, $types);
                call_user_func_array(array($this->_query, 'bind_param'), $args_ref);
            }
            $this->_query->execute();
            if ($this->_query->errno) {
                die('Unable to process MySQL query (check your params) - ' . $this->_query->error);
            }
            $this->_query_count++;
        } else {
            die('Unable to prepare statement (check your syntax) - ' . $this->_conn->error);
        }
        return $this;
    }

    public function fetchAll() {
        $params = array();
        $meta = $this->_query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->_query, 'bind_result'), $params);
        $result = array();
        while ($this->_query->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            $result[] = $r;
        }
        $this->_query->close();
        return $result;
    }

    public function fetchArray() {
        $params = array();
        $meta = $this->_query->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($this->_query, 'bind_result'), $params);
        $result = array();
        while ($this->_query->fetch()) {
            foreach ($row as $key => $val) {
                $result[$key] = $val;
            }
        }
        $this->_query->close();
        return $result;
    }

    public function numRows() {
        $this->_query->store_result();
        return $this->_query->num_rows;
    }

    public function close() {
        return $this->_conn->close();
    }

    public function affectedRows() {
        return $this->_query->affected_rows;
    }

    private function _gettype($var) {
        if(is_string($var)) return 's';
        if(is_float($var)) return 'd';
        if(is_int($var)) return 'i';
        return 'b';
    }


    function __destruct()
    {
            mysqli_close($this->_conn);
    }
}