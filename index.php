<?php
require_once "ExchangeRates.php";

try {
  $change = ['from' => 'USD', 'to' => 'RUB', 'amount' => 50];
  $newExcgange = new ExchangeRates($change);

  echo "Обмен успешно совершён! <br>";
  echo "Результат: $change[amount] $change[from] = <b>$newExcgange->result</b> $change[to] <br>";
  echo "По курсу 1 $change[from] = <b>$newExcgange->rates</b> $change[to] <br>";
} catch (Exception $err) {
  print_r($err->getMessage());
}