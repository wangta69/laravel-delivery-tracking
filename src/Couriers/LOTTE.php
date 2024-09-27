<?php
namespace Pondol\DeliveryTracking\Couriers;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class LOTTE
{

  /**
  * @param String $flag : write, read
  */
  public function tracking($invoicenumber) {


    $url = 'https://www.lotteglogis.com/mobile/reservation/tracking/linkView';
    $client = new Client();
    $crawler = new Crawler();
    $form_params= [
      'InvNo'=> $invoicenumber,
    ];


    $headers =  [
      'Accept' => 'application/json, text/javascript, */*; q=0.01',
      'Accept-Encoding' => 'gzip, deflate, br, zstd',
      'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
      'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0',
      // 'origin' => 'https://www.cjlogistics.com',
      // 'referer' => 'https://www.cjlogistics.com/ko/tool/parcel/tracking',
    ];

    $response = $client->request('POST', $url, [
      'form_params' => $form_params,
      'headers' => $headers,
      // 'cookies' => $cookieJar,
    ]);

    $html = $response->getBody()->getContents();
    // print_r($html);
    $crawler->addHTMLContent($html, 'UTF-8');

    $logs = $crawler->filter('div.scroll_date_table table')->filter('tr')->each(function ($tr, $i) {
      return $tr->filter('td')->each(function ($td, $i) {
        return trim($td->text());
      });
    });
    array_shift($logs);
    return ['error'=>false, 'status'=>$this->status($logs[0][0]), 'logs'=>$this->logs($logs)];
  }


    // 배송단계를 전체를 통일
    private function status($status) {
      switch($status) {
        case '인수/상품접수':
          return '상품인수';
        case '상품 이동중':
          return '상품이동중';
        case '배송 출발':
          return '배송출발';
        case '배달 완료':
          return '배송완료';
        default:
        return trim($status);
      }
    }

    private function logs($logs) {
      $data = [];
      foreach($logs as $k => $log) {
        $data[$k] = [];

        $data[$k]['time'] = $log[1];
        $data[$k]['location'] = $log[2];
        $data[$k]['status'] = $log[0];
        
        $statusmsg = preg_split("/\([^()]+\)/", $log[3]);
        $data[$k]['statusmsg'] = $statusmsg[0];

        preg_match_all('#\((.*?)\)#', $log[3], $info);
        
        if (count($info[1])) {
          // $deliveryInfo = count($info[1]) === 2 ? $info[1][1] : $info[1][0];
          $result = preg_split('/(:|\s)/', $info[1][0]);
          $data[$k]['deliveryPerson'] = $result[2];
          $data[$k]['contact'] = $result[3];
        }
        
      }
      return $data;
    }

}