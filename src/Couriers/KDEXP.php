<?php
namespace Pondol\DeliveryTracking\Couriers;


use GuzzleHttp\Client;

class KDEXP
{

  /**
  * @param String $flag : write, read
  */
  public function tracking($invoicenumber) {
    $url = 'https://kdexp.com/service/delivery/ajax_basic.do?barcode='.$invoicenumber;

    $client = new Client();

    $response = $client->request('GET', $url);
    $res = json_decode($response->getBody(), true);
    if(isset($res['result']) && $res['result'] == 'suc') {
      return ['error'=>false, 'status'=>$this->status(end($res['items'])['sc_stat']), 'logs'=>$this->logs($res['items'])];
    } else {
      return ['error'=>'numberValidaionError'];
    }

  }

  // 배송단계를 전체를 통일
  private function status($status) {
    switch($status) {
      case '접수완료':
        return '상품인수';
      case '영업소집하':
      case '터미널입고':
      case '영업소도착':
        return '상품이동중';
      case '배달차량상차':
        return '배송출발';
      case '배송완료':
        return '배송완료';
      default:
      return substr(trim($status), 5);
    }
  }

  private function logs($logs) {
    $data = [];
    foreach($logs as $k => $log) {
      $data[$k] = [];
      $data[$k]['time'] = $log['reg_date'];
      $data[$k]['location'] = $log['location'];
      $data[$k]['status'] = $log['stat'];
      $data[$k]['statusmsg'] = $log['sc_stat'];
      $data[$k]['deliveryPerson'] = $log['sc_nm'];
      $data[$k]['contact'] = $log['tel'];
    }
    return $data;
  }

}