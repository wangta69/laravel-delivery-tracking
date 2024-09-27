<?php
namespace Pondol\DeliveryTracking\Couriers;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class EPOST
{

  /**
  * @param String $flag : write, read
  */
  public function tracking($invoicenumber) {
      ## first check bbs_role
    $url = 'https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1='.$invoicenumber;

    $client = new Client();
    $crawler = new Crawler();

    $response = $client->request('GET', $url);


    $html = $response->getBody()->getContents(); 



    $html = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $html );

    $crawler->addHTMLContent($html, 'UTF-8');

    $logs = $crawler->filter('table#processTable')->filter('tr')->each(function ($tr, $i) {
      return $tr->filter('td')->each(function ($td, $i) {
        return trim($td->text());
      });
    });

    array_shift($logs);
    if(!count($logs)) {
      return ['error'=>'numberValidaionErrorOrnodata'];
    }

    return ['error'=>false, 'status'=>$this->status(end($logs)[3]), 'logs'=>$this->logs($logs)];
  }

  // 배송단계를 전체를 통일
  private function status($status) {
    $explode = explode(' ', $status);
    switch(trim($explode[0])) {
      // case '':
      //   return '상품준비';
      case '접수':
        return '상품인수';
      case '발송':
      case '도착':
        return '상품이동중';
      case '배달준비':
        return '배송출발';
      case '배달완료':
        return '배송완료';
      default:
      return substr(trim($status), 2);
    }
  }

  private function logs($logs) {
    $data = [];
    foreach($logs as $k => $log) {
      $data[$k] = [];

      $data[$k]['time'] = $log[0].' '.$log[1];
      $data[$k]['location'] = $log[2];

      $explode = explode(' ', $log[3]);
      $data[$k]['status'] = $explode[0];

      $data[$k]['statusmsg'] = '';

      $data[$k]['deliveryPerson'] = '';
      $data[$k]['contact'] = '';
    }
    return $data;
  }

}