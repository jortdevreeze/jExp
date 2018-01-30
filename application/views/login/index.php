<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="UTF-8">
    <meta name="google" value="notranslate">
    <title>jExp</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->getPublicPath('styles'); ?>layout.css" />
    <script src="<?php echo $this->getPublicPath('scripts'); ?>jquery-1.11.3.min.js"></script>
    <script src="<?php echo $this->getPublicPath('scripts'); ?>jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){           
            $(".jExp-form").validate({
                rules: {
                   username: "required",
                   password: "required"
                },
                messages: {
                    username: "Please enter your username."  
                    password: "Please enter your password." 
                }
            });
        });
    </script>
  </head>

  <body>
    <p>&nbsp;</p>
    <form action="<?php echo $this->link('login'); ?>" method="post" class="jExp-form">
        <h1>Login Form 
            <span>Please fill all the texts in the fields.</span>
        </h1>
        <label>
            <span>Username :</span>
            <input id="username" type="text" name="username" placeholder="Enter your username" />
        </label>
        <label>
            <span>Password :</span>
            <input id="password" type="password" name="password" placeholder="Enter your password" />
        </label>

         <label>
            <span>&nbsp;</span> 
            <input type="submit" class="button" value="Login" /> 
        </label>    
    </form>
  </body>
</html>
 


