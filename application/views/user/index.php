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
            $(".delete").click(function(){
                if (!confirm("Are you sure you want to delete this user?")){
                    return false;
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
    <div action="" method="post" class="jExp-table">
        <h1>Users 
            <span>An overview of all users.</span>
        </h1>
        <?php if ($this->users->num_rows > 0) { ?>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th colspan="2">E-mail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->users as $id => $user) { ?>

                    <tr>
                        <td><?php echo $id+1; ?></td>
                        <td><?php echo $user['surname'] . ' ' . $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <a href="<?php echo $this->link('user/edit', array('id'=>$user['id'])); ?>" class="button">Edit</a>
                            <a href="<?php echo $this->link('user/delete', array('id'=>$user['id'])); ?>" class="delete">Delete</a>
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>
            </table>
        
        <?php } ?>
        <a href="<?php echo $this->link('user/add'); ?>" class="frm-button">New User</a>
    </div>
    
  </body>
</html>
 


