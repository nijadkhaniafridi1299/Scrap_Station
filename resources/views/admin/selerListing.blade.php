    @extends('layout.app')
    @section('content')
    <form action="{{ route('seller_listing') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12">
        <div class="row">
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="Seller Name" type="text"
                    class="form-control form-control-sm" value="{{ isset($data['fullname']) ? $data['fullname'] : "" }}"  name="fullname"></div>
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <input type="date" class="form-control form-control-sm" value="{{ isset($data['date']) ? $data['date'] : "" }}"  name="date">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control form-control-sm"
                    placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text">
            </div>
            
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control form-control-sm"
              placeholder="Active days" id="name" name="day" value="{{ isset($data['day']) ? $data['day'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text">
            </div>
            <div class="mb-1 col-6 col-sm-6 col-md-3"><select class="form-select form-select-sm" name="is_verify" id="is_verify" aria-label="Default select example">
              <option value="">Select Verification From List</option>
            
              <option {{ isset($data['is_verify']) && ($data['is_verify']==1) ? "selected" : "" }} value="1">verified</option>
              <option {{ isset($data['is_verify']) && ($data['is_verify']==0) ? "selected" : "" }} value="0">unverified</option>


            </select></div>
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
                     <a href="{{ route('seller_listing') }}">
                      <button type="submit" class="btn btn-primary btn-sm search waves-effect waves-light"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('seller_listing') }}">
                        <button type="button" class="btn btn-secondary btn-sm" style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->

   {{-- @include('admin.main') --}}
 <div class="table_responsive">
  <table id="datatables" class="table table-striped display" style="width:100%">
    <thead>
        <tr>
             <th></th>
            <th>Listing No</th>
            <th>Seller</th>
            <th>Material</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Total</th>
            <th>Date</th>
            <th>Active Days</th>
            <th>Updated By</th>
            <th>Status</th>
             <th>Verified</th>
           
        </tr>
    </thead>
    <tbody>
        @foreach ($listings as $listing)
        @php
        $status_class = '';
       if($listing->systemstatus->name == 'opened'){
          $status_class = 'bg-success';
        }elseif($listing->systemstatus->name == 'closed'){
          $status_class = 'bg-danger';
        }
      
     
        @endphp
       
        <tr id="rowid-{{ $listing->sell_list_id }}">
           <td class="dt-control" data-customer-id="{{$listing->sell_list_id}}"></td>

          <td>
          <a data-bs-toggle="modal" data-bs-target="#exampleModal" href="javascript:void(0)" id="{{$listing->sell_list_id}}" onclick="showListing(this)">  {{ $listing->listing_no }}</a>
          </td>
          <td>{{ $listing->seller->fullname }}</td>
            <td>{{ $listing->material->name}}</td>
            <td>{{ $listing->quantity." (".$listing->quantity_unit.")"  }}</td>
            <td>{{ $listing->expected_price_per_unit." (per ".$listing->quantity_unit.") "}} SAR</td>
            <td>{{ $listing->expected_price}} SAR</td>
            <td>{{ $listing->created_at }}</td>
            <td>{{ $listing->active_days }}</td>
            @if ( $listing->updated_by != null &&  $listing->updated_by != '')
            <td>{{ $listing->updatedByPerson->fullname }} {{ $listing->updated_at}}</td>
                @else
                <td></td>
            @endif
            <td><span class="badge {{ $status_class }}">{{ $listing->systemstatus->name}}</span></td>
            <td id="check_icon-{{$listing->sell_list_id}}" style="text-align: center;">
            @if ( $listing->is_verified ==1)
            <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                @else
                <button type="button" id="btn-{{$listing->sell_list_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$listing->sell_list_id}}"  class=" btn btn-outline-primary verified_seller_listing">Verify</button>
            @endif
          </td>
        </tr>
        @endforeach
    </tbody>
  
</table>
 </div>


@include('admin/modals/sellerListingModal')
@include('admin/modals/offermodal')
@include('admin/modals/order_Modal')
@include('admin/modals/picture_modal')





 
<script>

    $(document).ready(function () {
       var myTable =  $('#datatables').DataTable({
       
          "order": [[7, "desc" ]], //or asc 
          "columnDefs" : [{"targets":3, "type":"date-eu"}],
          scrollY: '330px',
          "scrollX": true
          
      });
      // $('#datatable').delegate('.dt-control', 'click',function (){
      //   alert('clicked');
      // });
   
      $('#datatables').on('click', 'td.dt-control', function () {
          var tr = $(this).closest('tr');
          var row = myTable.row( tr );
          if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown dt-hasChild');
          }
          else {
            // Open this row
            sellerid = $(this).attr("data-customer-id");
            // alert(sellerid);
            var table = getApplicant(sellerid);
           
              row.child( table ).show();
              tr.addClass('shown dt-hasChild');
          }
        });
   
  function getApplicant(value)
      {
        var table_content = '';
          table =  $.ajax({
            type: 'GET',
            async:false,
            url: "{{ route('seller_applicant')}}"+"/"+value,
           
            
            dataType: "json",
            showLoader: true,
            success: function(data) {
            // var  result = JSON.parse(data);\
            //console.log(data);
            var  result = data.applicants;
            
            if(result.offers.length>0)
            {
            
              /* Creating table dynamicaly - START */
              table_content = "<table style='width:100%;border-collapse: collapse;'>";
              table_content += "<thead>";
              // table_content += "<tr><th style='border: 1px solid black;text-align: center;'>Offer No</th>";
              table_content += "<tr><th style='border: 1px solid black;text-align: center;'>Offer No</th>";
              table_content += "<th style='border: 1px solid black;text-align: center;'>Buyer Name</th>";
              table_content += "<th style='border: 1px solid black;text-align: center;'>Price</th>";
              table_content += "<th style='border: 1px solid black;text-align: center;'>Chat</th></tr>";
              
               table_content += "</thead>";
              table_content += "<tbody>";
              $.each(result.offers, function(index, element) {
              

                table_content += "<tr><td  style='border: 1px solid black;text-align: center;'><a data-bs-toggle='modal' data-bs-target='#offer_Modal' href= 'javascript:void(0)' id='"+element.offer_id+"' onclick='showoffering(this)'> "+element.offer_no+"</a></td>";
                table_content += "<td style='border: 1px solid black;text-align: center;'>"+element.buyer.fullname+"</td>";
                table_content += "<td style='border: 1px solid black;text-align: center;'>"+element.offered_price+"</td>";
                table_content+= '<td style="border: 1px solid black;text-align: center;"> <a data-bs-toggle="modal" data-bs-target="#viewChart" href="javascript:void(0)" id="btn-'+element.application_id+'" style=" font-size:17px; font-weight: bold;"  value="'+element.application_source+'" onclick="showChat(\''+element.application_source+'\','+element.application_id+')">View</a> </td></tr>';
            
                
              });
              table_content += "</tbody>";
              table_content += "</table>";
              
            }
            else
            {
              table_content = "<h4 style='text-align:center;'>No offer found </h4>";
            }
          },
          complete: function(){
            $('#loading').hide();
          }
              
            
          
            
          });
          return table_content;
        }
    });
  //   function showModel(data){
  //   var offer = data.value;
           
  //             $('#rowcheck-').val(offer);
  //             // console.log(newval);
            
  //             $('#deletestudentmodal').modal('show');
  // }
   
</script>
<script>

        
            // $(document).on('click', '.verified_buttion', function (e){
            //     e.preventDefault();
            //    var student_id = $(this).val();
            //     $('.confirm_btn').attr('id','rowcheck-'+student_id);
            
            //   $('#deletestudentmodal').modal('show');
            // });





</script>
@stop



