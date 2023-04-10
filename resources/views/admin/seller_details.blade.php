
 
@extends('layout.app')
@section('content')

  <form action="{{ route('seller_details') }}" method="GET">
    {{-- @csrf --}}
    <div class="col-md-12 col-lg-12">
      <div class="row">
          <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="seller Name" type="text"
                  class="form-control form-control-sm" value="{{ isset($data['fullname']) ? $data['fullname'] : "" }}"  name="fullname"></div>
       
          <div class="mb-1 col-6 col-sm-6 col-md-3"><input type="date"
            class="form-control form-control-sm" value="{{ isset($data['date']) ? $data['date'] : "" }}"  name="date"></div>
          {{-- <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control flatpickr-input"
                  placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                  ></div> --}}
          <div class="mb-1 col-6 col-sm-6 col-md-3">
            <select class="form-select form-select-sm" name="type" id="is_verify" aria-label="Default select example">
              <option value="">Select Type From List</option>
            
              @foreach ($type as $types)
                   
              <option {{ isset($data['type']) &&  $data['type'] ==$types ? "selected" : ""}} value="{{ $types}}">{{$types}}</option>
              
               @endforeach

            </select>
          </div>
          <div class="mb-1 col-6 col-sm-6 col-md-3">
            <select class="form-select form-select-sm" name="is_verify" id="is_verify" aria-label="Default select example">
              <option value="">Select Verification From List</option>
            
              <option {{ isset($data['is_verify']) && ($data['is_verify']==1) ? "selected" : "" }} value="1">verified</option>
              <option {{ isset($data['is_verify']) && ($data['is_verify']==0) ? "selected" : "" }} value="0">unverified</option>


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
                   <a href="{{ route('seller_details') }}">
                    <button type="submit" class="btn btn-primary btn-sm  search"
                    style="width: max-content;"><span>Search</span>
                  
              </button>
                   </a>
                  </form>
                  </div>
                  <div class="mb-1 col-6 col-sm-6 col-md-3">
                    <a href="{{ route('seller_details') }}">
                      <button type="button" class="btn btn-secondary btn-sm" style="width: max-content;">Reset</button>
                    </a>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="table_responsive">
    <table id="datatable"  class="table table-striped">
      <thead>
          <tr><th>Serial No</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Type</th>
              <th>Date</th>
              <th>Updated By</th>
              <th>View</th>
              <th>Status</th>
              <th>Verified</th>
              
             
          </tr>
      </thead>
      <tbody>
     
          @foreach ($sellers  as $seller)
          @php
          $status_class = '';
          if($seller->systemstatus->name == 'active'){
            $status_class = 'bg-success';
          
          }elseif($seller->systemstatus->name == 'inactive'){
            $status_class = 'bg-info';
          }
         
          @endphp
         
          <tr>
              <td>
                <a data-bs-toggle="modal" data-bs-target="#exampleModal" href="javascript:void(0)" id="{{$seller->seller_id}}" onclick="showsellerData(this)">  {{ $seller->seller_id}}</a>
              </td>
              <td>{{ $seller->fullname}}</td>
              <td>{{ $seller->email  }}</td>
              <td>{{$seller->mobile}} </td>
              <td>{{$seller->type}} </td>
              <td>{{ $seller->created_at }}</td>
              @if ( $seller->updated_by != null &&  $seller->updated_by != '')
              <td>{{ $seller->updatedByPerson->fullname }} {{ $seller->updated_at}}</td>
                  @else
                  <td></td>
              @endif
              <td><a href="{{ url('seller_dashboard/'.$seller->seller_id) }}"><i class="fa fa-eye"></i></a></td>
              <td><span class="badge {{ $status_class }}">{{ $seller->systemstatus->name}}</span></td>
          
                     
              <td id="seller_icon-{{$seller->seller_id}}" style="text-align: center">
                @if ($seller->is_verified == 1)
                <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
  
                    @else
                    <button type="button" id="sellerbtn-{{$seller->seller_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$seller->seller_id}}"  class=" btn btn-outline-primary verified_seller">Verify</button>
  
                @endif
            </td>
            
             
          </tr>
          @endforeach
      </tbody>
    
  </table>
  </div>
@include('admin/modals/sellermodal')
<!-- End #main -->
@include('admin/modals/rechargeModal')
{{-- <script>
  $(document).ready(function () {
      $('#datatable').DataTable({
        "order": [[6, "desc" ]], //or asc 
        "columnDefs" : [{"targets":3, "type":"date-eu"}],
    });    $('#datatable4').DataTable();
  });

</script> --}}
<script>
  $('td').delegate('.verified_seller', 'click',function (e){
               e.preventDefault();
              var student_id = $(this).val();
               $('.confirm_btn').attr('id','rowcheck-'+student_id);
           
             $('#deletestudentmodal').modal('show');
  }); 
 function seller_status(data){
 // console.log(data);
       var seller_id = data.id;
       if($(this).hasClass('statechange')){
         seller_id=seller_id.replace('modalcheck-','');
       }else{
         seller_id=seller_id.replace('rowcheck-','');
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
           url: "{{ route('seller_verified.update') }}",
           type: "POST",
           data: {"id": seller_id, "_token": "{{csrf_token()}}"},
           datatype: "json",
              
               success:function (response){
                   if (response.status == 200){
                      
                       if($(this).hasClass('statechange')){
        
                       }else{
                         var seller= response.seller;
                    

                         if(seller.is_verified == 1){
                           alert('Verified Seller');
                         
                           $('.modal-backdrop').remove();
                           $('#sellerbtn-'+seller_id).hide();       
                           $('#sellerbtn-'+seller_id).addClass('d-none');       
                         
                           $('#seller_icon-'+seller_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                           $('#icon_model-'+seller_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                           $('#rowcheck-'+seller_id).prop('check', true);
                         }else{
                           $('#rowcheck-'+seller_id).prop('checked',false);
                          

                         }
                           
                       }
                   }
               }
       });
}
 function showsellerData(data){
 // console.log(data);
   var seller_id = data.id;
   $.when(callAjaxFunction("{{ route('seller_details')}}"+"/"+seller_id,"GET","")).then(function(response) {
     var seller = response.sellers;
      
 //  $('td').removeAttr('id','icon_model-'+seller.seller_id);
     $(".toggleClass").empty();
     $(".status").removeClass("bg-success bg-info bg-warning bg-danger ");
     $('#single_record tbody').html("");
     $('.modal-title').text('Serial No: '+seller.seller_id);
     $('.seller').text('Seller: '+seller.fullname);
     $('.selermobile').text('Mobile: '+seller.mobile);
     $('.seleremail').text('Email: '+seller.email);
     $('.amount').text(seller.wallet_amount);
     $('.type').text(seller.type);
     $('.dprogress').text(seller.deal_in_progress);
     $('.dcompleted').text(seller.deal_completed);
     $('.otp').text("OTP:"+seller.current_otp);

     var status_class = '';

     if(seller.systemstatus.name == 'inactive'){ status_class = 'bg-info'; }
     else if (seller.systemstatus.name == 'active') { status_class = 'bg-success'; }
     
     $('.status').addClass(status_class);
     $('.toggleClass').attr('id','icon_model-'+seller.seller_id);
     $('.set_id').attr('id',seller.seller_id);
     $('.status').text(seller.systemstatus.name);
     $('.statechange').attr('id',seller.seller_id);
     if(seller.is_verified == 1){ $('#icon_model-'+seller.seller_id).append('<img class="verified_icon" src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">'); }
     else{ $('#icon_model-'+seller.seller_id).append('<button type="button" id="seller_btn-'+seller.seller_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+seller.seller_id+'"  class=" btn btn-outline-primary verified_seller">Verify</button>'); }
   });
}
</script>
@stop


 

