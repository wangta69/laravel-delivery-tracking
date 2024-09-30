<?php
namespace Pondol\DeliveryTracking\Couriers;


use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class HANJIN
{

  /**
  * @param String $flag : write, read
  */
  public function tracking($invoicenumber) {
      ## first check bbs_role
    $url = 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillResult.do';

    $client = new Client();
    $crawler = new Crawler();

    $form_params= [
      'wblnum'=> $invoicenumber,
      'mCode'=> "MN038",
      'schLang'=> "KR",
      'referer' => 'https://www.hanjin.com/kor/CMS/DeliveryMgr/WaybillSch.do?mCode=MN038',
    ];

    $headers =  [
      'Content-Type' => 'application/x-www-form-urlencoded',
      'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0',
    ];

    $response = $client->request('POST', $url, [
      'form_params' => $form_params,
      'headers' => $headers
    ]);


    $html = $response->getBody()->getContents();

    $crawler->addHTMLContent($html, 'UTF-8');

    $error = strpos($crawler->text(), '운송장이 등록되지 않았거나');
    // echo 'error:'.$error;
    if ($error) {
      return ['error'=>'numberValidaionError'];
    }



    // 01방문예정, 02물품수거, 03이동중, 04배송중, 05배송완료
    $status = $crawler->filter('div.delivery-step')->filter('li.on')->each(function ($li, $i) {
      return trim($li->text());
    });

    // 0=>날짜, 1=>사업장, 2=>배송상태 3=>배송내용 4=>담당직원 5,=>인수자 6=>영업소 7=>연락처
    $logs = $crawler->filter('.waybill-tbl table.board-list-table > tbody')->filter('tr')->each(function ($tr, $i) {
      return $tr->filter('td')->each(function ($td, $i) {
        return trim($td->text());
      });
    });

    return ['error'=>false, 'status'=>$this->status($status[0]), 'logs'=>$this->logs($logs)];
    
  }

  // 배송단계를 전체를 통일
  private function status($status) {
    switch($status) {
      case 'STEP상품접수':
        return '상품인수';
      case 'STEP2터미널입고':
        return '상품이동중';
      case 'STEP3상품이동중':
        return '상품이동중';
      case 'STEP4배송터미널도착':
        return '상품이동중';
      case 'STEP5배송출발':
        return '배송출발';
      case 'STEP6배송완료':
        return '배송완료';
      default:
      return substr(trim($status), 5);
    }
  }

  private function logs($logs) {
    $data = [];
    foreach($logs as $k => $log) {
      $data[$k] = [];

      $data[$k]['time'] = $log[0].' '.$log[1];
      $data[$k]['location'] = $log[2];
      $data[$k]['status'] = '';

      $statusmsg = preg_split("/\([^()]+\)/", $log[3]);
      $data[$k]['statusmsg'] = $statusmsg[0];
      preg_match_all('#\((.*?)\)#', $log[3], $info);
      
      if(count($info[1])) {
        $result = preg_split('/(\s:\s|\s)/', $info[1][0]);

        $data[$k]['deliveryPerson'] = trim($result[1]);
        $data[$k]['contact'] = trim($result[2]);
      } else {
        $data[$k]['deliveryPerson'] = '';
        $data[$k]['contact'] = '';
      }
    }
    return $data;
  }

}