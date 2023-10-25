<?php
require "vendor/autoload.php";
include "apps/core.php";

use EmailChecker\EmailChecker;
use EmailChecker\Adapter;

$dennylist = array_unique(explode("\n", str_replace("\r", "", file_get_contents("../core/dennylist.txt"))));

$disposable = new EmailChecker(new Adapter\ArrayAdapter($dennylist));

function is_disposable($email)
{
    global $disposable;
    return $disposable->isValid($email);
}
