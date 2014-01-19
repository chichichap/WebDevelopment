<?php
echo anchor('admin.php','Back') . "<br />";

//And if the $site variable is not empty we echo it's content by using the generate method of the table class / library
if(!empty($tickets)) echo $this->table->generate($tickets); 

?>