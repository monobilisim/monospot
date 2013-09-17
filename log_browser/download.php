<?php

$file = $_GET['file'];
$filename = basename($file);

header('Content-type: application/x-gzip');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile('/logimza/' . $file);

exit;