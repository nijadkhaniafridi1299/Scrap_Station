
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
                  //     // console.log(listing);
                  $(".toggleClass").empty();
                  $(".status").removeClass("bg-success bg-warning bg-danger bg-info");
                      $('#single_record tbody').html("");

                      $('.modal-title').text('Offer No: '+offer.offer_no);
                      $('.price').text(offer.offered_price+'SAR');
                      $('.tprice').text(offer.offered_price_with_vat+'SAR');
                      $('.seller').text('Seller: '+offer.seller.fullname);
                      $('.selermobile').text('Mobile Number: '+offer.seller.mobile);

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
                      
                      $('.status').addClass(status_class);
                      $('.status').text(offer.systemstatus.name);

                      $('.toggleClass').attr('id','icon_model-'+offer.offer_id);                   

                  //     $('.statechange').attr('id',order.order_id);
                  //     if(order.is_verified == 0){
                  //       $('.statechange').prop('checked',false);
                  //     }
                  //     else{
                  //       $('.statechange').prop('checked',true);

                  //     }
                  //     $('.weight').text(order.quantity+'('+order.quantity_unit+')');
                   
                  $('.statechange').attr('id',offer.offer_id);
                      if(offer.is_verified == 0){
                        $('#icon_model-'+offer.offer_id).append('<button type="button"  id="btn-'+offer.offer_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;"  value="'+offer.offer_id+'" onclick="showModel(this)"  class=" btn btn-outline-primary verified_buttion">Verify</button>');
                        $('.statechange').prop('checked',false);
                      }
                      else{
                        $('#icon_model-'+offer.offer_id).append('<img  src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 40px">');


                      }
                    
                
                }
        });
}

    
