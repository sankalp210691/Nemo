<?php
if (isset($_GET["req"])) {
    $ajaxtext = trim($_GET["ajaxtext"]);

    $url = trim(substr($ajaxtext, strpos($ajaxtext, "url: ") + 6, strpos($ajaxtext, ",") - strpos($ajaxtext, "url: ")));
    $url = substr($url, 0, -2);

    $ajaxtext = substr($ajaxtext, strpos($ajaxtext, "type"));

    $type = trim(substr($ajaxtext, strpos($ajaxtext, "type: ") + 7, strpos($ajaxtext, ",") - strpos($ajaxtext, "type: ")));
    $type = substr($type, 0, -2);

    $datatype = null;
    if (strpos($ajaxtext, "dataType") != false) {
        $ajaxtext = substr($ajaxtext, strpos($ajaxtext, "dataType"));
        $datatype = trim(substr($ajaxtext, strpos($ajaxtext, "dataType: ") + 11, strpos($ajaxtext, ",") - strpos($ajaxtext, "dataType: ")));
        $datatype = substr($datatype, 0, -2);
    }

    $ajaxtext = substr($ajaxtext, strpos($ajaxtext, "data:"));

    $data = trim(substr($ajaxtext, strpos($ajaxtext, "data: ") + 7, strpos($ajaxtext, ",") - strpos($ajaxtext, "data: ")));
    $data = substr($data, 0, -2);
    $data = str_replace("\"", "", $data);

    $args = explode("&", $data);
    $i = 0;
    $func = "";
    $funct = "";
    $arg_string = "";
    if ($type == "get")
        $type = "\$_GET[";
    else if ($type == "post")
        $type = "\$_POST[";
    for ($i = 0; $i < sizeof($args); $i++) {
        $l = substr($args[$i], 0, strpos($args[$i], "="));
        if ($l == "req") {
            $funct = substr($args[$i], strpos($args[$i], "=") + 1);
        }
        $args[$i] = $l;
    }

    $funcNameParts = explode("_", $funct);
    $funcs = "";
    if (sizeof($funcNameParts) > 1) {
        $func = "";
        for ($i = 0; $i < sizeof($funcNameParts); $i++) {
            if ($i != 0) {
                $funcNameParts[$i] = strtoupper(substr($funcNameParts[$i], 0, 1)) . substr($funcNameParts[$i], 1);
            }
            $func.=$funcNameParts[$i];
            $funcs.=$funcNameParts[$i];
        }
    } else {
        $func.=$funcNameParts[0];
        $funcs.=$funcNameParts[0];
    }
    $func.="(";
    $funcs.="(";
    for ($i = 1; $i < sizeof($args); $i++) {
        $func.=($type . "\"" . $args[$i] . "\"],");
        $funcs.=("\$" . $args[$i] . ",");
    }
    $func = substr($func, 0, -1);
    $funcs = substr($funcs, 0, -1);
    $func.=")";
    $funcs.=")";

    $req = $_GET["req"];
    if ($req == "Manager") {
        $string = "\nelse if(\$req==\"$funct\"){\n";
        if ($datatype == "json") {
            $string.="echo json_encode(";
        } else {
            $string.="echo ";
        }

        $string.=$func;
        if ($datatype == "json") {
            $string.=");\n";
        } else {
            $string.=";\n";
        }
        $string.="}\n";
        $cur_content = trim(file_get_contents("../../" . $url));
        $final = substr($cur_content, 0, strpos($cur_content, "else {"));
        $final.=$string;
        $final.=(substr($cur_content, strpos($cur_content, "else {")));
        file_put_contents("../../" . $url, $final);
    } else if ($req == "Supporter") {
        $string = "\nfunction " . $funcs . "{\n";
        $url = str_replace("manager", "supporter", $url);
        $url = str_replace("Manager", "Supporter", $url);
        $cur_content = trim(file_get_contents("../../" . $url));
        $string.="}\n";
        $final = substr($cur_content, 0, -2);
        $final.=$string;
        $final.="?>";
        file_put_contents("../../" . $url, $final);
    }
} else {
    ?>
    <html style="width:100%;">
        <head>
            <title>AJAX Backender</title>
        </head>
        <body style="width:100%;">
            <form action="ajaxBackender.php" method="GET" target="res">
                <textarea name="ajaxtext" style="width:50%;height:200px;resize:none;"></textarea>
                <br>
                <button name="req" value="Manager">Manager</button>
                <button name="req" value="Supporter">Supporter</button>
            </form>
            <iframe id="res" name="res" style="width:50%;height:200px;border:1px solid black;"></iframe>
        </body>
    </html>
    <?php
}
?>