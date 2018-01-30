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
                window.location.href = '/index/';
                return false;
            });
            $(".jExp-form").validate({
                rules: {
                   name: "required"
                },
                messages: {
                    name: "Please enter an experiment name."                  
                }
            });
            
        });
    </script>
  </head>

  <body>
    <p>&nbsp;</p>
    <div class="jExp-header">
        <a href="<?php echo $this->link('user'); ?>">User Management</a>
        <span> | </span>
        <a href="<?php echo $this->link('logout'); ?>">Logout</a>
    </div>
    <form action="<?php echo $this->link('create'); ?>" method="post" class="jExp-form">
        <h1>Experiment Form 
            <span>Please fill all the texts in the fields.</span>
        </h1>
        <label>
            <span>Experiment Name :</span>
            <input id="name" type="text" name="name" placeholder="Enter a name for the experiment" />
        </label>
 
         <label>
            <span>&nbsp;</span> 
            <input type="submit" class="button" value="Add" />  
            <input type="submit" class="button" id="cancel" value="Cancel" /> 
        </label>    
    </form>
    
  </body>
</html>
 


