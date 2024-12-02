function delivery_logs(courier, invoicenumber) {
  if(!invoicenumber) {
    return alert('물품 대기 중입니다.');
  }
  var url = `/delivery-tracking/${courier}/${invoicenumber}/html`;
  window_open(url, '', {width: 900, height: 600});
}