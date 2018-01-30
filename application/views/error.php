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
    <script>
        $(document).ready(function(){
            $('.back').click(function(){
                parent.history.back();
                return false;
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
    <div action="" method="post" class="jExp-table error">
        <h1>Error 
            <span>An unexpected error occurred.</span>
        </h1>
        <p>
            <?php echo $this->message; ?> Click <a href="#" class="back">here</a> to go back.
        </p>       
    </div>
    
  </body>
</html>
 


