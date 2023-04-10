@extends('layout.app')
@section('content')
    <form action="{{ route('paymentList') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12">
        <div class="row">
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="Seller Name" type="text"
                    class="form-control form-control-sm" value="{{ isset($data['fullname']) ? $data['fullname'] : "" }}"  name="fullname">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <input type="date" class="form-control form-control-sm" value="{{ isset($data['date']) ? $data['date'] : "" }}"  name="date">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control form-control-sm"
                    placeholder="Order No" id="order_no" name="order_no" value="{{ isset($data['order_no']) ? $data['order_no'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                   ></div>
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control form-control-sm"
                    placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                    ></div>
            <div class="mb-1 col-6 col-sm-6 col-md-3"> 
              <select class="form-select form-select-sm" name="is_verify" id="is_verify" aria-label="Default select example">
              <option value="">Select Verification From List</option>
            
              <option {{ isset($data['is_verify']) && ($data['is_verify']==1) ? "selected" : "" }} value="1">Verify</option>
              <option {{ isset($data['is_verify']) && ($data['is_verify']==0) ? "selected" : "" }} value="0">Not Verify</option>


            </select>
          </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3"><select class="form-select form-select-sm" name="status" id="status" aria-label="Default select example">
                      <option selected value="">Select Status From List</option>
                       @foreach ($system_status as $system)
                     
                      <option {{ isset($data['status']) &&  $data['status'] ==$system->value? "selected" : ""}} value="{{ $system->value }}">{{$system->name }}</option>
                      
                       @endforeach
                    </select></div>
            <div class="justify-content-md-start  col-md-4 col-lg-4">
                <div class="row">
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      {{-- <input type="button" class="btn btn-primary " value="Search"> --}}
                     <a href="{{ route('paymentList') }}">
                      <button type="submit" class="btn btn-primary btn-sm  search"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('paymentList') }}">
                        <button type="button" class="btn btn-secondary btn-sm " style="width: max-content;">Reset</button>
                      </a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table_responsive">
      <table id="datatable" class="table table-striped" style="width:100%; overflow-x:auto;">
        <thead>
          <tr>
            <th>Payment NO</th>
            <th>Seller Name</th>
            <th>Material</th>
            <th>Order No</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Updated By</th>
            <th>Total Price</th>
            <th>Status</th>
            {{-- <th>Verified</th> --}}
           
           
        </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
          
            @php
            $status_class = '';
            if($payment->systemstatus->name == 'in progress'){
              $status_class = 'bg-info';
            }elseif($payment->systemstatus->name == 'pending'){
              $status_class = 'bg-warning';
            }elseif($payment->systemstatus->name == 'rejected'){
              $status_class = 'bg-danger';
            }
            elseif($payment->systemstatus->name == 'accepted'){
              $status_class = 'bg-success';
            }  elseif($payment->systemstatus->name == 'completed'){
              $status_class = 'bg-success';
            }
         
            @endphp
           
            <tr>
              <td >
              <a data-bs-toggle="modal" data-bs-target="#paymentModal" href="javascript:void(0)" id="{{$payment->pay_id}}" onclick="showpayment(this)">  {{ $payment->pay_no }}</a>
              </td>
              <td>{{ $payment->order->offer->seller->fullname }}</td>
                <td>{{ $payment->order->material->name}}</td>
                <td>{{ $payment->order->order_no}}</td>
                <td>{{ $payment->order->quantity." (".$payment->order->quantity_unit.")"  }}</td>
                <td>{{$payment->created_at}}</td>
                @if ( $payment->updated_by != null &&  $payment->updated_by != '')
                <td>{{ $payment->updatedByPerson->fullname }} {{$payment->updated_at}}</td>
                    @else
                    <td></td>
                @endif
                <td>{{ $payment->order->total_price}} SAR</td>
                <td><span class="badge {{ $status_class }}">{{ $payment->systemstatus->name}}</span></td>
                {{-- <td id="payment_icon-{{$payment->pay_id}}" style="text-align: center;">
                  @if (  $payment->is_verified  ==1)
                  <img src="{{ url('/') }}/assets/img/shield-tick.png" alt="" style="height: 20px">
                      @else
                      <button type="button" id="btn-{{$payment->pay_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$payment->pay_id}}"  class=" btn btn-outline-primary verified_payment">Verify</button>
                  @endif
                </td> --}}
               
            </tr>
            @endforeach
        </tbody> 
      
    </table>
    </div>
   
    @include('admin/modals/paymentModal')




<script>



$('td').delegate('.verified_payment', 'click',function (e){
                e.preventDefault();
               var pay_id= $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+pay_id);
              
               $('#paymentconfirmation').modal('show');
            });
  function paymentStatus(data){
    var pay_id = data.id;
  
        if($(this).hasClass('statechange')){
          pay_id=pay_id.replace('modalcheck-','');
        }else{
          pay_id=pay_id.replace('rowcheck-','');
        }
        $('#paymentconfirmation').modal('hide');

        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('paymentstatus.update') }}",
		        type: "POST",
		        data: {"id": pay_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                        
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var payment= response.payment;
                          if(payment.is_verified == 1){
                            alert('Verified Payment');
                       
                            $(`#btn-${pay_id}`).hide();
                          
                            $('#payment_icon-'+pay_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_payment-'+pay_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            // $('#rowcheck-'+order_id).prop('checked',true);
                          }else{
                            // $('#rowcheck-'+order_id).prop('checked',false);
                          }
                            
                        }
                    }
                }
        });
  } 
</script>
@stop