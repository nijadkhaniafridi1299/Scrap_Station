@extends('layout.app')

  @section('content')
 

    <form action="{{ route('offerLists') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12">
        <div class="row">
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <input placeholder="Seller Name" type="text" class="form-control form-control-sm" value="{{ isset($data['sfullname']) ? $data['sfullname'] : "" }}"  name="sfullname">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <input class="form-control form-control-sm" placeholder="Buyer Name" id="order_no" name="bfullname" value="{{ isset($data['bfullname']) ? $data['bfullname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <input type="text" class="form-control form-control-sm" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="flatpickr-range"/>            
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select form-select-sm" name="listingsource" id="listingsource" aria-label="Default select example">
                <option selected value="">Select Listing From List</option>
                <option  {{ isset($data['listingsource']) && ($data['listingsource']== 'seller_listing') ? "selected" : "" }} value="seller_listing">Seler Listing</option>
                <option {{ isset($data['listingsource']) && ($data['listingsource']== 'buyer_listing') ? "selected" : "" }} value="buyer_listing">Buyer Listing</option>
              </select>
            </div>
            {{-- <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select" name="is_verify" id="is_verify" aria-label="Default select example">
                <option value="">Select Verification From List</option>
              
                <option {{ isset($data['is_verify']) && ($data['is_verify']==1) ? "selected" : "" }} value="1">Verify</option>
                <option {{ isset($data['is_verify']) && ($data['is_verify']==0) ? "selected" : "" }} value="0">Not Verify</option>


              </select>
            </div> --}}
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select form-select-sm" name="status" id="status" aria-label="Default select example">
                  <option selected value="">Select Status From List</option>
                  @foreach ($system_status as $system)
                    <option {{ isset($data['status']) &&  $data['status'] ==$system->value? "selected" : ""}} value="{{ $system->value }}">{{$system->name }}</option>
                  
                  @endforeach
              </select>
            </div>
            <div class="justify-content-md-start  col-md-4 col-lg-4">
                <div class="row">
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      {{-- <input type="button" class="btn btn-primary " value="Search"> --}}
                     <a href="{{ route('offerLists') }}">
                        <button type="submit" class="btn btn-primary btn-sm  search" style="width: max-content;"><span>Search</span> </button>
                     </a>
        </form>
            </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('offerLists') }}">
                        <button type="button" class="btn btn-secondary btn-sm " style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
       
   {{-- @include('admin.main') --}}
   <div class="table_responsive">
    <table id="example" class="table table-striped">
      <thead>
          <tr>
              <th>Offer No</th>
              <th>Listing Source</th>
              <th>Seller</th>
              <th>Buyer</th>
              <th>Offer Price</th>
              <th>Date</th>
              <th>Update By</th>
              <th>Sub Total Price</th>
              <th>Status</th>
              <th>Chat</th>
             
          </tr>
      </thead>
      <tbody>
     
          @foreach ($offers as $offer)
          @php
          $status_class = '';
          if($offer->systemstatus->name == 'in progress'){
            $status_class = 'bg-info';
          }elseif($offer->systemstatus->name == 'pending'){
            $status_class = 'bg-warning';
          }elseif($offer->systemstatus->name == 'rejected'){
            $status_class = 'bg-danger';
          }
          elseif($offer->systemstatus->name == 'request updated'){
            $status_class = 'bg-info';
          }elseif($offer->systemstatus->name == 'accepted'){
            $status_class = 'bg-success';
          }
          elseif($offer->systemstatus->name == 'completed'){
            $status_class = 'bg-success';
          }
          
          @endphp
         
          <tr>
            <td>
              <a data-bs-toggle="modal" data-bs-target="#offer_Modal" href="javascript:void(0)"  id="{{$offer->offer_id}}" onclick="showRowData(this)">  {{ $offer->offer_no }}</a>
            
            </td>
             
              <td>{{ $offer->listing_source}}</td>
              <td>{{ $offer->seller->fullname?? ""}}</td>
              <td>{{ $offer->buyer->fullname ?? ""}}</td>
              <td>{{ $offer->offered_price}} SAR</td>
              <td>{{ $offer->created_at }}</td>
              @if ( $offer->updated_by != null && $offer->updated_source != null &&  $offer->updated_by != '')
              <td>{{ $offer->updatedByPerson->fullname }} {{ $offer->updated_at}}</td>
                  @else
                  <td></td>
              @endif
              <td>{{ $offer->offered_price_with_vat}} SAR</td>
              <td><span class="badge {{ $status_class }}">{{ $offer->systemstatus->name}}</span></td>
              
              {{-- <td id="check_icon-{{$offer->offer_id}}" style="text-align: center">
                @if ($offer->is_verified  == 1)
                <img src="{{ url('/') }}/assets/img/shield-tick.png" alt="" style="height: 20px">
  
                    @else
                    <button type="button" id="btn-{{$offer->offer_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;"  value="{{$offer->offer_id}}" onclick="showModel(this)"  class=" btn btn-outline-primary verified_buttion">Verify</button>
  
                @endif
            </td> --}}
             <td> 
              <a data-bs-toggle="modal" data-bs-target="#viewChart" href="javascript:void(0)"  id="btn-{{$offer->offer_id}}" style="height: 30px; width:56px; font-size:18px; font-weight: bold;"  value="" onclick="showChat('{{$offer-> application_source}}','{{$offer-> application_id}}')" >View</a>
             </td>
              {{-- <td><label class="switch">
                  <input type="checkbox" value="{{ $offer->is_verified }}" id="rowcheck-{{$offer->offer_id}}"  onchange="checkboxChange(this)" {{ $offer->is_verified==1 ? 'checked' : '' }} class="getvalue">
                  <span class="slider round"></span>
                </label>
              </td> --}}
             
          </tr>
          @endforeach
      </tbody>
    
  </table>
   </div>

@include('admin/modals/offermodal')
@include('admin/modals/order_Modal')

@include('admin/modals/picture_modal')


<script>
 $(document).ready(function () {
     $('#example').DataTable({
       "order": [[ 5, "desc" ]], //or asc 
       "columnDefs" : [{"targets":3, "type":"date-eu"}],
       scrollY: '300px',
       "scrollX": true
   });
  });
  $('#pictureModal').on('shown.bs.modal', function (e) {
    $('#pictureModal').css('z-index','9999');
  });
  $('#pictureModal').on('hidden.bs.modal', function (e) {
    $('#pictureModal').css('z-index','100');
  }); 
   
   </script>
@stop
{{-- <script src="{{ url('/') }}/assets/js/offer.js"></script> --}}

