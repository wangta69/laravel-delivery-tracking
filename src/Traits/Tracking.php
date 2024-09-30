<?php
namespace Pondol\DeliveryTracking\Traits;
use Symfony\Component\Debug\Exception\FatalThrowableError;
trait Tracking  {

  public function _tracking($courier, $invoicenumber)
  {

    try {
      $svcClass = "\\Pondol\\DeliveryTracking\\Couriers\\".$courier;
      $svc = new $svcClass;
      $result =  $svc->tracking($invoicenumber);

      if ($result['error']) {
        $result['status'] = null;
        $result['logs'] = [];
      }

      return $result;
    // } catch (\Throwable $caught) {
    //   report($caught); 
    //   echo "===================================";
    //   $result['status'] = null;
    //   $result['logs'] = [];
    //   return $result;
    // }
  } catch (Error  $e) {
    // report($e); 
    // echo "===================================";
    $result['error'] = $e->getMessage();
    $result['status'] = null;
    $result['logs'] = [];
    return $result;
  // } catch (FatalThrowableError $e) {
  //     // report($e); 
  //     echo "===================================";
  //     $result['status'] = null;
  //     $result['logs'] = [];
  //     return $result;
  //   }
    } catch (\Throwable $caught) {
      $result['error'] = $caught->getMessage();
      $result['status'] = null;
      $result['logs'] = [];
      return $result;
    }
  }

  public function _couriers() {
    return config('courier.courier');
  }

  public function _courier($courier) {
    $couriers = $this->_couriers();
    // print_r($couriers);
    if(isset($couriers[$courier])) {
      return $couriers[$courier];
    } else {
      return false;
      // return ['name'=>'', 'url'=>'', 'query_url'=>''];
    }
  }

}
