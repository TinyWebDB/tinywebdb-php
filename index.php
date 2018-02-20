<?php
$request = $_SERVER['REQUEST_URI'];
if (!isset($_SERVER['REQUEST_URI'])) {
    $request = substr($_SERVER['PHP_SELF'], 1);
    if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != '') {
        $request .= '?' . $_SERVER['QUERY_STRING'];
    }
}
$url_trigger = 'api';
if (isset($_POST['action'])) {
    $request = '/' . $url_trigger . '/' . $_POST['action'] . '/';
}
if (strpos('/' . $request, '/' . $url_trigger . '/')) {
    header("HTTP/1.1 200 OK");
    $tinywebdb_key = explode($url_trigger . '/', $request);
    $tinywebdb_key = $tinywebdb_key[1];
    $tinywebdb_key = explode('?', $tinywebdb_key);
    $action        = $tinywebdb_key[0];
    switch ($action) {
        case "getvalue": // this action enable from v 0.1.x
            // JSON_API , Post Parameters : tag
            $tagName  = $_REQUEST['tag'];
            $tagValue = file_get_contents($tagName . ".txt");
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');
            echo json_encode(array(
                "VALUE",
                $tagName,
                $tagValue
            ));
            exit;
            break;
        case "storeavalue": // this action will enable from v 0.2.x
            // JSON_API , Post Parameters : tag,value
            $tagName     = $_POST['tag'];
            $tagValue    = $_POST['value'];
            $apiKey      = $_POST['apikey'];
            $log_message = sprintf("%s:%s\n", date('Y-m-d H:i:s'), "storeavalue: ($apiKey) $tagName -- $tagValue");
            $file_name   = 'tinywebdb_' . date('Y-m-d') . '.log';
            error_log($log_message, 3, $file_name);
            $setting_apikey = '';
            if ($apiKey == $setting_apikey) {
                
                $fh = fopen($tagName . ".txt", "w");
                fputs($fh, $tagValue);
                fclose(fh);
                header('Cache-Control: no-cache, must-revalidate');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Content-type: application/json');
                echo json_encode(array(
                    "STORED",
                    $tagName,
                    $tagValue
                ));
            } else {
                echo "check api key.";
            }
            exit;
            break;
        default:
            break;
    }
}

$listLog = array();
$listTxt = array();
if ($handler = opendir("./")) {
    while (($sub = readdir($handler)) !== FALSE) {
        if (substr($sub, -4, 4) == ".txt") {
            $listTxt[] = $sub;
        } elseif (substr($sub, 0, 10) == "tinywebdb_") {
            $listLog[] = $sub;
        }
    }
    closedir($handler);
}
?>

<h1>TinyWebDB API and Log Tail</h1>
<h2>TinyWebDB API</h2>
<h3>TinyWebDB "getvalue" test form</h3>
<form method="GET" action="http://scc.digilib.org/api/getvalue">
        tag: <input type="text" name="tag" value="led12345"><br>
        <input type="submit" value="submit">
</form>

<h3>TinyWebDB "storeavalue" test form</h3>
<form action="http://scc.digilib.org/api/storeavalue" method="post">
  <div>tag: <input type="text" name="tag" value="led12345"></div>
  <div>value: <input type="text" name="value" value="blink"></div>
  <input type="submit" value="submit">
  <input type="reset" value="reset">
</form>

<?php
echo "<h3>TinyWebDB Tags</h3>";
echo "<table border=1>";
echo "<thead><tr>";
echo "<th> Tag Name </th>";
echo "<th> Size </th>";
echo "</tr></thead>\n";
if ($listTxt) {
    sort($listTxt);
    foreach ($listTxt as $sub) {
        echo "<tr>";
        echo "<td><a href=getvalue?tag=" . substr($sub, 0, -4) . ">" .substr($sub, 0, -4) . "</a></td>\n";
        echo "<td>" . filesize("./" . $sub) . "</td>\n";
        echo "</tr>";
    }
}
echo "</table>";

echo "<h3>TinyWebDB Log Tail</h3>";
echo "<table border=1>";
echo "<thead><tr>";
echo "<th> Log Name </th>";
echo "<th> Size </th>";
echo "</tr></thead>\n";
if ($listLog) {
    sort($listLog);
    foreach ($listLog as $sub) {
        echo "<tr>";
        echo "<td><a href=?logfile=" . $sub . ">$sub</a></td>\n";
        echo "<td>" . filesize("./" . $sub) . "</td>\n";
        echo "</tr>";
    }
}
echo "</table>";

if ($_GET['logfile']) {
    $logfile = substr($_GET['logfile'], 0, 24);
    echo "<h2>Log file : " . $logfile . "</h2>";
    $lines = wp_tinywebdb_api_read_tail($logfile, 20);
    foreach ($lines as $line) {
        echo $line . "<br>";
    }
}


exit; // this stops rest steps

function wp_tinywebdb_api_read_tail($file, $lines)
{
    //global $fsize;
    $handle      = fopen($file, "r");
    $linecounter = $lines;
    $pos         = -2;
    $beginning   = false;
    $text        = array();
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if (fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }
        $linecounter--;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines - $linecounter - 1] = fgets($handle);
        if ($beginning)
            break;
    }
    fclose($handle);
    return array_reverse($text);
}

