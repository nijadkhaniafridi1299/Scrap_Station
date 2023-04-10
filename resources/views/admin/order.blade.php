

  <style>
    /* body{margin-top:20px;
    width: 100%
    } */
.timeline {
    border-left: 3px solid #727cf5;
    border-bottom-right-radius: 4px;
    border-top-right-radius: 4px;
    background: rgba(114, 124, 245, 0.09);
    margin: 0 45%;
    /* letter-spacing: 0.px; */
    position: relative;
    /* line-height: .4em; */
    font-size: 1.03em;
    padding:50px;
    list-style: none;
    /* text-align: left; */
    /* max-width: 70%; */
  /* width: 100%;     */
}





.timeline h2,
.timeline h3 {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 10px;
}

.timeline .event {
    border-bottom: 1px dashed #e8ebf1;
    padding-bottom: 25px;
    margin-bottom: 25px;
    position: relative;
}



.timeline .event:last-of-type {
    padding-bottom: 0;
    margin-bottom: 0;
    /* border: none; */
}

.timeline .event:before,
.timeline .event:after {
    position: absolute;
    display: block;
    top: 0;
}

.timeline .event:before {
    left: -207px;
    content: attr(data-date);
    text-align: right;
    font-weight: 100;
    font-size: 0.9em;
    min-width: 120px;
}

/* @media (max-width: 767px) {
    .timeline .event:before {
        left: 0px;
        text-align: left;
    }
} */

.timeline .event:after {
    -webkit-box-shadow: 0 0 0 3px #727cf5;
    box-shadow: 0 0 0 3px #727cf5;
    left: -12px;
    background: #fff;
    border-radius: 50%;
    height: 9px;
    width: 9px;
    content: "";
    top: 5px;
}



  </style>



  
    @extends('layout.app')
    @section('content')
    <form action="{{ route('orderList') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12">
        <div class="row">
        
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control form-control-sm"
                    placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <input type="date" class="form-control form-control-sm" value="{{ isset($data['date']) ? $data['date'] : "" }}"  name="date">
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
                     
                      <option {{ isset($data['status']) && $data['status']==$system->value ? "selected" : ""}} value="{{ $system->value }}">{{$system->name }}</option>
                      
                       @endforeach
                    </select></div>
            <div class="justify-content-md-start  col-md-4 col-lg-4">
                <div class="row">
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      {{-- <input type="button" class="btn btn-primary " value="Search"> --}}
                     <a href="{{ route('orderList') }}">
                      <button type="submit" class="btn btn-primary btn-sm  search"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('orderList') }}">
                        <button type="button" class="btn btn-secondary btn-sm " style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
   {{-- @include('admin.main') --}}
    <table id="example" class="table table-striped" style="width:100%">
      <thead>
          <tr>
              <th>Order No</th>
              <th>Material</th>
              <th>Driver</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total Price</th>
              <th>Date</th>
              <th>Update By</th>
              <th>Status</th>
              <th>Verified</th>
              <td>Action</td>
             
          </tr>
      </thead>
      <tbody>
     
          @foreach ($orders as $order)
          @php
          $status_class = '';
          if($order->systemstatus->name == 'in progress'){
            $status_class = 'bg-info';
          }elseif($order->systemstatus->name == 'pending'){
            $status_class = 'bg-warning';
          }elseif($order->systemstatus->name == 'rejected'){
            $status_class = 'bg-danger';
          }elseif($order->systemstatus->name == 'completed'){
            $status_class = 'bg-success';
          }
          elseif($order->systemstatus->name == 'accepted'){
            $status_class = 'bg-success';
          }
          @endphp
         
          <tr>
              <td>
                <a data-bs-toggle="modal" data-bs-target="#orderModal" href="javascript:void(0)" id="{{$order->order_id}}" onclick="showOrderdata(this)">  {{ $order->order_no }}</a>
              </td>
              <td>{{ $order->material->name}}</td>
              <td>{{ $order->driver->fullname ?? ""}}</td>
              <td>{{ $order->quantity." (".$order->quantity_unit.")"  }}</td>
              <td>{{ $order->price}} SAR</td>
              <td>{{ $order->total_price}} SAR</td>
              <td>{{ $order->created_at }}</td>
              @if ( $order->updated_by != null &&  $order->updated_by != '')
              <td>{{ $order->updatedByPerson->fullname }} {{ $order->updated_at}}</td>
                  @else
                  <td></td>
              @endif
              <td><span class="badge {{ $status_class }}">{{ $order->systemstatus->name}}</span></td>
             <td id="check_icon-{{$order->order_id}}" style="text-align: center;">
              @if (  $order->is_verified  ==1)
              <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                  @else
                  <button type="button" id="btn-{{$order->order_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$order->order_id}}"  class=" btn btn-outline-primary verified_order">Verify</button>
              @endif
            </td>
              <td >
                <a data-bs-toggle="modal" data-bs-target="#viewtrack" href="javascript:void(0)" id="{{  $order->order_id}}" onclick="showTrack(this)"><i title="Track" class="fa fa-eye"></i></a>
                <a href="javascript:void(0)"  id="user-{{  $order->order_id}}" onclick="showUser(this)"><i class="bi bi-plus-circle-fill user_assign"  id="user_assign" title="Assign To"></i></a>
              </td>
             
          </tr>
          @endforeach
      </tbody>
    
     </table>
   

@include('admin/modals/order_Modal')
 <!-- End #main -->

 @include('admin/modals/buyerlistingModal')
 <script>

  $(document).ready(function () {
     $('#example').DataTable({
       "order": [[ 6, "desc" ]], //or asc 
       "columnDefs" : [{"targets":3, "type":"date-eu"}],
       scrollY: '300px',
       "scrollX": true,
       initComplete: function () {
            $('.dataTables_filter input[type="search"]').css({ 'height': '30px', 'width': '120px', 'display': 'inline-block' });
        }
       
   });
 
   $('.close').on('click', function(){
     $('#to_user').modal('hide');
     $('#to_user').on('hidden.bs.modal', function () {
     $(this).find('form').trigger('reset');
 });
   });
 });
//  $(document).ready(function() {
// });
</script>
@stop



 
 



