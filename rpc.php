<?php
$host = "localhost";
$user = "root";
$pass = "mysql";
$name = "evt";


function backup_tables($host,$user,$pass,$name,$tables = '*')
{
    $link = mysql_connect($host,$user,$pass);
    mysql_select_db($name,$link);
    
    //get all of the tables
    if($tables == '*')
    {
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result))
        {
            $tables[] = $row[0];
        }
    }
    else
    {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }
    
    //cycle through
    foreach($tables as $table)
    {
        $result = mysql_query('SELECT * FROM '.$table);
        $num_fields = mysql_num_fields($result);
        
        $return.= 'DROP TABLE '.$table.';';
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
        
        for ($i = 0; $i < $num_fields; $i++) 
        {
            while($row = mysql_fetch_row($result))
            {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j<$num_fields; $j++) 
                {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j<($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
            }
        }
        $return.="\n\n\n";
    }
    
    //save file
    $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
    fwrite($handle,$return);
    fclose($handle);

}



function backup()
{
    global $user,$pass, $host, $name;
    backup_tables($host,$user,$pass,$name);

}

function execsql()
{
    global $user,$pass, $host, $name;
    $db = mysql_connect($host, $user, $pass) or die("error: cannot connect to $host");
    mysql_select_db($name, $db) or die("error: cannot select database");

    $sql = $_REQUEST['sql'];
    
    $result = mysql_query($sql, $db);
    if($result === TRUE)
    {
        echo mysql_info(); 
        return null;
    }
    elseif($result == FALSE)
    {
        echo "Error in Sql-Statement:<br>".mysql_error();
        return null;
    }
    else
    {
        $ncols = mysql_num_fields($result);
        
        echo "<table><tr>"; 
        for($i = 0; $i < $ncols; $i++) 
        { 
            $finfo = mysql_fetch_field($result, $i);
            echo "<th>",$finfo->name,"</th>";
        }
        echo "</tr>";

        while($row = mysql_fetch_row($result))
        {
            echo "<tr>";
            foreach($row as $field)
                echo "<td>$field</td>";
            echo "</tr>";
        }        
        echo "</table>";
    }
}
$obj = call_user_func($_REQUEST['fn']);
?>
