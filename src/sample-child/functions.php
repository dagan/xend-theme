<?php

function xend_child() {
    return new \Xend\Options(__DIR__, array(
        'viewBasePath'    => __DIR__ . '/views',
        'childModuleName' => 'xend-child',
    ));
}