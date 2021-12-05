<?php

require_once "ExchangeRates.php";

$change = [['USD' => 123, 'rub']];
$newExcgange = new ExchangeRates($change);

var_dump($newExcgange);