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
                if (!confirm("Are you sure you want to delete this experiment?")){
                    return false;
                }
            });
            $(".empty").click(function(){
                if (!confirm("Are you sure you want to clear all data from this experiment?")){
                    return false;
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
    <div action="" method="post" class="jExp-table">
        <h1>Experiments 
            <span>An overview of all experiments.</span>
        </h1>
        <?php if ($this->experiments->num_rows > 0) { ?>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Identifier</th>
                        <th colspan="2">Records</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->experiments as $id => $experiment) { ?>

                    <tr>
                        <td><?php echo $id+1; ?></td>
                        <td><?php echo $experiment['name']; ?></td>
                        <td><?php echo $experiment['identifier']; ?></td>
                        <td><?php echo $this->records[$id]; ?></td>
                        <td>
                            <a href="<?php echo $this->link('export', array('id'=>$experiment['id'])); ?>" class="button">Export</a>
                            <a href="<?php echo $this->link('edit', array('id'=>$experiment['id'])); ?>" class="button">Edit</a>
                            <a href="<?php echo $this->link('delete', array('id'=>$experiment['id'])); ?>" class="delete">Delete</a>
                            <a href="<?php echo $this->link('clear', array('id'=>$experiment['id'])); ?>" class="empty">Empty</a>
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>
            </table>
        
        <?php } ?>
        <a href="<?php echo $this->link('create'); ?>" class="frm-button">New Experiment</a>
    </div>
    
  </body>
</html>
 


