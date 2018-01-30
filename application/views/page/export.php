<?php

header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $this->file );
header('Content-Description: Files of an applicant');

//get the zip content and send it back to the browser
readfile($this->file);