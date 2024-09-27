<?php
namespace Pondol\DeliveryTracking\Traits;

trait Tracking  {

  public function _tracking($courier, $invoicenumber)
  {
    $svcClass = "\\Pondol\\DeliveryTracking\\Couriers\\".$courier;
    $svc = new $svcClass;
    $result =  $svc->tracking($invoicenumber);

    if ($result['error']) {
      $result['status'] = null;
      $result['logs'] = [];
    }

    return $result;
  }

  public function _couriers() {
    return config('courier.courier');
  }

  public function _courier($courier) {
    $couriers = $this->_couriers();
    // print_r($couriers);
    if(isset($couriers[$courier])) {
      return $couriers[$courier];
    } else return false;
  }

}
