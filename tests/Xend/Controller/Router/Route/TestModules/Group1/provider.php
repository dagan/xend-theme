<?php

// Format: ($type, $subtype, $expectedController, $expetedAction)
$testCases = array(
	array('error', '404', 'error', 'error-404'),
	array('error', 'other', 'error', 'error'),
	array('comment', 'popup', 'index', 'comment'),
	array('index', 'home', 'index', 'home'),
	array('index', 'front', 'index', 'front'),
	array('single', 'post', 'index', 'post'),
	array('single', 'page', 'index', 'page',),
	array('single', 'attachment', 'index', 'attachment'),
	array('single', 'custom', 'index', 'single'),
	array('archive', 'custom', 'index', 'index'),
	array('archive', 'index', 'index', 'index'),
	array('category', 'name', 'index', 'category'),
	array('tag', 'label', 'index', 'tag'),
	array('taxonomy', 'tax', 'index', 'taxonomy'),
	array('date', 'time', 'index', 'date'),
	array('date', 'day', 'index', 'date'),
	array('date', 'month', 'index', 'date'),
	array('date', 'year', 'index', 'date'),
);