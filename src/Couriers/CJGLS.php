<?php
namespace Pondol\DeliveryTracking\Couriers;


use GuzzleHttp\Client;

class CJGLS
{

  /**
  * @param String $flag : write, read
  */
  public function tracking($invoicenumber) {


    $url = 'https://www.cjlogistics.com/ko/tool/parcel/tracking';
    $client = new Client([
      'base_uri' => 'https://www.cjlogistics.com',
      'cookie' => true,
    ]);
    $cookieJar = new \GuzzleHttp\Cookie\CookieJar();

    $response = $client->get($url, [
      'cookie' => true,
      'cookies' => $cookieJar
    ]);
    preg_match('/<input type="hidden" title="인증키" name="_csrf" value="(.*)"/Uis',$response->getBody()->getContents(), $_csrf);

    // 01 상품준비 > 02 집화출발 > 03 상품인수 > 04 상품이동중 > 05 배송지도착 > 06 배송출발 > 07 배달완료 
    // $cookieJar = new \GuzzleHttp\Cookie\CookieJar();

      ## first check bbs_role
    $url = 'https://www.cjlogistics.com/ko/tool/parcel/tracking-detail';

    $client = new Client();
    // $crawler = new Crawler();
    $form_params= [
      'paramInvcNo'=> $invoicenumber,
      '_csrf'=>$_csrf[1]
    ];

    $headers =  [
      'Accept' => 'application/json, text/javascript, */*; q=0.01',
      'Accept-Encoding' => 'gzip, deflate, br, zstd',
      'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
      'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0',
      'origin' => 'https://www.cjlogistics.com',
      'referer' => 'https://www.cjlogistics.com/ko/tool/parcel/tracking',
    ];

    $response = $client->request('POST', $url, [
      'form_params' => $form_params,
      'headers' => $headers,
      'cookies' => $cookieJar,
    ]);

    $res = json_decode($response->getBody(), true);


    $logs = $res['parcelDetailResultMap']['resultList'];

    if(!$logs) {
      return ['error'=>'numberValidaionError'];
    }

    return ['error'=>false, 'status'=>$this->status(end($logs)['scanNm']), 'logs'=>$this->logs($logs)];
  }


    // 배송단계를 전체를 통일
    private function status($status) {
      switch($status) {
        case '집화처리':
          return '상품인수';
        case 'SM입고':
        case '간선상차':
        case '간선하차':
          return '상품이동중';
        case '배송출발':
          return '배송출발';
        case '배송완료':
          return '배송완료';
        default:
        return trim($status);
      }
    }

    private function logs($logs) {
      $data = [];
      foreach($logs as $k => $log) {
        $data[$k] = [];

        $data[$k]['time'] = $log['dTime'];
        $data[$k]['location'] = $log['regBranNm'];
        $data[$k]['status'] = $log['scanNm'];
        
        $crgNm = preg_split("/\([^()]+\)/", $log['crgNm']);
        preg_match_all('#\((.*?)\)#', $log['crgNm'], $info);

        if (count($info[1])) {
          $deliveryInfo = count($info[1]) === 2 ? $info[1][1] : $info[1][0];
          $result = preg_split('/(:|\s)/', $deliveryInfo);
          $data[$k]['deliveryPerson'] = $result[1];
          $data[$k]['contact'] = $result[2];
        } else {
          $data[$k]['deliveryPerson'] = '';
          $data[$k]['contact'] = '';
        }
        $data[$k]['statusmsg'] = $crgNm[0];
      }
      return $data;
    }

}