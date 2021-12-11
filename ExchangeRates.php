<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class ExchangeRates
{
  /**
   * @var string Наша пара из двух валют для конвертации
   */
  public $pair = '';
  /**
   * @var int Количество для конвертации валюты
   */
  public int $amount = 1;
  /**
   * @var array|void Список доступных пар курсов валют
   */
  public $listOfExchange = [];

  /**
   * @var mixed|string Пара валют для обмена
   */
  public $pairRates = '';
  /**
   * @var float Курс обмена
   */
  public float $rates = 1;

  /**
   * @var float|int Результат конвертация валют
   */
  public float $result = 0;
  private array $arr;


  /**
   * @param array $arr - Входной массив
   */
  public function __construct(array $arr)
  {
    try {


      $this->arr = $arr;
      $this->pair = $this->validateInput($this->arr);
      $this->listOfExchange = $this->getListOfExchange();
      $this->pairRates = $this->getPairRates();
      $this->rates = $this->pairRates[$this->pair];

      $make =  $this->makeChange($this->rates, $this->amount);

    } catch (Exception $err) {
      print_r($err->getMessage());
    }
    return $make;
  }

  /**
   * Получения списка доступных валют для конвертации.
   *
   * @return object
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getListOfExchange():object
  {
    try {
      $client = new GuzzleHttp\Client();
      $res = $client->request(
        'GET',
        'https://currate.ru/api/?get=currency_list&key=' . $_ENV['API_KEY'],
        ['connect_timeout' => 7]); //connect_timeout в секундах
      $this->checkConnect($res);
      $data = json_decode($res->getBody())->data;
    } catch (Exception $err) {
      print_r($err->getMessage());
    }
    return $data;
  }

  /**
   * Получаем курс валюты для текущей пары.
   *
   * @return mixed
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getPairRates():mixed
  {
    try {

      //Проверка на наличие такой пары валют в списке
      if (!in_array($this->pair,$this->listOfExchange)){
          throw new Exception('В базе нет доступного обмена для таких валют '.$this->pair);
      }


      $client = new GuzzleHttp\Client();
      $res = $client->request(
        'GET',
        'https://currate.ru/api/?get=rates&pairs=' . $this->pair . '&key=' . $_ENV['API_KEY'],
        ['connect_timeout' => 7]); //connect_timeout в секундах
      $this->checkConnect($res);
      $data = json_decode($res->getBody(), true)['data'];
    } catch (Exception $err) {
      print_r($err->getMessage());
    }
    return $data;
  }

  /**
   * Проверяем подключение к серверу API.
   *
   * @param $client
   * @return bool
   * @throws Exception
   */
  protected function checkConnect($client): bool
  {
    if ($client->getStatusCode() !== 200) {
      throw new Exception('Сервер не ответил. Код: ' . $client->getStatusCode());
    }
    $check = json_decode($client->getBody());
    if (intval($check->status) !== 200) { //я чёт не понял, почему возвращает статус в строке то в числе, поэтому конвертим. Странный апи
      throw new Exception(
        'Сервер вернул ошибку. Код: ' . $check->status .
        ' и сказал: ' . $check->message
      );
    }
    return true;
  }

  /**
   * Простой метод для проверки входных данных.
   *
   * @param array $arr
   * @return string
   * @throws Exception
   */
  protected function validateInput($arr): string
  {
    if (!$arr['from']) {
      throw new Exception('Неправильные входные данные. Нет валюты ИЗ которой происходит конвертация');
    }
    if (!$arr['to']) {
      throw new Exception('Неправильные входные данные. Нет валюты В которую происходит конвертация');
    }
    if (!$arr['amount']) {
      throw new Exception('Неправильные входные данные. Не указана сумма, которую вы хотите поменять.');
    }

    $this->amount = $arr['amount'];
    return $arr['from'] . $arr['to'];
  }

  /**
   * Делаем обмен валюты посредством сложного вычисления.
   *
   * @param $rate
   * @param $amount
   * @return float|int
   */
  protected function makeChange($rate, $amount):float|int
  {
    $this->result = $rate * $amount;
    return $this->result;
  }

}