<?php 
$host = 'localhost';
$user = 'user';
$passwd = 'passed';
$dbname = 'dbname';
$tables = array();


// 链接
$link = mysql_connect($host, $user, $passwd);
if($link) mysql_select_db($dbname);
mysql_query('set  names utf8');


// 获得数据库下表
$result =  mysql_query("show tables;", $link);
if($result) {
    while($ans = mysql_fetch_row($result)) {
        $tables[] = $ans[0];
    }
}
setcookie('k1','v1');
setcookie('k2', 'v2');

if(empty($_COOKIE['tbname'])) {
    setcookie('tbname', $tables[0] );
}

// 删除一条记录

if(isset($_GET['del']) && isset($_GET['tbname'])) {
    $sql = "delete from  {$_GET['tbname']} where id='{$_GET['del']}'";
    if(mysql_query($sql)) {
        header("Location:{$_SERVER['SCRIPT_NAME']}");
    }else {
        echo "del Error";
    }
}

// 打印表信息
if(isset($_GET['name'])) {
    if(in_array($_GET['name'], $tables)) {
        prt($_GET['name']); 
        die();
    }
}


// 表示要添加一行数据
if(!empty($_POST)) {
    $tbname = $_GET['tbname'];
    
    $k = implode(',' , array_keys($_POST));
    $v = implode("','" , array_values($_POST));

    $sql = "insert into $tbname ($k) values (' $v ') ";
    if(mysql_query($sql)) {
        header("Location:{$_SERVER['SCRIPT_NAME']}");
    } else {
        echo "ERROR";
        var_dump($sql);
    }
    die();
}

// 获得表的字段
function get_fields($tbname) {
    $result = mysql_query("describe $tbname");
    $ans = array();
    if($result) {
        while($row = mysql_fetch_row($result)) {
            $ans[] = $row[0];
        }
    }
    return $ans;
}

// 打印用 table 的形式打印
function prt($name) {

    setcookie('tbname' , $name);
    $field = get_fields($name);

    echo "<p> 表 $name 添加一条数据  </p>";
        echo "<form method='post' action='?tbname=$name' >";

    echo "<table border='1'>";
        echo "<tr>";
        foreach ($field as $v) {
            echo "<td>{$v}</td>";
        }
        echo "<td></td>";
        echo "</tr>";
        echo "<tr>";
        foreach ($field as $v) {
            echo "<td><input type='text' name=$v> </td>";
        }
        echo "<td> <input type='submit' value='添加一条数据'> </td>";
        echo "</tr>";;
    echo "</table>";

        echo "</form>";
    echo "<P>表 $name 的信息</p>";
    $result = mysql_query("select * from $name");
    if($result) {
        while($ans = mysql_fetch_assoc($result)) {
            $info[] = $ans;
        }
    }


        echo "<table border='1'>";
        echo "<tr>";

        echo "</tr>";
        
        
        echo "<tr>";
        foreach ($field as $v) {
            echo "<td>{$v}</td>";
        }
        echo "<td>删除(del) </td>";
        echo "</tr>";

    if(!isset($info)) echo "目前表 $name 是空表";
    else {




        foreach($info as $v ) {
            echo "<tr>";
            foreach($v as $d) {
                echo "<td> $d </td>";
            }
            echo "<td style='text-align: center; min-width:100px '><a  href='?del={$v['id']}&tbname=$name' data='{$v['id']}' >{$v['id']}</td>";


            echo '</tr>';
        }
    
    }
        
    echo "</table>";



    echo "<script>
        $(function () {
            $('table input:first').remove();        

        })

     </script>"; 

}

?>


<!Doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <title>  </title>
        <script src="//libs.baidu.com/jquery/1.8.3/jquery.js"></script>
        <style>
            body, div, span, a, li {margin: 0; padding: 0;}
            #main { width: 100%;}
            #zuo { width: 10%; float: left;}
            #you { width: 90%; float: left;}
        </style>
<script>

function cookie2json () {
    var cookie = '{' +  document.cookie + " ' }";
    var cookie = cookie.replace(/=/g, ":'").replace(/;/g, "',");
    return eval('(' +  cookie  + ')')
}

</script>
        <script>
$(function() {
    var cookies = cookie2json();
    var view = cookies.tbname;
    $("#zuo a").click( function () {
        $.get("", {'name': $(this).attr('data')}, function($data) {
            $("#you").html($data);
        });
    });
    

    // 如果view 就是要显示的表， 就表示要显示的那个表
    if(view) {
        $("#zuo a[data="+ view +"]").trigger('click');
    }
});
        </script>
    </head>
    <body>
        <div id='header'>

        </div>

        <div id='main'>
            <div id='zuo'>
                <p>数据库  <?php $dbname  ?>    <p/>
                <ul>
<?php
foreach($tables as $v) {
    echo "<li> <a href='#' data = '$v'>$v</a></li>";
}


?>
                </ul>
            </div>

            <div id='you'>

            </div>
        </div>         
    </body>
</html>
