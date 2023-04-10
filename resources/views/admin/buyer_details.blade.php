@extends('layout.app')
@section('content')

    <form action="{{ route('buyer_details') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12">
        <div class="row">
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="Buyer Name" type="text"
                    class="form-control form-control" value="{{ isset($data['fullname']) ? $data['fullname'] : "" }}"  name="fullname"></div>
         
           
            {{-- <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control flatpickr-input"
                    placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                    ></div> --}}
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <select class="form-select" name="type" id="is_verify" aria-label="Default select example">
                        <option value="">Select Type From List</option>
                      
                        @foreach ($type as $types)
                             
                        <option {{ isset($data['type']) &&  $data['type'] ==$types ? "selected" : ""}} value="{{ $types}}">{{$types}}</option>
                        
                         @endforeach
        
                      </select>
                   </div>    
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select" name="is_verify" id="status" aria-label="Default select example">
                <option value="">Select Verification From List</option>
              
                <option {{ isset($data['is_verify']) && ($data['is_verify']==1)  ? "selected" : "" }} value="1">verified</option>
                <option {{ isset($data['is_verify']) && ($data['is_verify']==0)  ? "selected" : "" }} value="0">unverified</option>


              </select>
           </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3"><select class="form-select" name="status" id="status" aria-label="Default select example">
                      <option selected value="">Select Status From List</option>
                       @foreach ($system_status as $system)
                     
                      <option {{ isset($data['status']) &&  $data['status'] ==$system->value? "selected" : ""}} value="{{ $system->value }}">{{$system->name }}</option>
                      
                       @endforeach
                    </select></div>
            <div class="justify-content-md-start  col-md-4 col-lg-4">
                <div class="row">
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      {{-- <input type="button" class="btn btn-primary " value="Search"> --}}
                     <a href="{{ route('buyer_details') }}">
                      <button type="submit" class="btn btn-primary btn-sm  search"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('buyer_details') }}">
                        <button type="button" class="btn btn-secondary btn-sm " style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table_responsive">
      <table id="datatable" class="table table-striped" style="100%;">
        <thead>
            <tr><th>Serial No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Type</th>
                <th>Date</th>
                <th>View</th>
                <th>Updated By</th>
                <th>Status</th>
                <th>Verified</th>
               
            </tr>
        </thead>
        <tbody>
       
            @foreach ($buyers  as $buyer)
            @php
            $status_class = '';
            if($buyer->systemstatus->name == 'inactive'){
              $status_class = 'bg-info';
            }elseif($buyer->systemstatus->name == 'active'){
              $status_class = 'bg-success';
            }
            
            @endphp
           
            <tr>
                <td>
                  <a data-bs-toggle="modal" data-bs-target="#exampleModal" href="javascript:void(0)" id="{{$buyer->buyer_id}}" onclick="showRowData(this)">  {{ $buyer->buyer_id}}</a>
                </td>
                <td>{{ $buyer->fullname}}</td>
                <td>{{ $buyer->email  }}</td>
                <td>{{$buyer->mobile}} </td>
                <td>{{$buyer->type}} </td>
                <td> {{$buyer->created_at}} </td>
                <td><a href="{{ url('buyer_dashboard/'.$buyer->buyer_id) }}"><i class="fa fa-eye"></i></a></td>
                 @if ( $buyer->updated_by != null &&  $buyer->updated_by != '')
               <td>{{ $buyer->updatedByPerson->fullname }} {{$buyer->updated_at}}</td>
                   @else
                   <td></td>
               @endif
                <td><span class="badge {{ $status_class }}">{{ $buyer->systemstatus->name}}</span></td>
                           
              <td id="check_icon-{{$buyer->buyer_id}}" style="text-align: center">
                @if ($buyer->is_verified == 1)
                <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">

                    @else
                    <button type="button" id="btn-{{$buyer->buyer_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$buyer->buyer_id}}"  class=" btn btn-outline-primary verified_buttion">Verify</button>

                @endif
            </td>
                {{-- <td><label class="switch">
                    <input type="checkbox" value="{{ $buyers->is_verified }}" id="rowcheck-{{$buyers->buyer_id}}"  onchange="checkboxChange(this)" {{ $buyers->is_verified==1 ? 'checked' : '' }} class="getvalue">
                    <span class="slider round"></span>
                  </label>
                </td> --}}
               
            </tr>
            @endforeach
        </tbody>
      
    </table>
    </div>
  

    @include('admin/modals/buyerModal')
  </main>
 <!-- End #main -->
 @include('admin/modals/rechargeModal')

<script>
   $('td').delegate('.verified_buttion', 'click',function (e){
                e.preventDefault();
               var buyer_id = $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+buyer_id);
            
              $('#deletestudentmodal').modal('show');
   }); 
  function Buyer_verified(data){
        var buyer_id = data.id;
        if($(this).hasClass('statechange')){
            buyer_id=buyer_id.replace('modalcheck-','');
            // alert( buyer_id);
        }else{
            buyer_id=buyer_id.replace('rowcheck-','');
        }
        $('#deletestudentmodal').modal('hide');

        // alert(listing_id);
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('buyer_verified.update') }}",
		        type: "POST",
		        data: {"id": buyer_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                       
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var buyer= response.buyer;
                          if(buyer.is_verified == 1){
                            alert('Verified Buyer');
                            $('.modal-backdrop').remove();
                            $(`#btn-${buyer_id}`).hide();       
                    
                            $('#check_icon-'+buyer_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_model-'+buyer_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            // $('#rowcheck-'+buyer_id).prop('checked',true);
                          }else{

                            
                          }
                            
                        }
                    }
                }
        });
}
function showRowData(data){
  // console.log(data);
  var buyer_id = data.id;
  $.when(callAjaxFunction("{{ route('buyer_details')}}"+"/"+buyer_id,"GET","")).then(function(response) {
    var buyer = response.buyer;
    $(".toggleClass").empty();
      $('#single_record tbody').html("");
      $('.modal-title').text('Serial No: '+buyer.buyer_id);
      $('.seller').text('Buyer: '+buyer.fullname);
      $('.selermobile').text('Mobile: '+buyer.mobile);
      $('.seleremail').text('Email: '+buyer.email);
      $('.otp').text('OTP: '+buyer.current_otp);
      $('.amount').text(buyer.wallet_amount);
      $('.type').text(buyer.type);

      $('.dprogress').text(buyer.deal_in_progress);
      $('.dcompleted').text(buyer.deal_completed);
      if (buyer.systemstatus.name == 'inactive') {
        var status_class = 'bg-info';
      }else if (buyer.systemstatus.name == 'active') {
        var status_class = 'bg-success';
      }
      
      $('.status').addClass(status_class);                   
      $('.status').text(buyer.systemstatus.name);
      $('.set_id').attr('id',buyer.buyer_id); 
      $('.toggleClass').attr('id','icon_model-'+buyer.buyer_id); 
      $('.statechange').attr('id',buyer.buyer_id);
      if(buyer.is_verified == 0){
        $('#icon_model-'+buyer.buyer_id).append('<button type="button" id="btn-'+buyer.buyer_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+buyer.buyer_id+'"  class=" btn btn-outline-primary verified_buttion">Verify</button>');
      }
      else{
        $('#icon_model-'+buyer.buyer_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
      }
  });
}   
</script>
@stop