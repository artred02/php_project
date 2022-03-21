<?php

class Database {

    private $db;

    private $args_count = 0;
    private $sql = null;
    private $request = null;
    private $args = [];

    private $show_error = true;

    public function __construct($host, $dbname, $username, $password) {
        $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $username, $password);
    }

    // prepare and execute an sql request.
    // only use this if you don't have to bind values
    public function prepareAndExecute($sql, $arguments = []) {
        $this->prepare($sql);

        return $this->execute($arguments);
    }

    // prepare an sql request
    public function prepare($sql) {
        // keeping the sql request for later
        $this->sql = $sql;

        // counting the amount of arguments this sql request needs
        $this->args_count = $this->get_args_count($this->sql);

        // preparing the request
        $this->request = $this->db->prepare($sql);

        // throwing an error if it cannot be prepared
        if (!$this->request) {
            $this->sql_error("Cannot prepare SQL request.");
        }
    }

    // binds a $value to a $parameter with the $data_type type.
    // i recommend using ":param" as a parameter.
    public function bindValue($parameter, $value, $data_type) {
        if ($this->sql == null) {
            $this->sql_error("Cannot bind value because no request was prepared.");
        }

        if ($this->args_count == 0) {
            $this->sql_error(
                "Cannot bind value '" .
                $value . "' to parameter '" .
                $parameter ."' with type '" .
                $data_type . "' as there are no more arguments to bind."
            );
        }

        $this->push_arg($value);

        // binding the value and decrementing the remaining argument count
        $this->request->bindValue($parameter, $value, $data_type);
        $this->args_count--;
    }

    // execute an sql request
    public function execute($arguments = []) {
        if ($this->sql == null) {
            $this->sql_error("Cannot execute request because no request was prepared.");
        }

        if (sizeof($arguments) != $this->args_count) {
            $this->sql_error("Expected " . $this->args_count . " argument" . ($this->args_count == 1 ? "" : "s") . ", got " . sizeof($arguments));
        }

        if (sizeof($arguments) == 0) {
            if (!$this->request->execute()) {
                $this->sql_error("Couldn't execute the SQL request");
            }
        } else {
            if (!$this->request->execute($arguments)) {
                $this->sql_error("Couldn't execute the SQL request");
            }
        }

        foreach ($arguments as $argument) {
            $this->push_arg($argument);
        }

        $request = $this->request;

        $this->clear();
        return $request;
    }

    private function push_arg($argument) {
        $this->args[sizeof($this->args)] = $argument;
    }

    private function clear() {
        $this->args_count = 0;
        $this->sql = null;
        $this->request = null;
        $this->args = [];
    }

    private function sql_error($message) {
        if (!$this->show_error) {
            echo "An error occured... :(";
            die();
        }

        echo '
            <h2 style="color: red">Error: ' . $message . '</h2>
            
            <table>
                <tr>
                    <td><b style="margin-right: 20px">SQL Request:</b></td>
                    <td>' . $this->sql . '</td>
                </tr>
                <tr>
                    <td><b style="margin-right: 20px">SQL Request w/ args:</b></td>
                    <td>' . $this->build_request() . '</td>
                </tr>
            </table><br>';

        $args_count = count($this->args);
        if ($args_count != 0) {
            echo '<b>Argument' . ($args_count == 1 ? "" : "s") . ':</b>
                <ul style="margin: 5px">';

            foreach ($this->args as $argument) {
                echo '<li>' . $argument . '</li>';
            }

            echo '</ul><br>';
        }

        echo "<b>Request's var_dump:</b>";

        var_dump($this->request);
        die();
    }

    // replace every parameters (":param" or '?') with the correct value
    private function build_request() {
        $rebuilt_request = "";

        $param_count = 0;
        $is_param = false;
        for ($i = 0; $i < strlen($this->sql); $i++) {
            switch ($this->sql[$i]) {
                case ":":
                    $is_param = true;
                case "?":
                    $rebuilt_request .= $this->args[$param_count];
                    $param_count++;
                    break;
                case " ":
                    $is_param = false;
                default:
                    if (!$is_param) {
                        $rebuilt_request .= $this->sql[$i];
                    }
                    break;
            }
        }

        return $rebuilt_request;
    }

    private function get_args_count($sql) {
        $args_count = 0;

        for($i = 0; $i < strlen($sql); $i++) {
            switch ($sql[$i]) {
                case ":":
                case "?":
                    $args_count++;
                    break;
            }
        }

        return $args_count;
    }
}