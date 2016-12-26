<?php
include "../../db/DBConnect.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>MVC Constructor</title>
    </head>
    <body>
        <?php
        if (!isset($_GET["req"])) {
            $db_connection = new DBConnect("mysql", "", "", "", "");
            $con = $db_connection->getCon();
            $result = mysql_query("show databases");
            echo "<form action='mvc_constructor.php' method='get' target='res'><select name='db_name'>";
            while ($row = mysql_fetch_array($result)) {
                echo "<option value='" . $row["Database"] . "'>" . $row["Database"] . "</option>";
            }echo "</select><br><button name='req' value='Model'>Model</button><button name='req' value='Controller'>Controller</button></form><br><iframe id='res' name='res' style='border:1px solid black;width:95%;height:500px'></iframe>";
            $db_connection->mysql_connect_close();
        } else {
            $req = $_GET["req"];
            $db_name = $_GET["db_name"];
            if ($req == "Model") {
                $db_connection = new DBConnect("mysql", $db_name, "", "", "");
                $con = $db_connection->getCon();
                $result = mysql_query("show tables");
                $tables = array();
                $i = 0;
                while ($row = mysql_fetch_array($result)) {
                    $tables[$i] = $row["Tables_in_" . $db_name];
                    $i++;
                }
                $tables_size = $i;
                for ($i = 0; $i < $tables_size; $i++) {
                    $result = mysql_query("show columns from " . $tables[$i]) or die(mysql_error());
                    $table_details = array();
                    $j = 0;
                    $classname = strtoupper(substr($tables[$i], 0, 1)) . substr($tables[$i], 1);
                    $data = "<?php class " . $classname . "{";
                    while ($row = mysql_fetch_array($result)) {
                        $table_details[$j] = array(
                            "column_name" => $row["Field"],
                            "data_type" => $row["Type"],
                            "allowed_null" => $row["Null"],
                            "key" => $row["Key"],
                            "default" => $row["Default"],
                            "extra" => $row["Extra"]
                        );
                        $data.="private $" . $row["Field"] . ";";
                        $data.="public function get" . strtoupper(substr($row["Field"], 0, 1)) . substr($row["Field"], 1) . "(){ return \$this->" . $row["Field"] . "; }";
                        $data.="public function set" . strtoupper(substr($row["Field"], 0, 1)) . substr($row["Field"], 1) . "(\$" . $row["Field"] . "){ \$this->" . $row["Field"] . " = \$" . $row["Field"] . "; }";
                        $j++;
                    }
                    $data.="} ?>";
                    file_put_contents("../../model/" . $classname . "Model.php", $data);
                }
                $db_connection->mysql_connect_close();
            } else if ($req == "Controller") {
                $db_connection = new DBConnect("mysql", $db_name, "", "", "");
                $con = $db_connection->getCon();
                $result = mysql_query("show tables");
                $tables = array();
                $i = 0;
                while ($row = mysql_fetch_array($result)) {
                    $tables[$i] = $row["Tables_in_" . $db_name];
                    $i++;
                }
                $tables_size = $i;
                for ($i = 0; $i < $tables_size; $i++) {
                    $result = mysql_query("show columns from " . $tables[$i]);
                    $table_details = array();
                    $j = 0;
                    while ($row = mysql_fetch_array($result)) {
                        $table_details[$j] = array(
                            "column_name" => $row["Field"],
                            "data_type" => $row["Type"],
                            "allowed_null" => $row["Null"],
                            "key" => $row["Key"],
                            "default" => $row["Default"],
                            "extra" => $row["Extra"]
                        );
                        $j++;
                    }
                    $classname = strtoupper(substr($tables[$i], 0, 1)) . substr($tables[$i], 1) . "Controller";
                    $data = "<?php  class " . $classname . "{\n";
                    $data.=insertFunction($db_name, $tables[$i], $table_details, $j);
                    $data.=updateFunction($db_name, $tables[$i], $table_details, $j);
                    $data.=deleteFunction($db_name, $tables[$i], $table_details, $j);
                    $data.=getByPrimaryKeyFunction($db_name, $tables[$i], $table_details, $j);
                    $data.=findByAllFunction($db_name, $tables[$i], $table_details, $j);
                    $data.="} ?>";
                    file_put_contents("../../controller/" . $classname . ".php", $data);
                }
                $db_connection->mysql_connect_close();
            }
        }

        function insertFunction($db_name, $table_name, $table_details, $table_details_size) {
            $data = " function insert($" . $table_name . ",\$persistent_connection){\n";
            for ($i = 0; $i < $table_details_size; $i++) {
                if (strpos($table_details[$i]["extra"], "auto_increment") === false) {
                    $data.="$" . $table_details[$i]["column_name"] . " = $" . $table_name . "->get" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "();\n";
                }
            }
            $data.="\n";

            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection = new DBConnect(\"mysqli\", \"$db_name\", \"\", \"\", \"\");\n";
            $data.="\$con = \$db_connection->getCon();\n";
            $data.="}else{\n";
            $data.="\$con = \$persistent_connection;\n";
            $data.="}\n";

            $data.="\$query=\"insert into " . $table_name . "(\";\n";
            $data.="\$placeholder_list = \"\";\n";
            $data.="\$datatype_list = \"\";\n";
            $data.="\$argument_array = array();\n\$k=0;\n";
            for ($i = 0; $i < $table_details_size; $i++) {
                if (strpos($table_details[$i]["extra"], "auto_increment") === false) {
                    if ($table_details[$i]["data_type"] != "date" && $table_details[$i]["data_type"] != "time") {
                        $data.="if($" . $table_details[$i]["column_name"] . "!=null){\n";
                        $data.="\$query.=\"" . $table_details[$i]["column_name"] . ",\";\n";
                        $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                        $data.="\$placeholder_list.=\" ? ,\";\n";
                        $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                        $data.="\$k++;\n";
                        $data.="}\n";
                    } else {
                        if ($table_details[$i]["data_type"] == "date") {
                            $data.="if($" . $table_details[$i]["column_name"] . "!=null){\n";
                            $data.="\$query.=\"" . $table_details[$i]["column_name"] . ",\";\n";
                            $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                            $data.="\$placeholder_list.=\" ? ,\";\n";
                            $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                            $data.="\$k++;\n";
                            $data.="}else{\n";
                            $data.="\$query.=\"" . $table_details[$i]["column_name"] . ",\";\n";
                            $data.="\$placeholder_list.=\" CURDATE() ,\";\n";
                            $data.="}\n";
                        } else if ($table_details[$i]["data_type"] == "time") {
                            $data.="if($" . $table_details[$i]["column_name"] . "!=null){\n";
                            $data.="\$query.=\"" . $table_details[$i]["column_name"] . ",\";\n";
                            $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                            $data.="\$placeholder_list.=\" ? ,\";\n";
                            $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                            $data.="\$k++;\n";
                            $data.="}else{\n";
                            $data.="\$query.=\"" . $table_details[$i]["column_name"] . ",\";\n";
                            $data.="\$placeholder_list.=\" CURTIME() ,\";\n";
                            $data.="}\n";
                        }
                    }
                }
            }
            $data.="\$query = substr(\$query,0,-1);\n";
            $data.="\$placeholder_list = substr(\$placeholder_list,0,-1);\n";
            $data.="\$query.=\") values(\$placeholder_list)\";\n";
            $data.="\$statement = \$con->prepare(\$query);\n";
            $data.="\$argument_array = array_merge(array(\$datatype_list),array_values(\$argument_array));\n";
            $data.="\$tmp = array();\n";
            $data.="foreach(\$argument_array as \$key => \$value) \$tmp[\$key] = &\$argument_array[\$key];\n";
            $data.="call_user_func_array(array(\$statement,'bind_param'),\$tmp);\n";
            $data.="\$statement->execute();\n\$statement->close();\n";
            $data.="\$statement = \$con->prepare(\"select LAST_INSERT_ID() from $table_name\");\n";
            $data.="\$statement->execute();\n";
            $data.="\$statement->bind_result(\$last_id);\n";
            $data.="\$statement->fetch();\n";
            $data.="\$statement->close();\n";
            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection->mysqli_connect_close();\n";
            $data.="}\n";
            $data.="return \$last_id;\n";
            $data.="}\n";
            return $data;
        }

        function updateFunction($db_name, $table_name, $table_details, $table_details_size) {
            $data = " function update($" . $table_name . ",\$persistent_connection){\n";
            $argument_list = "";
            for ($i = 0; $i < $table_details_size; $i++) {
                $argument_list.="$" . $table_details[$i]["column_name"] . ",";
                $data.="$" . $table_details[$i]["column_name"] . " = $" . $table_name . "->get" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "();\n";
            }
            $data.="\n";

            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection = new DBConnect(\"mysqli\", \"$db_name\", \"\", \"\", \"\");\n";
            $data.="\$con = \$db_connection->getCon();\n";
            $data.="}else{\n";
            $data.="\$con = \$persistent_connection;\n";
            $data.="}\n";
            $data.="\$argument_array = array();\n\$k=0;\n";
            $data.="\$datatype_list=\"\";\n";
            $data.="\$query=\"update " . $table_name . " set\";\n";
            for ($i = 0; $i < $table_details_size; $i++) {
                if (strpos($table_details[$i]["extra"], "auto_increment") === false) {
                    if ($table_details[$i]["data_type"] != "date" && $table_details[$i]["data_type"] != "time") {
                        if ($table_details[$i]["data_type"] == "text" || strpos($table_details[$i]["data_type"], "char") != false || strpos($table_details[$i]["data_type"], "varchar") != false)
                            $data.="if($" . $table_details[$i]["column_name"] . "!=null || strlen(\$" . $table_details[$i]["column_name"] . ")!=0){\n";
                        else
                            $data.="if(strlen(\$" . $table_details[$i]["column_name"] . ")!=0){\n";
                        $data.="\$query.=\" " . $table_details[$i]["column_name"] . "=? ,\";\n";
                        $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                        $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                        $data.="\$k++;\n";
                        $data.="}\n";
                    } else {
                        if ($table_details[$i]["data_type"] == "date") {
                            $data.="if($" . $table_details[$i]["column_name"] . "!=null){\n";
                            $data.="\$query.=\"" . $table_details[$i]["column_name"] . "=? ,\";\n";
                            $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                            $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                            $data.="\$k++;\n";
                            $data.="}\n";
                        } else if ($table_details[$i]["data_type"] == "time") {
                            $data.="if($" . $table_details[$i]["column_name"] . "!=null){\n";
                            $data.="\$query.=\"" . $table_details[$i]["column_name"] . "=? ,\";\n";
                            $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                            $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                            $data.="\$k++;\n";
                            $data.="}\n";
                        }
                    }
                }
            }
            $data.="\$query = substr(\$query,0,-1);\n";
            $primary_key_part = "";
            for ($i = 0; $i < $table_details_size; $i++) {
                if ($table_details[$i]["key"] == "PRI") {
                    $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                    $data.="\$argument_array[\$k] = $" . $table_details[$i]["column_name"] . ";\n";
                    $data.="\$k++;\n";
                    $primary_key_part.=" where " . $table_details[$i]["column_name"] . "=? and";
                }
            }
            $primary_key_part = substr($primary_key_part, 0, -4);
            $data.="\n\$query.=\"" . $primary_key_part . "\";\n";
            $data.="\$statement = \$con->prepare(\$query);\n";
            $data.="\$argument_array = array_merge(array(\$datatype_list),array_values(\$argument_array));\n";
            $data.="\$tmp = array();\n";
            $data.="foreach(\$argument_array as \$key => \$value) \$tmp[\$key] = &\$argument_array[\$key];\n";
            $data.="call_user_func_array(array(\$statement,'bind_param'),\$tmp);\n";
            $data.="\$statement->execute();\n\$statement->close();";
            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection->mysqli_connect_close();\n";
            $data.="}\nreturn 1;\n}\n";
            return $data;
        }

        function deleteFunction($db_name, $table_name, $table_details, $table_details_size) {
            $key_column_names = array();
            $j = 0;
            for ($i = 0; $i < $table_details_size; $i++) {
                if ($table_details[$i]["key"] == "PRI") {
                    $key_column_names[$j] = $table_details[$i]["column_name"];
                    $j++;
                }
            }

            if ($j == 0) {
                echo "No <b>PRIMARY KEY</b> defined for table <b>" . $table_name . "</b>.  -- Found in <b>deleteFunction</b><br>";
                return;
            }

            $key_column_names_size = $j;
            $data = " function delete(";
            for ($i = 0; $i < $key_column_names_size; $i++) {
                $data.="\$" . $key_column_names[$i] . ",";
            }
            $data = substr($data, 0, -1);
            $data.= ",\$persistent_connection) {\n";
            $data.="if(";
            for ($i = 0; $i < $key_column_names_size; $i++) {
                $data.="\$" . $key_column_names[$i] . "==null ||";
            }
            $data = substr($data, 0, -2);
            $data.="){return;}\n";
            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection = new DBConnect(\"mysqli\", \"$db_name\", \"\", \"\", \"\");\n";
            $data.="\$con = \$db_connection->getCon();\n";
            $data.="}else{\n";
            $data.="\$con = \$persistent_connection;\n";
            $data.="}\n";

            $data.="\$query=\"delete from " . $table_name . " where";
            $datatype_list = "\"";
            $argument_list = "";
            for ($i = 0; $i < $key_column_names_size; $i++) {
                $data.=" " . $key_column_names[$i] . "=? and";
                $datatype_list.= getPHPPreparedStatementType($table_details[$i]["data_type"]);
                $argument_list.="$" . $key_column_names[$i] . ",";
            }
            $datatype_list.="\"";
            $data = substr($data, 0, -4);
            $data.="\";";
            $argument_list = substr($argument_list, 0, -1);
            $data.="\$statement = \$con->prepare(\$query);\n";
            $data.="\$statement->bind_param(" . $datatype_list . "," . $argument_list . ");\n";
            $data.="\$statement->execute();\n";
            $data.="\$statement->close();\n";
            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection->mysqli_connect_close();\n";
            $data.="}\nreturn 1;\n}\n";

            return $data;
        }

        function getByPrimaryKeyFunction($db_name, $table_name, $table_details, $table_details_size) {
            $key_column_names = array();
            $output_vars = "";
            $j = 0;
            for ($i = 0; $i < $table_details_size; $i++) {
                $output_vars.= "$" . $table_details[$i]["column_name"] . ",";
                if ($table_details[$i]["key"] == "PRI") {
                    $key_column_names[$j] = $table_details[$i]["column_name"];
                    $j++;
                }
            }

            if ($j == 0) {
                echo "No <b>PRIMARY KEY</b> defined for table <b>" . $table_name . "</b>.  -- Found in <b>getByPrimaryKeyFunction</b><br>";
                return;
            }

            $output_vars = substr($output_vars, 0, -1);
            $key_column_names_size = $j;
            $data = " function getByPrimaryKey(";
            for ($i = 0; $i < $key_column_names_size; $i++) {
                $data.="\$" . $key_column_names[$i] . ",";
            }
            $data = substr($data, 0, -1);
            $data.= ",\$request,\$clause,\$persistent_connection) {\n";

            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection = new DBConnect(\"mysqli\", \"$db_name\", \"\", \"\", \"\");\n";
            $data.="\$con = \$db_connection->getCon();\n";
            $data.="}else{\n";
            $data.="\$con = \$persistent_connection;\n";
            $data.="}\n";

            $data.="if(sizeof(\$request)==1 && \$request[0]==\"*\")";
            $data.="\$query=\"select * from " . $table_name . " where";
            $datatype_list = "\"";
            $argument_list = "";
            for ($i = 0; $i < $key_column_names_size; $i++) {
                $data.=" " . $key_column_names[$i] . "=? and";
                $argument_list.="$" . $key_column_names[$i] . ",";
            }
            $data = substr($data, 0, -4);
            $data.="\";\n";
            $data.="else{\n";
            $data.="\$query = \"select \".implode(\",\",\$request).\" from " . $table_name . " where";
            for ($i = 0; $i < $key_column_names_size; $i++) {
                $data.=" " . $key_column_names[$i] . "=? and";
            }
            $data = substr($data, 0, -4);
            $data.="\";\n";
            $data.="}\n";
            $argument_list = substr($argument_list, 0, -1);
            $data.="\$statement = \$con->prepare(\$query);\n";
            $data.=" \$statement->bind_param(";
            for ($i = 0; $i < $table_details_size; $i++) {
                if ($table_details[$i]["key"] == "PRI") {
                    $datatype_list.=getPHPPreparedStatementType($table_details[$i]["data_type"]);
                }
            }
            $datatype_list.="\"";
            $data.=$datatype_list . "," . $argument_list . ");\n\$statement->execute();\n";

            $data.="\$meta = \$statement->result_metadata();\n";
            $data.="while(\$field = \$meta->fetch_field()){\n";
            $data.="\$var = \$field->name;\n";
            $data.="\$\$var = null;\n";
            $data.="\$parameters[\$field->name] = &\$\$var;\n}\n";
            $data.="call_user_func_array(array(\$statement, 'bind_result'), \$parameters);\n";
            $data.="\$statement->fetch();\n";

            //$data.="\$statement->bind_result(" . $output_vars . ");\n";
            //$data.="\$statement->fetch();\n";
            $data.="\$statement->close();\n";
            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection->mysqli_connect_close();\n";
            $data.="}\n\n";

            $data.="if(sizeof(\$parameters)==0){\nreturn null;\n}\n";

            $data.="$" . $table_name . " = new " . strtoupper(substr($table_name, 0, 1)) . substr($table_name, 1) . "();\n";
            for ($i = 0; $i < $table_details_size; $i++) {
                $data.="if (in_array('" . $table_details[$i]["column_name"] . "', \$request) || \$request[0] == '*')";
                $data.="$" . $table_name . "->set" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "(\$parameters[\"" . $table_details[$i]["column_name"] . "\"]);\n";
            }
            $data.="return $" . $table_name . ";\n";

            $data.="\n}\n";
            return $data;
        }

        function findByAllFunction($db_name, $table_name, $table_details, $table_details_size) {
            $data = " function findByAll($" . $table_name . ",\$request,\$clause,\$persistent_connection){\n";
            $data.="if(sizeof(\$request)==1 && \$request[0]==\"*\"){\n";
            $data.= "\$query=\"select * from " . $table_name . " where 1=1\";\n}else{\n";
            $data.="\$query = \"select \".implode(\",\",\$request).\" from " . $table_name . " where 1=1\";\n";
            $data.="}\n";
            $data.="\$argument_array = array();\n";
            $data.="\$k=0;\n";
            $data.="\$datatype_list=\"\";\n";
            for ($i = 0; $i < $table_details_size; $i++) {
                if ($table_details[$i]["data_type"] == "text" || strpos($table_details[$i]["data_type"], "char") != false || strpos($table_details[$i]["data_type"], "varchar") != false)
                    $data.="if((\$e = $" . $table_name . "->get" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "())!=null || strlen((\$e = $" . $table_name . "->get" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "()))!=0){\n";
               else
                   $data.="if(strlen((\$e = $" . $table_name . "->get" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "()))!=0){\n";
                $data.="\$query.=\" and " . $table_details[$i]["column_name"] . "=?\";\n";
                $data.="\$datatype_list.=\"" . getPHPPreparedStatementType($table_details[$i]["data_type"]) . "\";\n";
                $data.="\$argument_array[\$k] = \$e;\n";
                $data.="\$k++;\n";
                $data.="}\n";
            }
            $data.="if(strlen(\$clause)!=0 || \$clause!=null){\n";
            $data.="\$query.=\" \".\$clause;";
            $data.="}\n";

            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection = new DBConnect(\"mysqli\", \"$db_name\", \"\", \"\", \"\");\n";
            $data.="\$con = \$db_connection->getCon();\n";
            $data.="}else{\n";
            $data.="\$con = \$persistent_connection;\n";
            $data.="}\n";

            $data.="\$statement = \$con->prepare(\$query);\n";
            $data.="\$argument_array = array_merge(array(\$datatype_list), array_values(\$argument_array));\n";
            $data.="\$tmp = array();\n";
            $data.="foreach (\$argument_array as \$key => \$value)\n";
            $data.="\$tmp[\$key] = &\$argument_array[\$key];\n";
            $data.="if(strlen(\$datatype_list)>0)\n";
            $data.="call_user_func_array(array(\$statement, 'bind_param'), \$tmp);\n";
            $data.="\$statement->execute();\n";
            $data.="\$meta = \$statement->result_metadata();\n";
            $data.="while (\$field = \$meta->fetch_field()) {\n";
            $data.="\$var = \$field->name;\n";
            $data.="$\$var = null;\n";
            $data.="\$parameters[\$field->name] = &$\$var;\n";
            $data.="}\n";
            $data.="call_user_func_array(array(\$statement, 'bind_result'), \$parameters);\n\n";

            $data.="\$i = 0;\n";
            $data.="$" . $table_name . "s = array();\n";
            $data.="while (\$statement->fetch()) {\n";
            $data.="$" . $table_name . "s[\$i] = new " . strtoupper(substr($table_name, 0, 1)) . substr($table_name, 1) . "();\n";
            for ($i = 0; $i < $table_details_size; $i++) {
                $data.="if(in_array(\"" . $table_details[$i]["column_name"] . "\",\$request) || \$request[0]=='*')";
                $data.="$" . $table_name . "s[\$i]->set" . strtoupper(substr($table_details[$i]["column_name"], 0, 1)) . substr($table_details[$i]["column_name"], 1) . "(\$parameters[\"" . $table_details[$i]["column_name"] . "\"]);\n";
            }
            $data.="\$i++;\n";
            $data.="}\n";


            $data.="if(\$persistent_connection==null){\n";
            $data.="\$db_connection->mysqli_connect_close();\n";
            $data.="}\n";
            $data.="return $" . $table_name . "s;\n";
            $data.="}\n";
            return $data;
        }

        function getPHPPreparedStatementType($mysql_datatype) {
            if (strpos($mysql_datatype, "int") !== false || strpos($mysql_datatype, "binary") !== false) {
                return "i";
            } else if (strpos($mysql_datatype, "text") !== false || strpos($mysql_datatype, "char") !== false || strpos($mysql_datatype, "date") !== false || strpos($mysql_datatype, "time") !== false) {
                return "s";
            } else if (strpos($mysql_datatype, "float") !== false || strpos($mysql_datatype, "double") !== false || strpos($mysql_datatype, "decimal") !== false) {
                return "d";
            } else {
                return "-";
            }
        }
        ?>
    </body>
</html>
