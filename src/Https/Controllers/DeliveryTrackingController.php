<?php
namespace Pondol\DeliveryTracking\Https\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Validator;
use Response;

use Pondol\DeliveryTracking\Traits\Tracking;

class DeliveryTrackingController  { //  extends Controller

  use Tracking;

  public function __construct() {
  }


/*
    * Tracking delivery
    *
    * @param String $courier 택배사 코드
    * @param  String invoicenumber  송장번호
    */
  public function tracking(Request $request, $courier, $invoicenumber, $type='json')
  {
    $result = $this->_tracking($courier, $invoicenumber);

    switch($type) {
      case 'html':
        
        return view('tracking::show', [
          'result'=>$result,
          'invoicenumber' => $invoicenumber,
          'courier' => $this->_courier($courier)
        ]);
      default:
        return response()->json($result, 200);//500, 203
    }
  }

  public function couriers() {
    return $this->_couriers();
  }

}
