<div class="modal fade" id="unactive_status" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style=" z-index: 9999;">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Complaint</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            
              <h4>Are you sure? want to Close this Complaint</h4>
              <div class="col">
                <input type="text" maxlength="30" minlength="5" class="form-control input_Length" placeholder="Reason" value="" name="reason" id="reason" required>
                <div class="lengthmessage">
  
                </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary confirm_status"  id="" onclick="unActive(this)">Yes</button>
          </div>
      </div>
  </div>
</div> 

<div class=" modal fade" id="complaint_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold" id="exampleModalLabel" style=""></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="p-3 pt-0 modal-body">
          <div class="invoice-preview-card card">
              <div class="invoice-padding pb-0 card-body">
                  <div class="d-flex  justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                      <div class="flex-fill">
                          <div class="logo-wrapper">
                              <h3 class="text-primary invoice-logo mb-25"></h3>
                              <p class="mb-25  card-text" style="width: 90%;">
                              
                              </div>
                              <div class="mb-0"><h6 class="mb-0 fw-bold ">Seller: <span id="seller"></span></h6></div>
                              <p class="card-text fw-bold  selermobile"></p>
                              <div class="mb-0"><h6 class="mb-0 fw-bold">Source: <span id="item_source"></span></h6></div>
                              <p class="card-text fw-bold ">Type: <span id="complaint_type"></span><i id="color"  class="  icons"></i></p>
                              <div class="mb-0"><h6 class="mb-0 fw-bold driver_data ">Complaint: <span id="complaint_text"></span></h6></div>
                             
                          </div><div class="mt-md-0 mt-2 flex-fill">
                             <br><br><br>
                              <div class="d-flex justify-content-between align-items-end">
                                  <p><strong>Status</strong></p>
                                  <p class="badge status"></p>
                              </div>
                              {{-- <div class="d-flex justify-content-between align-items-end">
                                <p><strong>Source</strong></p>
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#buyerLising"><p class="sellerlisting"></p></a>
                            </div> --}}
                             
                          </div>
                      </div>
                  </div>
                  <hr class="invoice-spacing mb-0">
                
               
                    
                  <div class="card" style="overflow-y: scroll">
                    <div class="card-header d-flex justify-content-between align-items-center p-3"
                      style="border-top: 4px solid #ffa900;">
                      <h5 class="mb-0">Discussion</h5>
                      <div class="d-flex flex-row align-items-center" >
                        <span class="badge bg-warning me-3" id="messagelenght"></span>
                        <i class="fas fa-minus me-3 text-muted fa-xs"></i>
                        <i class="fas fa-comments me-3 text-muted fa-xs"></i>
                        <i class="fas fa-times text-muted fa-xs"></i>
                      </div>
                    </div>
                    <div id="comments_data">

                    </div>
                  
                  </div>
              </div>
          </div>     
      </div>
    </div>
  </div>