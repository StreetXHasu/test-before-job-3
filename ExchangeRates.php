<?php

class ExchangeRates
{
  public $status = 0;
  public $change = [];

  public function __construct($arr)
  {
    $this->connect();
    $this->change = $arr;
  }

  protected function connect()
  {
    if (true) {
      echo 'подключение ок';
    }
  }

  protected function makeChange()
  {
    if (true) {
      echo 'подключение ок';
    }
  }

}