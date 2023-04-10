
@if (isset($payment))
    

<div class="modal fade" id="paymentconfirmation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style=" z-index: 9999;">
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
                <button type="button" class="btn btn-primary confirm_btn"  id="rowcheck-{{$payment->pay_id}}" onclick="paymentStatus(this)">Yes</button>
            </div>
        </div>
    </div>
  </div>


<div class=" modal fade" id="paymentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                              <div class="mb-0"><h6 class="mb-0 fw-bold seller"></h6></div>
                              <p class="card-text fw-bold  selermobile"></p>
                              <div class="mb-0"><h6 class="mb-0 fw-bold buyer"></h6></div>
                              <p class="card-text fw-bold  buyermobile"></p>
                              <div class="mb-0"><h6 class="mb-0 fw-bold driver"></h6></div>
                              <p class="card-text fw-bold  drivermobile"></p>
                          </div><div class="mt-md-0 mt-2 flex-fill">
                             <br><br><br>
                              <div class="d-flex justify-content-between align-items-end">
                                  <p><strong>Status</strong></p>
                                  <p class="badge {{ $status_class }} status"></p>
                              </div>
                              <div class="d-flex justify-content-between align-items-end">
                                <p><strong>Source</strong></p>
                                <p class="sellerlisting"></p>
                              </div>
                              {{-- <div class="d-flex justify-content-between align-items-end">
                                <p><strong>Listing No</strong></p>
                                <p class="listingno"></p>
                              </div> --}}
                              <div class="d-flex justify-content-between align-items-end">
                                <p><strong>Order No</strong></p>
                                <p class="orderno"></p>
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
                                  <th class="py-1">Price</th>
                                  <th class="py-1">Total Price</th>
                                  
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td class="py-1"><p class="card-text mb-25 material"></p></td>
                                  <td class="py-1"><span class=" weight"></span></td>
                                  <td class="py-1"><span class=" price"></span></td>
                                  <td class="py-1"><span class="tprice"></span></td>
                              </tr>
                             
                              </tbody>
                          </table>
                      </div>
                      <hr class="invoice-spacing mb-0">
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
                            {{-- <th class="py-1">Verified</th> --}}
                          
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td class="paymentClass" id="icon_payment-{{ $payment->pay_id}}">
                            {{-- <label class="switch">
                            <input type="checkbox" class="statechange" value="{{ $order->is_verified }}" id="modalcheck-{{$order->order_id}}"  onchange="checkboxChange(this)" {{ $order->is_verified==1 ? 'checked' : '' }} class="getvalue">
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
  @endif