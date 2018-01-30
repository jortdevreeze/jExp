<?php
/**
* +---------------------------------------------------------------------------+
* | Copyright (c) 2015, Jort de Vreeze                                        |
* | All rights reserved.                                                      |
* |                                                                           |
* | Redistribution and use in source and binary forms, with or without        |
* | modification, are not permitted.                                          |
* +---------------------------------------------------------------------------+
* | jExp 1.0                                                                  |
* +---------------------------------------------------------------------------+
* | install.php                                                               |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

/**
 * Determine installation process.
 */
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
if ($method == 'POST') {
    
    $host = filter_input(INPUT_POST, 'host');
    $username = filter_input(INPUT_POST, 'username');
    $dbpassword = filter_input(INPUT_POST, 'dbpassword1');
    $dbname = filter_input(INPUT_POST, 'dbname');
    
    $surname = filter_input(INPUT_POST, 'surname');
    $name = filter_input(INPUT_POST, 'name');
    $email = filter_input(INPUT_POST, 'email');
    $password = filter_input(INPUT_POST, 'password1');
    
    if (null !== $username && 
        null !== $dbpassword &&
        null !== $dbname &&
        null === $surname &&
        null === $name && 
        null === $email &&
        null === $password) {
    
        if (null === $host || empty($host)) {
            $host = 'localhost';
        }
     
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="UTF-8">
    <meta name="google" value="notranslate">
    <title>jExp Installation</title>
    <link rel="stylesheet" type="text/css" href="public/styles/layout.css" />
    <script src="public/js/jquery-1.11.3.min.js"></script>
    <script src="public/js/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            
            $(".jExp-form").validate({
                rules: {
                   surname: "required",
                   name: "required",
                   email: {
                        required: true,
                        email: true
                    },
                    password1: {
                        required: true,
                        minlength: 5
                    },
                    password2: {
                        equalTo: "#password1"
                    }
                },
                messages: {
                    surname: "Please enter your firstname.",
                    name: "Please enter your lastname.",
                    email: "Please enter a valid email address.",
                    password1: {
                        required: "Please provide a valid password.",
                        minlength: "Your password must be at least 5 characters long."
                    },
                    password2: {
                        required: "Please provide a valid password.",
                        minlength: "Your password must be at least 5 characters long.",
                        equalTo: "Your passwords must match."
                    }                    
                }
            });
            
        });
    </script>
  </head>

  <body>
    <p>&nbsp;</p>
    <div class="jExp-header"></div>
    <form action="install.php" method="post" class="jExp-form">
        <h1>Step 2 - Add User 
            <span>Please fill all the texts in the fields.</span>
        </h1>
        <label>
            <span>First name :</span>
            <input id="surname" type="text" name="surname" placeholder="Enter your first name" />
        </label>
        <label>
            <span>Name :</span>
            <input id="name" type="text" name="name" placeholder="Enter your name" />
        </label>
        <label>
            <span>E-mail :</span>
            <input id="email" type="text" name="email" placeholder="Enter your e-mail address" />
        </label>
        <label>
            <span>Password :</span>
            <input id="password1" type="password" name="password1" placeholder="Enter your password" />
        </label>
        <label>
            <span>Confirm Password :</span>
            <input id="password2" type="password" name="password2" placeholder="Confirm your password" />
        </label>
        <input id="host" type="hidden" name="host" value="<?php echo $host; ?>" />
        <input id="username" type="hidden" name="username" value="<?php echo $username; ?>" />
        <input id="dbpassword1" type="hidden" name="dbpassword1" value="<?php echo $dbpassword; ?>" />
        <input id="dbname" type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
         <label>
            <span>&nbsp;</span> 
            <input type="submit" class="button" value="Finish" />
        </label>    
    </form>
  </body>
</html>
            
<?php
    } else {        
        if (null !== $host &&
            null !== $username && 
            null !== $dbpassword &&
            null !== $dbname &&
            null !== $surname &&
            null !== $name && 
            null !== $email &&
            null !== $password) {
            
            /**
             * Create configuration file
             */ 
            if (!$handle = fopen('settings.ini', 'w')) {
                echo "Cannot create the configuration file";
                exit;
            }    

            fwrite($handle, "[model]\n");
            fwrite($handle, sprintf("host='%s'\n", $host));
            fwrite($handle, sprintf("username='%s'\n", $username));
            fwrite($handle, sprintf("password='%s'\n", $dbpassword));
            fwrite($handle, sprintf("dbname='%s'\n", $dbname));

            fclose($handle);

            /**
             * Create database tables
             */
            $connection = @new mysqli($host, $username, $dbpassword);

            if (!$connection) {
                die('Connect Error: ' . mysqli_connect_error());
            }

            if (false === mysqli_select_db($connection, $dbname)) {     
                $connection->query(
                    sprintf("CREATE DATABASE %s", $dbname)  
                );
                mysqli_select_db($connection, $dbname);
            }

            $sql[] = 'CREATE TABLE IF NOT EXISTS experiment ( '.
                'id INT(11) NOT NULL AUTO_INCREMENT, '.
                'name VARCHAR(128) NOT NULL, '.
                'identifier VARCHAR(128) NOT NULL, '.
                'PRIMARY KEY ( id ))';
            $sql[] = 'CREATE TABLE IF NOT EXISTS session ( '.
                'id INT(11) NOT NULL AUTO_INCREMENT, '.
                'experiment VARCHAR(128) NOT NULL, '.
                'identifier VARCHAR(128) NOT NULL, '.
                'timestamp datetime NOT NULL, '.
                'question VARCHAR(128) NOT NULL, '.
                'error tinyint(128) NOT NULL, '.
                'data blob NOT NULL, '.
                'PRIMARY KEY ( id ))';
            $sql[] = 'CREATE TABLE IF NOT EXISTS user ( '.
                'id INT(11) NOT NULL AUTO_INCREMENT, '.
                'surname VARCHAR(128) NOT NULL, '.
                'name VARCHAR(128) NOT NULL, '.
                'email VARCHAR(128) NOT NULL, '.
                'password VARCHAR(128) NOT NULL, '.
                'PRIMARY KEY ( id ))';

            foreach ($sql as $query) {
                if($connection->query(strval($query)) === false) {
                    echo "Cannot configure the database";
                    exit;
                }
            }
            
            
            if($connection->query(strval($query)) === false) {
                echo "Cannot configure the database";
                exit;
            }
            $add = $connection->query(
                sprintf(
                    "INSERT INTO user (surname, name, email, password) VALUES ('%s', '%s', '%s', '%s')", 
                    $surname, $name, $email, md5($password)
                )
             );
            
            if (false === $add) {
                echo "Cannot add user to the database";
                exit;
            }
            
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="UTF-8">
    <meta name="google" value="notranslate">
    <title>jExp Installation</title>
    <link rel="stylesheet" type="text/css" href="public/styles/layout.css" />
    <script src="public/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            
            window.setTimeout(function() {
                window.location = 'index.php';
            }, 5000);
            
        });
    </script>
  </head>

  <body>
    <p>&nbsp;</p>
    <div class="jExp-header">
    </div>
    <form action="install.php" method="post" class="jExp-form">
        <h1>Finished 
            <span>The installation is completed</span>
        </h1>
        <p>You are being redirected to the login page in five seconds</p>
    </form>
  </body>
</html>

<?php
            unlink(__FILE__);

        } else {
            echo "An unknown error occured. The installation is not able to continue.";
            exit;
        }
    }

} else {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="UTF-8">
    <meta name="google" value="notranslate">
    <title>jExp Installation</title>
    <link rel="stylesheet" type="text/css" href="public/styles/layout.css" />
    <script src="public/js/jquery-1.11.3.min.js"></script>
    <script src="public/js/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            
            $(".jExp-form").validate({
                rules: {
                    dbname: "required",
                    username: "required",
                    dbpassword1: {
                        required: true,
                        minlength: 5
                    },
                    dbpassword2: {
                        equalTo: "#dbpassword1"
                    }
                },
                messages: {
                    dbname: "Please enter a valid database name.",
                    username: "Please enter a valid username.",
                    dbpassword1: {
                        required: "Please provide a valid password.",
                        minlength: "Your password must be at least 5 characters long."
                    },
                    dbpassword2: {
                        required: "Please provide a valid password.",
                        minlength: "Your password must be at least 5 characters long.",
                        equalTo: "Your passwords must match."
                    }                    
                }
            });
            
        });
    </script>
  </head>

  <body>
    <p>&nbsp;</p>
    <div class="jExp-header">
    </div>
    <form action="install.php" method="post" class="jExp-form">
        <h1>Step 1 - Configure Database 
            <span>Please fill all the texts in the fields.</span>
        </h1>
        <label>
            <span>Hostname :</span>
            <input id="host" type="text" name="host" placeholder="Enter the hostname" />
        </label>
        <label>
            <span>Database Name :</span>
            <input id="dbname" type="text" name="dbname" placeholder="Enter the database name" />
        </label>
        <label>
            <span>Username :</span>
            <input id="username" type="text" name="username" placeholder="Enter your username" />
        </label>
        <label>
            <span>Password :</span>
            <input id="dbpassword1" type="password" name="dbpassword1" placeholder="Enter your password" />
        </label>
        <label>
            <span>Confirm Password :</span>
            <input id="dbpassword2" type="password" name="dbpassword2" placeholder="Confirm your password" />
        </label>

         <label>
            <span>&nbsp;</span> 
            <input type="submit" class="button" value="Next" />
        </label>    
    </form>
  </body>
</html>
<?php } ?>
