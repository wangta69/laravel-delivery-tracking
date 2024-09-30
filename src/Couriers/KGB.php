<?php
namespace Pondol\DeliveryTracking\Couriers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class KGB
{

  /**
  * @param String $flag : write, read
  */
  public function tracking($invoicenumber) {
    $url = 'https://www.ilogen.com/web/personal/trace/'.$invoicenumber;

    $client = new Client();
    $crawler = new Crawler();
    $response = $client->request('GET', $url);
    $html = $response->getBody();
    $crawler->addHTMLContent($html, 'UTF-8');


    $error = strpos($crawler->text(), '운송장번호를 확인해주세요');
    // echo 'error:'.$error;
    if ($error) {
      return ['error'=>'numberValidaionError'];
    }

    $status = $crawler->filter('ul.tkStep')->filter('li.on')->each(function ($li, $i) {
      return trim($li->text());
    });

    // 0=>날짜, 1=>사업장, 2=>배송상태 3=>배송내용 4=>담당직원 5,=>인수자 6=>영업소 7=>연락처
    $logs = $crawler->filter('table.tkInfo > tbody')->filter('tr')->each(function ($tr, $i) {
      return $tr->filter('td')->each(function ($td, $i) {
        return trim($td->text());
      });
    });

    return ['error'=>false, 'status'=>$this->status($status[0]), 'logs'=>$this->logs($logs)];
  }

  // 배송단계를 전체를 통일
  private function status($status) {
    switch($status) {
      case '01방문예정':
        return '상품준비';
      case '02물품수거':
        return '상품인수';
      case '03이동중':
        return '상품이동중';
      case '04배송중':
        return '배송출발';
      case '05배송완료':
        return '배송완료';
      default:
      return substr(trim($status), 2);
    }
  }

  private function logs($logs) {
    $data = [];
    foreach($logs as $k => $log) {
      $data[$k] = [];

      $data[$k]['time'] = $log[0];
      $data[$k]['location'] = $log[1];
      $data[$k]['status'] = $log[2];
      $data[$k]['statusmsg'] = $log[3];
      if($log[6]) {
        $log_6 = explode('◆', $log[6]);
        $data[$k]['deliveryPerson'] = $log_6[1];
      } else {
        $data[$k]['deliveryPerson'] = '';
      }
      $data[$k]['contact'] = $log[7];
    }
    return $data;
  }

}