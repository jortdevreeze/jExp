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
            
            $("#cancel").click(function(){
                window.location.href = '/user/';
                return false;
            });
            
            $(".jExp-form").validate({
                rules: {
                   surname: "required",
                   name: "required",
                   email: {
                        required: true,
                        email: true
                    },
                    password1: {
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
                        minlength: "Your password must be at least 5 characters long."
                    },
                    password2: {
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
        <a href="<?php echo $this->link('index'); ?>">Overview</a>
        <span> | </span>
        <a href="<?php echo $this->link('logout'); ?>">Logout</a>
    </div>
    <form action="<?php echo $this->link('user/edit', array('id' => $this->user['id'])); ?>" method="post" class="jExp-form">
        <h1>Add User 
            <span>Please fill all the texts in the fields.</span>
        </h1>
        <label>
            <span>First name :</span>
            <input id="surname" type="text" name="surname" value="<?php echo $this->user['surname']; ?>" />
        </label>
        <label>
            <span>Name :</span>
            <input id="name" type="text" name="name" value="<?php echo $this->user['name']; ?>" />
        </label>
        <label>
            <span>E-mail :</span>
            <input id="email" type="text" name="email" value="<?php echo $this->user['email']; ?>" />
        </label>
        <label>
            <span>Password :</span>
            <input id="password1" type="password" name="password1" value="" />
        </label>
        <label>
            <span>Confirm Password :</span>
            <input id="password2" type="password" name="password2" value="" />
        </label>

         <label>
            <span>&nbsp;</span> 
            <input type="submit" class="button" value="Change" />
            <input type="submit" class="button" id="cancel" value="Cancel" /> 
        </label>    
    </form>
  </body>
</html>
 


