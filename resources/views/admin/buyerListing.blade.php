 @extends('layout.app')
 @section('content')
     
    <form action="{{ route('buyer_listing') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12">
        <div class="row">
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="Buyer Name" type="text"
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
                     
                      <option {{ isset($data['status']) && $data['status']==$system->value ? "selected" : ""}} value="{{ $system->value }}">{{$system->name }}</option>
                      
                       @endforeach
                    </select></div>
            <div class="justify-content-md-start  col-md-4 col-lg-4">
                <div class="row">
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      {{-- <input type="button" class="btn btn-primary " value="Search"> --}}
                     <a href="{{ route('buyer_listing') }}">
                      <button type="submit" class="btn btn-primary btn-sm  search"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('buyer_listing') }}">
                        <button type="button" class="btn btn-secondary btn-sm" style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table_responsive">
      <table id="datatable1" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                 <th></th>  
                <th>Listing No</th>
                <th>Buyer</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Total</th>
                <th>Date</th>
                <th>Active Day</th>
                <th>Update By</th>
                <th>Status</th>
                 <th>Verified</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach ($buyerlistings as $buyerlisting)
            @php
            $status_class = '';
           if($buyerlisting->systemstatus->name == 'opened'){
              $status_class = 'bg-success';
            }elseif($buyerlisting->systemstatus->name == 'closed'){
              $status_class = 'bg-danger';
            }
          
         
            @endphp
           
            <tr>
              
              <td class="dt-control" data-customer-id="{{$buyerlisting->buyer_list_id}}"></td>

              <td>
              <a data-bs-toggle="modal" data-bs-target="#buyerLising" href="javascript:void(0)" id="{{$buyerlisting->buyer_list_id}}" onclick="showListing(this)">  {{ $buyerlisting->listing_no }}</a>
              </td>
              <td>{{ $buyerlisting->buyer->fullname }}</td>
                <td>{{ $buyerlisting->material->name}}</td>
                <td>{{ $buyerlisting->quantity." (".$buyerlisting->quantity_unit.")"  }}</td>
                <td>{{ $buyerlisting->expected_price_per_unit." (per ".$buyerlisting->quantity_unit.") "}} SAR</td>
                <td>{{ $buyerlisting->expected_price}} SAR</td>
                <td>{{ $buyerlisting->created_at }}</td>
                <td>{{ $buyerlisting->active_days }}</td>
                @if ( $buyerlisting->updated_by != null &&  $buyerlisting->updated_by != '')
                <td>{{ $buyerlisting->updatedByPerson->fullname }} {{ $buyerlisting->updated_at}}</td>
                    @else
                    <td></td>
                @endif
                <td><span class="badge {{ $status_class }}">{{ $buyerlisting->systemstatus->name}}</span></td>
                <td id="list_icon-{{$buyerlisting->buyer_list_id}}" style="text-align: center;">
                @if ( $buyerlisting->is_verified ==1)
                <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                    @else
                    <button type="button" id="btn-{{$buyerlisting->buyer_list_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$buyerlisting->buyer_list_id}}"  class=" btn btn-outline-primary verified_buyerListing">Verify</button>
                @endif
              </td>
            </tr>
            @endforeach
        </tbody>
      
    </table>
    </div>
    
    @include('admin/modals/buyerlistingModal')
    @include('admin/modals/offermodal')
    @include('admin/modals/order_Modal')
    @include('admin/modals/picture_modal')


<script>
  $(document).ready(function () {
    var myTable =  $('#datatable1').DataTable({
          "order": [[ 7, "desc" ]], //or asc 
          "columnDefs" : [{"targets":5, "type":"date-eu"}],
          scrollY: '330px',
          "scrollX": true
      });
      
      $('#datatable1').on('click', 'td.dt-control', function () {
          var tr = $(this).closest('tr');
          var row = myTable.row( tr );
          if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown dt-hasChild');
          }
          else {
            // Open this row
            buyerid = $(this).attr("data-customer-id");
            // alert(buyerid);
            var table = getApplicant(buyerid);
           
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
            url: "{{ route('buyer_applicant')}}"+"/"+value,
           
            
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
              table_content += "<th style='border: 1px solid black;text-align: center;'>Seller Name</th>";
              table_content += "<th style='border: 1px solid black;text-align: center;'>Price</th>";
              table_content += "<th style='border: 1px solid black;text-align: center;'>Chat</th></tr>";
              
               table_content += "</thead>";
              table_content += "<tbody>";
              $.each(result.offers, function(index, element) {
                  console.log(element.application_source);

                table_content += "<tr><td style='border: 1px solid black;text-align: center;'><a data-bs-toggle='modal' data-bs-target='#offer_Modal' href= 'javascript:void(0)' id='"+element.offer_id+"' onclick='showoffering(this)'> "+element.offer_no+"</a></td>";
                table_content += "<td style='border: 1px solid black;text-align: center;'>"+element.seller.fullname+"</td>";
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

</script>
<script>
        $('td').delegate('.verified_buyerListing', 'click',function (e){
                e.preventDefault();
               var listing_id = $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+listing_id);
            
              $('#buyerListingmodal').modal('show');
            }); 
   function buyerListingVerified(data){
  // console.log(data);
        var listing_id = data.id;
        if($(this).hasClass('statechange')){
         listing_id=listing_id.replace('modalcheck-','');
        }else{
          listing_id=listing_id.replace('rowcheck-','');
        }
        // listing_id=listing_id.replace('rowcheck-','');
        $('#buyerListingmodal').modal('hide');
        // var value = $(this).val();
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('buyer_listing.update') }}",
		        type: "POST",
		        data: {"id": listing_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                 
                        
                          var listing= response.buyerlisting;
                          if(listing.is_verified == 1){
                            alert('Verified Seller Listing');
                            $('.modal-backdrop').remove();
                            $(`#btn-${listing_id}`).hide();                    
                            $(`#btn-${listing_id}`).addClass('d-none'); 
                            $('#list_icon-'+listing_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_buyerlisting-'+listing_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');

                            // $('#rowcheck-'+listing_id).prop('checked',true);
                          }else{
                            // $('#rowcheck-'+listing_id).prop('checked',false);
                          }
                            
                        
                    }
                }
        });
} 
    function showListing(data){
  // console.log(data);
        var listing_id = data.id;
     
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('buyer_listing')}}"+"/"+listing_id,
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
           
                   var listing = response.buyerlistings;
                   var image = response.images;

                   console.log(image);
                   $(".status").removeClass("bg-success bg-danger");

                   $(".buyerlistingClass").empty();
                      $('#single_record tbody').html("");
                      $('.modal-title').text('Listing No: '+listing.listing_no);
                      $('.price').text(listing.expected_price_per_unit+" (per "+listing.quantity_unit+") ");
                      $('.total').text(listing.expected_price+'SAR');
                      // $('.close_reson').text(listing.closed_reason);
                      $('.seller').text('buyer: '+listing.buyer.fullname);
                      $('.selermobile').text('Mobile: '+listing.buyer.mobile);
                      $('.material').text(listing.material.name );
                      if(listing.closed_reason != null){
                      $('.close_reson').html('<p><strong>Reason</strong></p><p>'+listing.closed_reason+'</p>');
                      }
                      $('.address').text(listing.address.address );
                      if (listing.systemstatus.name == 'opened') {
                        var status_class = 'bg-success';
                      }else if (listing.systemstatus.name == 'closed') {
                        var status_class = 'bg-danger';
                      }
                      $('.statuslisting').addClass(status_class);
                      $('.statuslisting').text(listing.systemstatus.name);
                   
                      $('.buyerlistingClass').attr('id','icon_buyerlisting-'+listing.buyer_list_id); 
                      $('.statechange').attr('id',listing.buyer_list_id);

                                      

                      if(listing.is_verified == 0){
                        $('#icon_buyerlisting-'+listing.buyer_list_id).append('<button type="button" id="btn-'+listing.buyer_list_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+listing.buyer_list_id+'"  class=" btn btn-outline-primary verified_buyerListing">Verify</button>');

                       
                     
                      }
                      else{
                        $('#icon_buyerlisting-'+listing.buyer_list_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');

                       

                      }
                      
                      $('.weight').text(listing.quantity+'('+listing.quantity_unit+')');
                   
                      showImagesDetails(image, "total_images", "listing_images");
                    
                
                }
        });
}
</script>
@stop