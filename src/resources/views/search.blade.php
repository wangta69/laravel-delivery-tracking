@extends('tracking::app')
@section('title', 'HOME')

@section('content')

<div class="container-fluid">


  <div class="card">
    <div class="card-header">
      <span>배송조회</span>
    </div>
    <div class="card-body">
    <form name="delivery-form">

      <div class="input-group mt-1">
          <label class="col-sm-3 col-md-2">배송업체</label>

            <select name="courier" class="form-select">
              <option value="">선택</option>
              @foreach($couriers as $k=>$c)
              <option value="{{$k}}">{{$c['name']}}</option>
              @endforeach
            </select>

      </div>
      <div class="input-group mt-1">
        <label class="col-sm-3 col-md-2">타입</label>
        <select name="type" class="form-select">
              <option value="json">json</option>
              <option value="html" selected>html</option>
            </select>
      </div>
      <div class="input-group mt-1">
        <label class="col-sm-3 col-md-2">송장번호</label>
        <input type="text" name="invoice_no" value="" class="form-control">
        <button type="button" class="btn btn-danger act-tracking">추적하기</button>
      </div>
    </form>
  </div><!-- .card-body -->

  </div>



</div>
@endsection

@section ('styles')
@parent
@endsection

@section ('scripts')
@parent
<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>
<script>
$(function(){
  $('.act-tracking').on('click', function(){
    var courier = $("select[name=courier] > option:selected").val();
    var invoice_no =  $("input[name=invoice_no]").val();
    var type = $("select[name=type] > option:selected").val();
    var url = `/delivery-tracking/${courier}/${invoice_no}/${type}`;
    window.open(url, '', 'width=900, height=600');
  })
})
</script>
@endsection
