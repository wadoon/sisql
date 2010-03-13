<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/xml; charset=utf-8"0>
    <title>Sql-Simple</title>
    <script language="javascript" src="jquery-1.4.2.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?php 
error_reporting(E_ALL);

//for yaml
include("spyc.php");

$sqltemplates = spyc_load_file( "templates.yaml"  );

$categories = array();
foreach($sqltemplates as $t)
{
    $c = explode(",", $t['tags']);
    foreach($c as $d)
    {
        $categories[$d][]=$t;
    }
}

?>

<h1>Sql Simple</h1>
<div id="special"> 
    <a href="#" onclick="$.get('rpc.php',{fn:'backup'},function(data){alert('Datenbank gesichert');});" >Datenbank sichern</a> <b>|</b>
    <a href="help.html">Hilfe</a>
</div>

<div id="content">

<h2>Wählen Sie Ihre Aufgabe:</h2>
<select id="selTemplate" size="15">
    <? 
    foreach($categories as $key => $tpls)
    {
        echo "<optgroup label=\"$key\">";
        foreach($tpls as $tpl)   
            echo "<option value='$tpl[name]'>$tpl[desc]</option>";
        echo "</optgroup>";
    }
    ?>
</select>

<button id="preview">SQL Preview</button>
<h2>Setzen Sie die Variablen:</h2>
<div id="input"></div>
<div id="sqlpreview"></div>

<button id="exec">Execute</button>
<h2>Durchführung</h2>
<div id="exec"></div>

<!-- <pre><? print_r($sqltemplates); ?></pre> -->

<script language="javascript">
        var templates=<?=json_encode($sqltemplates);?>;
    </script>
    <script language="javascript" src="function.js"></script>
</div>
</body>
</html>
