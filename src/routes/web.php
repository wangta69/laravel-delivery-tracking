<?php
Route::group(['prefix' => 'delivery-tracking', 'as' => 'delivery.tracking.', 'namespace' => 'Pondol\DeliveryTracking\Http\Controllers', 'middleware' => ['web']], function () {
  Route::get('', array('uses'=>'DeliveryTrackingController@search'));
  Route::get('couriers', array('uses'=>'DeliveryTrackingController@couriers'));
  Route::get('{courier}/{invoicenumber}/{type?}', array('uses'=>'DeliveryTrackingController@tracking'));
});
