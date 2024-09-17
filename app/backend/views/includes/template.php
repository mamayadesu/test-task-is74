<?php
use \test_is74\Controllers\View;

/** @var View $this */
?>
<html>
    <head>
        <title><?php echo $this->getHeaderTitle(); ?></title>
        <meta charset="utf-8">
    </head>
    <body>
        <h2><?php echo $this->title; ?></h2>
        <?php echo $this->pageContent; ?>
    </body>
</html>

