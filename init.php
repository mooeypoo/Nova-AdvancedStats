<?php
/*!
 * AdvancesStats for Anodyne Nova 2
 *
 * Add advanced statistics to nova administration.
 */
require( __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');

$system = new AdvancedStats\System();
$system->install();
