<?php
use \test_is74\Controllers\View;
use \test_is74\Layout;

/** @var View $this */
?>
<html>
    <head>
        <title><?php echo $this->getHeaderTitle(); ?></title>
        <meta charset="utf-8">
        <?php echo Layout::getInstance()->compileFrontendHeaders(); ?>
    </head>
    <body>
        <div class="page__wrapper">
            <h2><?php echo $this->title; ?></h2>
            <hr>
            <?php echo $this->pageContent; ?>
        </div>
    </body>
</html>

