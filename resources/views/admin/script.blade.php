


{{-- offer function --}}
<script>
  
  $(document).ready(function (){
   var table = $('#datatable').DataTable({
      //  'responsive': true,
       "order": [[ 5, "desc" ]],
       "columnDefs" : [{"targets":3, "type":"date-eu"}],
       scrollY: '330px',
       "scrollX": true
   });

   // Handle click on "Expand All" button
   $('#btn-show-all-children').on('click', function(){
       // Expand row details
       table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
   });

   // Handle click on "Collapse All" button
   $('#btn-hide-all-children').on('click', function(){
       // Collapse row details
       table.rows('.parent').nodes().to$().find('td:first-child').trigger('click');
   });
});
</script>
<script>
     function showModel(data){
    var offer = data.value;
           
              $('#rowcheck-').val(offer);
              // console.log(newval);
            
              $('#deletestudentmodal').modal('show');
  }
function checkboxChange(data){
  // console.log(data);
        var offer_id = data.value;
        
       
       
        if($(this).hasClass('statechange')){
          offer_id =offer_id .replace('modalcheck-','');
        }else{
          offer_id =offer_id.replace('rowcheck-','');
        }
        $('#deletestudentmodal').modal('hide');
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('offer_listing.update') }}",
		        type: "POST",
		        data: {"id": offer_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                    
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var offer= response.offer;
                           
                          if(offer.is_verified == 1){
                             
                            alert('Verified Offer');
                            $('.modal-backdrop').remove();
                            $(`#btn-${offer_id}`).hide();       
                         
                            $('#check_icon-'+offer_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_model-'+offer_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt=""style="height: 40px">');
                            $('#rowcheck-'+offer_id).prop('checked',true);
                          }else{
                            $('#rowcheck-'+offer_id).prop('checked',false);
                          }
                            
                        }
                    }
                }
        });
}
function showRowData(data){
  // console.log(data);
  var offer_id = data.id;
  offer_id=offer_id.replace('btn-','');
  $.when(callAjaxFunction("{{ route('offerLists')}}"+"/"+offer_id,"GET","")).then(function(response) {
        var offer = response.offers;
        var status_class = "";
        //     // console.log(listing);
        $(".toggleClass").empty();
        $(".status").removeClass("bg-success bg-warning bg-danger bg-info");
        $('#single_record tbody').html("");

        $('.modal-title').text('Offer No: '+offer.offer_no);
        $('.price').text(offer.offered_price+'SAR');
        // $('.close_reson').(offer.reason);
        if(offer.reason != null){ $('.close_reson').html('<p><strong>Reason</strong></p><p>'+offer.reason+'</p>'); }
        $('.tprice').text(offer.offered_price_with_vat+'SAR');
        $('.seller').text('Seller: '+offer.seller.fullname);
        $('.selermobile').text('Mobile Number: '+offer.seller.mobile);

        $('.buyer').text('Buyer: '+offer.buyer.fullname);
        $('.buyermobile').text('Mobile Number:'+offer.buyer.mobile);
        $('.material').text(offer.listing_source);
        $('.address').text(offer.address.address );
        if(offer.systemstatus.name == 'rejected'){ status_class = 'bg-danger'; }
        else if (offer.systemstatus.name == 'completed') { status_class = 'bg-success'; }
        else if (offer.systemstatus.name == 'in progress') { status_class = 'bg-info'; }
        else if (offer.systemstatus.name == 'pending') { status_class = 'bg-warning'; }
        else if (offer.systemstatus.name == 'accepted') { status_class = 'bg-success'; }
        else if (offer.systemstatus.name == 'request updated') { status_class = 'bg-info'; }

        $('.status').addClass(status_class);
        $('.status').text(offer.systemstatus.name);
        $('.toggleClass').attr('id','icon_model-'+offer.offer_id);                   

        if(offer.previous_offer_id != null){
          $('#previous_offer').show();
          $('#previous_offer').html('<button data-bs-toggle= "modal" data-bs-target= "#offer_Modal"  type="button"  id="btn-'+offer.previous_offer_id+'" class="btn btn-outline-secondary"  value="'+offer.previous_offer_id+'" onclick="showRowData(this)"  class=" btn btn-outline-primary ">Previous Offers</button>');
        }
        else if(offer.previous_offer_id == null || offer.previous_offer_id == ""){ $('#previous_offer').hide(); }  
        if(offer.order == undefined && offer.order == null){ $('#view_order').hide(); }
        else if( offer.order.order_id !== null){
          $('#view_order').show();
          $('#view_order').html('<button data-bs-toggle="modal" data-bs-target="#orderModal" type="button" id="btn-'+offer.order.order_id+'"  value="'+offer.order.order_id+'" onclick="showOrderdata(this)"  class=" btn btn-outline-success "> Order</button>');
        }
  });
}
//seller Listing

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
            url: "{{ route('seller_listing')}}"+"/"+listing_id,
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
           
                      var listing = response.listing;
                      var image = response.images;
                
                      $(".toggleClass").empty();
                      $('.images_empty').empty();
                      $('.close_reson').empty();
                      $(".status").removeClass("bg-success bg-danger ");
                      $('#single_record tbody').html("");
                      $('.modal-title').text('Listing No: '+listing.listing_no);
                      
                      if(listing.closed_reason != null){
                      $('.close_reson').html('<p><strong>Reason</strong></p><p>'+listing.closed_reason+'</p>');
                      }
                      $('.price').text(listing.expected_price_per_unit+" (per "+listing.quantity_unit+") ");
                      $('.total').text(listing.expected_price+'SAR');
                      $('.seller').text('Seller: '+listing.seller.fullname);
                      $('.selermobile').text('Mobile: '+listing.seller.mobile);
                      $('.material').text(listing.material.name );
                      $('.address').text(listing.address.address );
                      if(listing.systemstatus.name == 'closed'){
                        var status_class = 'bg-danger';
                      }else if (listing.systemstatus.name == 'opened') {
                        var status_class = 'bg-success';
                      }
                      
                      $('.statuslisting').addClass(status_class);
                      $('.statuslisting').text(listing.systemstatus.name);
                   
                      $('.toggleClass').attr('id','icon_model-'+listing.sell_list_id); 
                      $('.statechange').attr('id',listing.sell_list_id);

                                      

                      if(listing.is_verified == 0){
                        $('#icon_model-'+listing.sell_list_id).append('<button type="button" id="btn-'+listing.sell_list_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+listing.sell_list_id+'"  class=" btn btn-outline-primary verified_seller_listing">Verify</button>');

                       
                     
                      }
                      else{
                        $('#icon_model-'+listing.sell_list_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');

                       

                      }  
                      $('.weight').text(listing.quantity+'('+listing.quantity_unit+')');
                      showImagesDetails(image, "total_images", "listing_images");
                     
                
                   
                }

        });
}
$('td').delegate('.verified_seller_listing', 'click',function (e){
                e.preventDefault();
               var listing_id = $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+listing_id);
            
              $('#verify_listing').modal('show');
            }); 

function selerListingVerified(data){
  // console.log(data);
        var listing_id = data.id;
        if($(this).hasClass('statechange')){
         listing_id=listing_id.replace('modalcheck-','');
        }else{
          listing_id=listing_id.replace('rowcheck-','');
        }
        // listing_id=listing_id.replace('rowcheck-','');
        $('#verify_listing').modal('hide');
        // var value = $(this).val();
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('seller_listing.update') }}",
		        type: "POST",
		        data: {"id": listing_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                 
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var listing= response.listing;
                          if(listing.is_verified == 1){
                            alert('Verified Seller Listing');
                            $('.modal-backdrop').remove();
                            $(`#btn-${listing_id}`).addClass('d-none'); 

                            $(`#btn-${listing_id}`).hide();
                          
                            
                        
                            $('#check_icon-'+listing_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_model-'+listing_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');

                            $('#rowcheck-'+listing_id).prop('checked',true);
                          }else{
                            $('#rowcheck-'+listing_id).prop('checked',false);
                          }
                            
                        }
                    }
                }
        });
}
function showTrack(data){
 var order_id = data.id;
 $.ajax({
            url: "{{ route('getTrack')}}"+"/"+order_id,
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                 var treack_point = response.track;
                 var tracking = '';
                 $.each(treack_point, function(index, treack_points) {
              
                  tracking +='<div id="content" > <ul class="timeline" style="width: 60%;"> <li class="event" data-date="'+treack_points.checkin_datetime +'"> <h3>'+treack_points.checkpoint.name+'</h3> <p>'+treack_points.note+'</p> </li> </ul> </div>';                  
                   
                 });
                 if(treack_point.length >0){
                  $("#trackings").html(tracking);
                 
                 }
                 else{
                  $("#trackings").text("No Message");

                 }
                }
        });
}

// new chat-offer function
function showChat(src_name,src_id){

  $('#messagelenght').text("");
  $("#comments_data").html("");
  $('.image_empty').empty();
  $.when(callAjaxFunction("{{ route('get_chat')}}"+"/"+src_name+"/"+src_id,"GET","")).then(function(response) {
    // console.log(response);
    if(typeof(response)!="undefined"){
      var comment = response.data.comments;
        //  console.log(comment);
      $('#messagelenght').text(comment.length);
      let comment_data = '';
      $.each(comment, function(index, element) {
        comment_data = '';
       
        if(element.created_source == "seller"){
          comment_data +='<div class="d-flex justify-content-between"><p class="small mb-1">'+element.commenter+'</p><p class="small mb-1 text-muted">'+element.created_at+'</p></div><div class="d-flex flex-row justify-content-start"><span style="width: 45px; height: 100%;"><i class="bi bi-person-fill" style= "color:green"></i></span><div> <p class="small p-2 ms-3 mb-3 rounded-3" style="background-color: green; color:#eee">'+element.comment_text+'</p> </div> </div><table class="image_empty" id= "listing_images_'+element.comment_id+'"></table>';
        }
        else{
          comment_data += '<div class="d-flex justify-content-between"> <p class="small mb-1 text-muted">'+element.created_at+'</p> <p class="small mb-1">'+element.commenter+'</p> </div> <div class="d-flex flex-row justify-content-end mb-4 pt-1"> <div> <p class="small p-2 me-3 mb-3 text-white rounded-3" style="background-color: blue">'+element.comment_text+'</p> </div> <span><i class="bi bi-person-fill" style= "color:blue"></i></span></div> <table class="d-flex flex-row justify-content-end mb-4 image_empty" style="margin-top:-8%;" id= "listing_images_'+element.comment_id+'"></table>';
        }
      $("#comments_data").append(comment_data);

        showImagesDetails(element.images, "total_images_"+element.comment_id, "listing_images_"+element.comment_id);

      });
      // $("#comments_data").html(comment_data);
    }
});
}


// order function
function showOrderdata(data){
  var order_id = data.id;
  order_id=order_id.replace('btn-','');
  $.when(callAjaxFunction("{{ route('orderList')}}"+"/"+order_id,"GET","")).then(function(response) {
    var order = response.orders;
  //  console.log(order);
    $(".orderClass").empty(); $(".driver_data").empty(); $(".note").empty();
    $("#review").empty(); $("#star_icon").empty(); $('#single_record tbody').html("");
    $(".status").removeClass("bg-success bg-warning bg-danger bg-info");
    $('.modal-title').text('Order No: '+order.order_no);
    $('.price').text(order.price+'SAR');
      if(order.review != null){
        $('#review').html('<h6 class="mt-1 mb-1 pb-0 pt-0 text-center fw-bold" id="review">Review</h6> <table class="table"> <thead> <tr> <th class="  text-center" id="star_icon"></th> </tr> </thead> <tbody> <tr> <td class="py-1"><p class="card-text mb-25 note  text-center"></p></td> </tr> </tbody> </table>');
        var i = 0;
        var star = '';
        for (var i = order.review.stars; i >=1; i--){ star += '<i class="bi bi-star-fill"></i>'; }
        for (var i = (5 - order.review.stars); i >=1; i--){ star += '<i class="bi bi-star"></i>'; }
        $('#star_icon').html(star);
        $('.note').text(order.review.note);
      }
      
      $('.tprice').text(order.total_price+'SAR');
      $('.seller').text('Seller: '+order.offer.seller.fullname);
      $('.selermobile').text('Mobile Number: '+order.offer.seller.mobile);
      $('.buyer').text('Buyer: '+order.offer.buyer.fullname);
      $('.sellerlisting').text(order.offer.listing_source);
      $('.buyermobile').text('Mobile Number: '+order.offer.buyer.mobile);
      
      // $('.selermobile').text('Mobile: '+listing.seller.mobile);
      $('.orderClass').attr('id','icon_order-'+order.order_id);                   

      $('.material').text(order.material.name );
      $('.address').text(order.address.address );
      
      $('.statechange').attr('id',order.order_id);
      if(order.is_verified == 0){ $('#icon_order-'+order.order_id).append('<button type="button" id="btn-'+order.order_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+order.order_id+'"  class=" btn btn-outline-primary verified_order">Verify</button>'); }
      else{ $('#icon_order-'+order.order_id).append('<img class="verified_icon" src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">'); }
      $('.weight').text(order.quantity+'('+order.quantity_unit+')');

      var status_class = '';
      if(order.systemstatus.name == 'rejected'){ status_class = 'bg-danger'; }
      else if (order.systemstatus.name == 'completed') { status_class = 'bg-success'; }
      else if (order.systemstatus.name == 'in progress') { status_class = 'bg-info'; }
      else if (order.systemstatus.name == 'pending') { status_class = 'bg-warning'; }
      else if (order.systemstatus.name == 'accepted') { status_class = 'bg-success'; }
      
      $('.status').addClass(status_class);
      $('.status').text(order.systemstatus.name);
      $('.driver').text('Driver: '+order.driver.fullname);
      $('.drivermobile').text('Mobile Number: '+order.driver.mobile);
  });
}
$('td').delegate('.verified_order', 'click',function (e){
                e.preventDefault();
               var order_id= $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+order_id);
              
               $('#orderconfirmation').modal('show');
            });
function VerifiedOrder(data){
  // console.log(data);
        var order_id = data.id;
      
        if($(this).hasClass('statechange')){
          order_id=order_id.replace('modalcheck-','');
        }else{
          order_id=order_id.replace('rowcheck-','');
        }
        $('#orderconfirmation').modal('hide');

        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('order_listing.update') }}",
		        type: "POST",
		        data: {"id": order_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                        
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var order= response.order;
                          if(order.is_verified == 1){
                            alert('Verified Order');
                            // $('.modal-backdrop').remove();
                            $(`#btn-${order_id}`).hide();
                            $(`#btn-${order_id}`).addClass('d-none'); 

                          
                            $('#check_icon-'+order_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_order-'+order_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#rowcheck-'+order_id).prop('checked',true);
                          }else{
                            $('#rowcheck-'+order_id).prop('checked',false);
                          }
                            
                        }
                    }
                }
        });
}


function callAjaxFunction(url_endpoint,type,data){
  return $.ajax({
      url: url_endpoint,
      type: type, data : data, datatype: "json",
      // async : false,
      beforeSend: function () { $('#loader').show(); },
      success:function (response){
        $('#loader').hide();
        // console.log(response);
        if (response.status == 200){ return response; }
      },
      error: function () { $('#loader').hide(); },
  });
}

// common function
function showoffering(data){
     
     var offer_id = data.id;
     offer_id=offer_id.replace('btn-','');
     $('.offer_verified_tr').attr('id', 'offer_verified')
    // console.log(offer_id);
 // var value = $(this).val();
 // alert(value);  
 //      if($(this).is(":checked")){
 //       alert('check');
 //       value =1;
 //      }
      
 $.ajax({
     url: "{{ route('offerLists')}}"+"/"+offer_id,
     type: "GET",
     //data: {"_token": "{{csrf_token()}}"},
     datatype: "json",
        
         success:function (response){
    
            var offer = response.offers;
           // console.log(offer.order);
           //     // console.log(listing);
           //$(".toggleClass").empty();
           $(".status").removeClass("bg-success bg-warning bg-danger bg-info");
               $('#single_record tbody').html("");

               $('.modal-title').text('Offer No: '+offer.offer_no);
               $('.price').text(offer.offered_price+'SAR');
               $('.tprice').text(offer.offered_price_with_vat+'SAR');
               $('.seller').text('Seller: '+offer.seller.fullname);
               $('.selermobile').text('Mobile Number: '+offer.seller.mobile);
               $('.close_reson').html('<p><strong>Reason</strong></p><p>'+offer.reason+'</p>');

               $('.buyer').text('Buyer: '+offer.buyer.fullname);
               $('.buyermobile').text('Mobile Number:'+offer.buyer.mobile);
           //     $('.driver').text('Driver: '+order.driver.fullname);
           //     $('.drivermobile').text('Mobile Number: '+order.driver.mobile);
           //     // $('.selermobile').text('Mobile: '+listing.seller.mobile);
               $('.material').text(offer.listing_source);
               $('.address').text(offer.address.address );
               if(offer.systemstatus.name == 'rejected'){
                 var status_class = 'bg-danger';
               }else if (offer.systemstatus.name == 'completed') {
                 var status_class = 'bg-success';
               }else if (offer.systemstatus.name == 'in progress') {
                 var status_class = 'bg-info';
               }else if (offer.systemstatus.name == 'pending') {
                 var status_class = 'bg-warning';
               }else if (offer.systemstatus.name == 'accepted') {
                 var status_class = 'bg-success';
               }else if (offer.systemstatus.name == 'request updated') {
                 var status_class = 'bg-info';
               }
               if(offer.previous_offer_id != null){
                 $('#previous_offer').show();

                 $('#previous_offer').html('<button data-bs-toggle= "modal" data-bs-target= "#offer_Modal"  type="button"  id="btn-'+offer.previous_offer_id+'" class="btn btn-outline-secondary"  value="'+offer.previous_offer_id+'" onclick="showoffering(this)"  class=" btn btn-outline-primary ">Previous Offers</button>');

               }
               else if(offer.previous_offer_id == null || offer.previous_offer_id == ""){
                 $('#previous_offer').hide();
               }
               if(offer.order == undefined && offer.order == null){
                 // if(offer.order.order_id == null || offer.order.order_id == ""){
                    $('#view_order').hide();  
                   // }
               }
               else if( offer.order.order_id !== null){
                 $('#view_order').show();
                 $('#view_order').html('<button data-bs-toggle="modal" data-bs-target="#orderModal" type="button" id="btn-'+offer.order.order_id+'"  value="'+offer.order.order_id+'" onclick="showOrderdata(this)"  class=" btn btn-outline-success "> Order</button>');

             
               }
               $('.status').addClass(status_class);
               $('.status').text(offer.systemstatus.name);
               
               $('#offer_verified').attr('id','icon_model-'+offer.offer_id);                   

               $('.statechange').attr('id',order.order_id);
               if(order.is_verified == 0){
                 $('.statechange').prop('checked',false);
               }
               else{
                 $('.statechange').prop('checked',true);

               }
               $('.weight').text(order.quantity+'('+order.quantity_unit+')');
            
           $('.statechange').attr('id',offer.offer_id);
               if(offer.is_verified == 0){
                 $('#offer_verified').html('<button type="button"  id="btn-'+offer.offer_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;"  value="'+offer.offer_id+'" onclick="showModel(this)"  class=" btn btn-outline-primary ">Verify</button>');
                 //$('.statechange').prop('checked',false);
               }
               else{
                 $('#offer_verified').html('<img  src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 40px">');


               }
             
         
         }
 });
   }
   //payment
   function showpayment(data){
  var pay_id = data.id;
  $.when(callAjaxFunction("{{ route('paymentList')}}"+"/"+pay_id,"GET","")).then(function(response) {
    var payment = response.payment;
    $(".paymentClass").empty();
    $(".status").removeClass("bg-success bg-warning bg-danger ");

    $('#single_record tbody').html("");
    $('.modal-title').text('Payment No: '+payment.pay_no);
    $('.price').text(payment.order.price+'SAR');
    $('.tprice').text(payment.order.total_price);
    $('.sellerlisting').text(payment.order.offer.listing_source);

    // $('.seller').text('Seller: '+payment.order.offer.listing.listing_no);
    // $('.listingno').text(payment.order.offer.);
    $('.orderno').text(payment.order.order_no);
    $('.selermobile').text('Mobile: '+payment.order.offer.seller.mobile);
    $('.material').text(payment.order.material.name );
    $('.buyer').text('Buyer: '+payment.order.offer.buyer.fullname);
    $('.buyermobile').text('Mobile Number: '+payment.order.offer.buyer.mobile);
    // $('.address').text(payment.order.address.address );
    var status_class = '';

    if(payment.systemstatus.name == 'rejected'){ status_class = 'bg-danger'; }
    else if (payment.systemstatus.name == 'completed') { status_class = 'bg-success'; }
    else if (payment.systemstatus.name == 'in progress') { status_class = 'bg-info'; }
    else if (payment.systemstatus.name == 'pending') { status_class = 'bg-warning'; }
    else if (payment.systemstatus.name == 'accepted') { status_class = 'bg-success'; }
    
    $('.status').addClass(status_class);
    $('.status').text(payment.systemstatus.name);
    $('.driver').text('Driver: '+payment.order.driver.fullname);
    $('.drivermobile').text('Mobile Number: '+payment.order.driver.mobile);
    $('.weight').text(payment.order.quantity+'('+payment.order.quantity_unit+')');
    $('.paymentClass').attr('id','icon_payment-'+payment.pay_id);
  });
}

 // driver module
function showDriver(data){
  var driver_id = data.id;
  $.when(callAjaxFunction("{{ route('driverList')}}"+"/"+driver_id,"GET","")).then(function(response) {
    var driver = response.driver;
    $(".driverClass").empty();
    $('#single_record tbody').html("");
    $('.modal-title').text('Serial No: '+driver.driver_id);
    $('.seller').text('Driver: '+driver.fullname);
    $('.selermobile').text('Mobile: '+driver.mobile);
    $('.seleremail').text('Email: '+driver.email);
    $('.amount').text(driver.wallet_amount);
    $('.dprogress').text(driver.deal_in_progress);
    $('.dcompleted').text(driver.deal_completed);
    var status_class = '';
    if (driver.systemstatus.name == 'inactive') { status_class = 'bg-info'; }
    else if (driver.systemstatus.name == 'active') { status_class = 'bg-success'; }

    $('.status').addClass(status_class);                   
    $('.status').text(driver.systemstatus.name);
    $('.driverClass').attr('id','icon_model-'+driver.driver_id); 
    $('.statechange').attr('id',driver.driver_id);
    if(driver.is_verified == 0){ $('#icon_model-'+driver.driver_id).append('<button type="button" id="btn-'+driver.driver_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+driver.driver_id+'"  class=" btn btn-outline-primary verify_driver">Verify</button>'); }
    else{ $('#icon_model-'+driver.driver_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">'); }

  });
} 
    $('td').delegate('.verify_driver', 'click',function (e){
                e.preventDefault();
               var driver_id = $(this).val();
                $('.confirm_driver').attr('id','rowcheck-'+driver_id);
              $('#verify_driver_modal').modal('show');
   }); 
   function verifyDriver(data){
  // console.log(data);
  
        var driver_id = data.id;
      
        if($(this).hasClass('statechange')){
          driver_id=driver_id.replace('modalcheck-','');
            alert( driver_id);
        }else{
          driver_id=driver_id.replace('rowcheck-','');
        }
        // alert(driver_id);
        $('#deletestudentmodal').modal('hide');

        // alert(listing_id);
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
                  url: "{{ route('driververification') }}",
                  type: "POST",
                  data: {"id": driver_id, "_token": "{{csrf_token()}}"},
                  datatype: "json",
               
                  success:function (response){
                    if (response.status == 200){
                          
                            if($(this).hasClass('statechange')){
            
                            }else{
                              var driver= response.driver;
                              if(driver.is_verified == 1){
                                alert('Verified Buyer');
                                $('.modal-backdrop').remove();
                                $(`#btn-${driver_id}`).hide();       
                                $(`#btn-${driver_id}`).addClass('d-none');       
                        
                                $('#check_icon-'+driver_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                                $('#icon_model-'+driver_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                                // $('#rowcheck-'+buyer_id).prop('checked',true);
                              }else{

                                
                              }
                                
                            }
                    }
                }
        });
}
$('.add_driver').on("click", function(){
    $("#add_drivers").modal("show");
    $('#add_drivers').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });
 });
 $('#add_driver_data').submit(function(e){
  
    e.preventDefault();
    $.ajax({
            url:  "{{ route('add_driver')}}",
		        type: "POST",
		        data: $(this).serialize(),
		        datatype: "json",
                success:function (response){
                    if (response.status == 200){
                        $('#add_drivers').modal('hide');
                        $('#add_drivers').on('hidden.bs.modal', function () {
                        $(this).find('form').trigger('reset');
                        // window.location = route('driverList');
                        });
                      alert(response.message); }
                    else{
                        $('#add_drivers').modal('show');
                        alert(response.message);
                    }
                }
        }); 

 });

 
function edit_driver(data){
  var driver_id = data.id;
  $('#edit_driver').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
  })
  $.ajax({
            url: "{{ route('driverList')}}"+"/"+driver_id,
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",               
            success:function (response){
                var driver = response.driver;  
              //  console.log(driver);         
                  $('#fullname1').val(driver.fullname);
                  $('#fullname_ar1').val(driver.fullname_ar);
                  $('#email1').val(driver.email);
                  $('#iqama_cr_no1').val(driver.iqama_cr_no);
                  $('#mobile1').val(driver.mobile);
                  $('#driver_id').val(driver.driver_id);
                   
                  $('#buyer_id').val(driver.buyer.buyer_id);
            }
  });
}

$(document).on('click', '.Update_drivers', function (e){
  e.preventDefault();
  var driver_id = $('#driver_id').val();
  var fullname = $('#fullname1').val();
  var fullname_ar = $('#fullname_ar1').val();
  var email = $('#email1').val();
  var iqama_cr_no = $('#iqama_cr_no1').val();
  var mobile = $('#mobile1').val();
  var buyer_id = $('#buyer_id').val();
//  alert(mobile);
  var driver = {
    'fullname':fullname,
    'fullname_ar':fullname_ar,
    'email':email,
    'iqama_cr_no':iqama_cr_no,
    'buyer_id':buyer_id,
    'mobile':mobile
  }
  $('#edit_driver').modal('hide');
  
  $.ajax({
    url: "{{ route('driver_update')}}"+"/"+driver_id,
    type: "POST",
    data: {driver, "_token": "{{csrf_token()}}"},
    datatype: "json",
    success:function (response){
        if (response.status == 200){ alert(response.message); }
    }
  });
});

//seller
$('.adding_recharge').on("click", function(){
                      // $('.set_id').attr('id',seller.seller_id);
  $('#url').val('seller');

  // $('#add_recharges').html();
  var id=$(this).attr('id'); 
  $('#seller_id_recharge').val(id);            


    $("#add_recharges").modal("show");
    $('#add_recharges').on('hidden.bs.modal', function () {
    $('#add_recharges form')[0].reset();
    });
   

});
   $('.add_recharge').on("click", function(e){
    e.preventDefault();
    
    if($('#url').val() == 'buyer'){
      // alert('hi buyer');

      var buyer_id = $('#seller_id_recharge').val();
      var wallet_amount = $('#recharge').val();
      $.ajax({
            url: "{{ route('add_recharges')}}"+"/"+buyer_id,
		        type: "POST",
		        data: {'wallet_amount': wallet_amount, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
           
                   var wallet_amount = response.buyer.wallet_amount;
                      
                   $('.wallet_amount').html(wallet_amount);
                   $('.amount').html(wallet_amount);
                     
                   
                    
                
                }
        });
    }else if($('#url').val() == 'seller'){
      // alert('hi seller');
      var seller_id = $('#seller_id_recharge').val();
      var wallet_amount = $('#recharge').val();
      $.ajax({
            url: "{{ route('add_recharge')}}"+"/"+seller_id,
		        type: "POST",
		        data: {'wallet_amount': wallet_amount, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
           
                   var wallet_amount = response.seller.wallet_amount;
              
                   $('.wallet_amount').html(wallet_amount);
                   $('.amount').html(wallet_amount);
                     
                   
                    
                
                }
        });
    }
    $('#add_recharges').modal('hide');
     
      
   });

   //buyer recharge
   $('.adding_recharges').on("click", function(){
                      // $('.set_id').attr('id',seller.seller_id);
   
                      // $('#add_recharges').html();
                      var id=$(this).attr('id'); 
                      $('#seller_id_recharge').val(id);            
                      $('#url').val('buyer');
    // $('#add_recharges').modal('show');
    // $("#add_recharges").modal("show");
    $("#add_recharges").modal("show");
    $('#add_recharges').on('hidden.bs.modal', function () {
    $('#add_recharges form')[0].reset();
});
// $('.add_recharge').on("click", function(e){
//     e.preventDefault();
    
    
    
//     var seller_id = $('#seller_id_recharge').val();
//     var wallet_amount = $('#recharge').val();
//       // if(recharge == null && recharge == ""){
        
//       // }
     
//          $('#add_recharges').modal('hide');
//          $.ajax({
//             url: "{{ route('add_recharge')}}"+"/"+seller_id,
// 		        type: "POST",
// 		        data: {'wallet_amount': wallet_amount, "_token": "{{csrf_token()}}"},
// 		        datatype: "json",
               
//                 success:function (response){
           
//                    var wallet_amount = response.seller.wallet_amount;
              
//                    $('.wallet_amount').html(wallet_amount);
//                    $('.amount').html(wallet_amount);
                     
                   
                    
                
//                 }
//         });
//    }); 

   });
   

   // Complaint
function showcomplaint(data){
    var complaint_id = data.id;
    $.when(callAjaxFunction("{{ route('complaint_list')}}"+"/"+complaint_id,"GET","")).then(function(response) {
      var complaint = response.complaint;
      // console.log(complaint.complaintAgainstOP.ref_no);
      var history = response.complaint_history;
      var complaint_comments = response.complaint_comments;

        
      $('#exampleModalLabel').text('Complaint No: '+complaint.complaint_no);
      $('#item_source').text(complaint.item_source);
      $('#complaint_type').text(complaint.complaint_type);
      $('#complaint_text').text(complaint.complaint_text);
      $('#seller').text(complaint.seller.fullname);
      var status_class = '';
      if (complaint.systemstatus.name == 'inactive') { status_class = 'bg-info'; }
      else if (complaint.systemstatus.name == 'active') { status_class = 'bg-success'; }

      $('.status').addClass(status_class); 
      $('.status').text(complaint.systemstatus.name);

      var priority = ''; var color = ''; var title = '';
      if (complaint.complaint_priority == 'High') { priority = 'fa fa-file-arrow-up-fill'; color = 'green'; title = 'High'; }
      else if (complaint.complaint_priority == 'Low') { priority = 'fa fa-file-arrow-down-fill'; color = 'yellow'; title = 'Low'; }
      else if (complaint.complaint_priority == 'Urgent') { priority = 'fa fa-exclamation-triangle'; color = 'red'; title = 'Urgent'; }
      else if (complaint.complaint_priority == 'Normal') { priority = 'fa fa-exclamation'; color = 'black'; title = 'Normal'; }

      $('.icons').addClass(priority);
      $('#color').css('color', color);
      $('#color').prop('title', title);
      var comment_data = '';
      $.each(complaint_comments, function(index, element) {
        // console.log(element.images);
        if(element.created_source == "seller"){ comment_data +='<div class="d-flex justify-content-between"><p class="small mb-1"></p><p class="small mb-1 text-muted">'+element.created_at+'</p></div><div class="d-flex flex-row justify-content-start"><span style="width: 45px; height: 100%;"><i style="color:green" class="bi bi-person-fill"></i></span><div> <p class="small p-2 ms-3 mb-3 rounded-3" style="background-color: green; color:#eee">'+element.comment_text+'</p> </div> </div><table style = "margin-top: -2%;" id = "images_listing_'+element.complaint_id+'"> </table>'; }
        else{ comment_data += '<div class="d-flex justify-content-between"> <p class="small mb-1 text-muted">'+element.created_at+'</p> <p class="small mb-1"></p> </div> <div class="d-flex flex-row justify-content-end mb-4 pt-1"> <div> <p class="small p-2 me-3 mb-3 text-white rounded-3" style="background-color: blue">'+element.comment_text+'</p> </div> <span><i style="color:blue" class="bi bi-person-fill"></i></span><table id = "images_listing_'+element.complaint_id+'"> </table>  </div>'; }
        $("#comments_data").html(comment_data);
        showImagesDetails(element.images, "total_images_"+element.complaint_id, "images_listing_"+element.complaint_id);
      });

    });
}
  function status(data){
    var complaint_id = data.id;
    $('.confirm_status').attr('id',complaint_id);  
  }
  function unActive(data){
    var complaint_id = data.id;
    let reason = $('#reason').val();
    $('#unactive_status').modal('hide');
    $.ajax({
            url: "{{ route('status_change')}}"+"/"+complaint_id,
            type: "GET",
            data: {'reason': reason, "_token": "{{csrf_token()}}"},
            datatype: "json",
            success:function (response){
              let complaint = response.status_change;
              if(response.status == 200){
                alert('Close Complaint Successfully');
                $('#status-'+complaint.complaint_id).removeClass('bg-success');
                $('#status-'+complaint.complaint_id).addClass('badge bg-info');
                $('#status-'+complaint.complaint_id).text('Inactive');
                $('#icon-'+complaint.complaint_id).hide();
              }
            }
    });  
  }


</script>

