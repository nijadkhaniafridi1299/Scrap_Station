{{-- @if (isset($buyerlisting)) --}}

<div class="modal fade" id="buyerListingmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style=" z-index: 9999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
  
            <div class="modal-body">
              
                <h4>Are you sure? want to verified this</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary confirm_btn"  id="rowcheck-" onclick="buyerListingVerified(this)">Yes</button>
            </div>
        </div>
    </div>
  </div>


<div class=" modal fade" id="buyerLising" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold serial_no" id="exampleModalLabel" style=""></h5>
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
                              <div class="mb-0"><h6 class="mb-0 fw-bold seller"></h6></div>
                              <p class="card-text fw-bold  selermobile"></p>
                          </div><div class="mt-md-0 mt-2 flex-fill">
                             <br><br><br>
                              <div class="d-flex justify-content-between align-items-end">
                                  <p><strong>Status</strong></p>
                                  <p class="badge status  statuslisting"></p>
                              </div>
                              <div class="d-flex justify-content-between align-items-end close_reson">
                             
                              </div>           
                             
                          </div>
                      </div>
                  </div>
                  <hr class="invoice-spacing mb-0">
                  <h6 class="mt-1 mb-1 pb-0 pt-0 text-center">Materials </h6>
                  <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th class="py-1">Material Name </th>
                                  <th class="py-1">Weight</th>
                                  <th class="py-1">Subtotal</th>
                                  <th class="py-1 ">Total</th>
                                 
                                 
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td class="py-1"><p class="card-text mb-25 material"></p></td>
                                  <td class="py-1"><span class=" weight"></span></td>
                                  <td class="py-1"><span class="price"></span></td>
                                  <td class="py-1"><span class="total"></span></td>
                              </tr>
                             
                              </tbody>
                          </table>
                      </div>
                      <div id="total_images">
                       
                      </div>
                      <h6 class="mt-1 mb-1 pb-0 pt-0 text-center fw-bold">Address </h6>
                     
                      <table class="table">
                          <thead>
                              <tr>
                              <th class="py-1">Address</th>
                            
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td class="py-1"><p class="card-text  mb-25 address"></p></td>
                      
                          </tr>
                      </tbody>
                      <table class="table">
                        <thead>
                            <tr>
                            <th class="py-1">Verified</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td class="buyerlistingClass" id="icon_buyerlisting-">
                            {{-- <label class="switch">
                            <input type="checkbox" class="statechange" value="{{ $listing->is_verified }}" id="modalcheck-{{$listing->sell_list_id}}"  onchange="checkboxChange(this)" {{ $listing->is_verified==1 ? 'checked' : '' }} class="getvalue">
                            <span class="slider round"></span>
                          </label> --}}
                        </td>
                    
                        </tr>
                    </tbody>
                  </table>
              </div>
          </div>     
      </div>
    </div>
  </div>
  {{-- @endif --}}