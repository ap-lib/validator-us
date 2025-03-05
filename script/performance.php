<?php declare(strict_types=1);

use AP\Validator\US\AddressSanitizes;
use AP\Validator\US\CitySanitizes;
use AP\Validator\US\NameSanitizes;

include __DIR__ . "/../vendor/autoload.php";

$i = 0;
microtime(true);
$name = "anton";

$iter = 10000;

$v1    = new AddressSanitizes();
$start = microtime(true);
for ($i = 0; $i < $iter; $i++) {
    $v1->validateString($name);
}
echo sprintf("address: %.05f \n", microtime(true) - $start);

$v1    = new NameSanitizes();
$start = microtime(true);
for ($i = 0; $i < $iter; $i++) {
    $v1->validateString($name);
}
echo sprintf("name: %.05f \n", microtime(true) - $start);

$v1    = new CitySanitizes();
$start = microtime(true);
for ($i = 0; $i < $iter; $i++) {
    $v1->validateString($name);
}
echo sprintf("city: %.05f \n", microtime(true) - $start);

$start = microtime(true);
for ($i = 0; $i < $iter; $i++) {
    //$v1->validateString($name);
}
echo sprintf("nothing: %.05f \n", microtime(true) - $start);