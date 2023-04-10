@if (isset($seller))
<div class="modal fade" id="deletestudentmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style=" z-index: 9999;">
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
                <button type="button" class="btn btn-primary confirm_btn"  id="rowcheck-{{$seller->seller_id}}" onclick="seller_status(this)">Yes</button>
            </div>
        </div>
    </div>
  </div> 
  
  
  
  <div class=" modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <p class="card-text fw-bold  seleremail"></p>
                                <p class="card-text fw-bold  otp"></p>
                            </div><div class="mt-md-0 mt-2 flex-fill">
                               <br><br><br>
                                <div class="d-flex justify-content-between align-items-end">
                                    <p><strong>Status</strong></p>
                                    <p class="badge {{ $status_class }} status"></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <p><strong>Type</strong></p>
                                    <p class="type "></p>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <hr class="invoice-spacing mb-0">
                   
                    <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th class="py-1">Deal Progress </th>
                                  <th class="py-1">Amount</th>
                                  <th class="py-1">Deal Completed</th>
                                  
                                 
                                 
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td class="py-1"><p class="card-text mb-25 dprogress"></p></td>
                                  <td class="py-1"><span class=" amount"></span>
                                    @if (in_array("recharge", Session::get('roles')))
                                    <i class="fa fa-plus-circle adding_recharge set_id" style="margin-left:1%;" id="" title="Add Recharge"></i>                                  </td>
                                        
                                    @endif
                                  <td class="py-1"><span class="dcompleted"></span></td>
                                
                              </tr>
                             
                              </tbody>
                          </table>
                        
                     
                        <table class="table">
                          <thead>
                              <tr>
                              <th class="py-1">Verified</th>
                            
                          </tr>
                      </thead>
                      <tbody>
                          
                          <tr>
                            <td class="toggleClass" id="icon_model-{{ $seller->seller_id }}">
                                {{-- @if ($seller->is_verified == 1)
                                
                                <img src="{{ url('/') }}/assets/img/shield-tick.png" alt="" style="height: 20px">
  
                                    @else
                                    <label class="switch">
                                      <input type="checkbox" class="statechange" value="{{ $seller->is_verified }}" id="modalcheck-{{$seller->seller_id}}"  onchange="checkboxChange(this)" {{ $seller->is_verified==1 ? 'checked' : '' }} class="getvalue">
                                      <span class="slider round"></span>
                                    </label>
                                @endif
                                  --}}
                                  
                               
                                
                           
                             
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