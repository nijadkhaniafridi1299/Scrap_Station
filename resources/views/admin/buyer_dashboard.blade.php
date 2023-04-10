@extends('layout.app')
@section('content')
    <div class="content-wrapper container-xxl p-0 animate__animated animate__fadeIn">
        <div class="content-body">
          <div class="app-user-view">
           <div class="row">
             <div class="order-1 col-md-5 order-md-0 col-lg-5 col-xl-4">
              <div class="card">
                  <div class="card-body"><div class="user-avatar-section">
                      <div class="d-flex align-items-center flex-column">
                          <div class="avatar rounded mt-3 mb-2 bg-light-success" style="height: 110px; width: 110px;">
                              <span class="avatar-content" style="border-radius: 0px; font-size: calc(48px); width: 100%; height: 100%;">@php foreach(explode(" ",$buyer->fullname) as $ft) {echo strtoupper($ft[0]);} @endphp </span>
                          </div>
                          <div class="d-flex flex-column align-items-center text-center">
                          <div class="user-info">
                              <h4 class="userfullname" id="buyer_status"><span class="fw-bold text-capitalize">{{ $buyer->fullname }}</span>
                                @if ($buyer->is_verified == 1) 
                                <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                                @endif
                                  
                              </h4>
                              <span class="text-capitalize badge bg-light-primary">Company</span>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="d-flex justify-content-between my-2 pt-75">
                      <div class="d-flex flex-column align-items-start me-2">
                          <div class="ms-75"><h4 class="mb-0 wallet_amount">{{ $buyer->wallet_amount }}</h4></div>
                          <span class="rounded p-75 badge bg-light-primary">
                          </span><small>Wallet Amount</small>
                          @if (in_array("recharge", Session::get('roles')))
                          <i class="bi bi-plus-circle-fill adding_recharges" id="{{ $buyer->buyer_id }}" title="Add Recharge"></i>
                              
                         @endif
                      </div>
                      
                  <div class="d-flex flex-column align-items-start">
                     <div class="ms-75"><h4 class="mb-0">{{ $buyer->deal_in_progress }}</h4></div>
                     <span class="rounded p-75 badge bg-light-primary">
                     </span>
                     <small>Deal Progress</small>
                  </div>
                  <div class="d-flex flex-column align-items-start me-2">
                  <div class="ms-75"><h4 class="mb-0">{{ $buyer->deal_completed }}</h4></div>
                     <span class="rounded p-75 badge bg-light-primary">
                     </span><small>Deal Completed</small>
                  </div>
              </div>
              <h4 class="fw-bolder border-bottom pb-50 mb-1">Details</h4>
              <div class="info-container">
                <ul class="list-unstyled">
                      <li class="mb-75">
                      <span class="fw-bolder me-25">Email:</span>
                      <span>{{ $buyer->email }}</span>
                      </li>
                      <li class="mb-75">
                          <span class="fw-bolder me-25">Status:</span>
                          @php
                          $status_class = '';
                          if ($buyer->systemstatus->name == 'inactive') {
                              $status_class = 'bg-info';
                          }elseif ($buyer->systemstatus->name == 'active') {
                              $status_class = 'bg-success';
                          }
                          @endphp
                          <span class=" badge {{  $status_class }} ">{{ $buyer->systemstatus->name}}</span>
                      </li>
                    
                      <li class="mb-75">
                          <span class="fw-bolder me-25">Mobile #:</span>
                          <span>{{ $buyer->mobile }}</span>
                      </li>
                      <li class="mb-75">
                       
                        <span>{{ $buyer->addressdetail->address ?? ""}}</span>
                    
                    </li>
                    <li class="mb-75">
                      <span class="fw-bolder me-25">Type:</span>

                      <span>{{ $buyer->type ?? ""}}</span>
                  
                  </li>
                  </ul>
              </div>
             <div class="d-flex justify-content-center pt-2 mr-1" style="">
                @if($buyer->is_verified == 0)
                <button type="button" id="buyer_btn-{{$buyer->buyer_id}}" style="margin-right: 3%;" value="{{$buyer->buyer_id}}"  class=" btn btn-outline-primary btn-sm  verified_buyer">Verify</button>

                @endif
                  <button type="button" class="btn btn-primary btn-sm buyer_edit">Edit</button>



                  {{-- @if($seller->is_verified == 0)
                  <label class="switch">
                    <input type="checkbox" value="{{ $seller->is_verified }}" id="rowcheck-{{$seller->seller_id}}"  onchange="checkboxChange(this)" {{ $seller->is_verified==1 ? 'checked' : '' }} class="getvalue">
                    <span class="slider round"></span>
                  </label>
                  @endif --}}
              </div>
          </div>
      </div>
      </div>
      <div class="modal fade" id="sellerEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title1" id="exampleModalLabel">Edit buyer</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="edit_seller_form">
                <div class="row">
                  <div class="col">
                    <input type="text" maxlength="30" minlength="5" class="form-control input_Length" value="{{ $buyer->fullname }}" placeholder="Enter Name" name="fullname" id="fullname" required>
                    <div class="lengthmessage">
                     @csrf
                    </div>
                  </div>
                  <div class="col">
                    <input type="text" maxlength="30" minlength="5" class="form-control input_Length" placeholder="Enter name_ar" value="{{ $buyer->fullname_ar }}" name="fullname_ar" id="fullname_ar" required>
                    <div class="lengthmessage">

                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col">
                    <input type="email" maxlength="30" minlength="5" class="form-control input_Length" value="{{  $buyer->email  }}" placeholder="Enter Email" name="email" id="email" required>
                    <div class="lengthmessage">

                    </div>
                  </div>
                  <div class="col">
                    <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="status" id="status" required>
                
                      @foreach ($systemstatus as $key => $val)
                      <option value="{{ $val->id }}" {{ (collect(old('options'))->contains($val->id)) ? 'selected':'' }}>{{ $val->name }}</option>
                  
                      @endforeach
                    </select>
                  </div>
                </div><br>
                <div class="row">
                  <div class="col">
                    <input type="text" maxlength="30" minlength="5" class="form-control" value="{{ $buyer->iqama_cr_no }}" placeholder="Enter Iqma Cr NO" name="iqama_cr_no" id="iqama_cr_no" required>
                    <div class="lengthmessage">

                    </div>
                  </div>
                  <div class="col">
                    <input type="file" class="form-control" placeholder="Enter Image">
                  </div>
                </div><br>
                <div class="row">
                  <div class="col">
                    <input type="text" maxlength="30" minlength="5" class="form-control input_Length"  value="{{ $buyer->mobile }}" placeholder="+92*++++++*" name="mobile" id="mobile" required>
                    <div class="lengthno">

                    </div>
                  </div>
                  <div class="col">
                    <input type="hidden" class="form-control input_Length" value="{{ $buyer->buyer_id }}"  name="buyer_id" id="buyer_id" required>
                  
                  </div>
                </div>
             
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary update_buyer">Update</button>
            </div>
          </div>
        </form>
        </div>
      </div>




     @include('admin/modals/buyerModal')   

      <div class="order-0 col-md-7 order-md-1 col-lg-7 col-xl-8">
        <ul class="mb-2 nav nav-pills">
           
              <li class="nav-item">
                  <a class="nav-link active"  data-bs-toggle="tab" href="#seller_listing">
                      <span class="fw-bold">Buyer Listing</span>
                  </a>
              </li>
           
              <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="tab" href="#order">
                     <span class="fw-bold">Orders</span>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="tab" href="#payment">
                    <span class="fw-bold">Payments</span>
                  </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#driver">
                  <span class="fw-bold">Driver</span>
                </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#address">
                <span class="fw-bold">Address</span>
              </a>
          </li>
          </ul>
         <div class="tab-content">
            <div class="tab-pane " id="chat">
                  <div class="chat-app-window">
                      
                      <div class="card">
                        
                   
                          <div id="cbody" class="card-body" style="height: 290px; overflow-y: auto;">
                            
                        <div>
                      </div>
                    
                  </div>
              </div>
          </div>
    </div>
   


      <div class="tab-pane active" id="seller_listing" class="container tab-pane fade">
          <div class="card">
           
            <div class="react-dataTable user-view-account-projects">
                 <div class="sc-fznJRM gRcmCX">
                    <div class="sc-fzoiQi finKZY">
                        <div class="sc-AxjAm iazphd rdt_Table" role="table">
                            <div class="sc-fzqNqU YAOvq">
                                <div class="table_responsive">
                                  <table id="datatable" class="table table-striped" style="width:100%; ">
                                    <thead>
                                        <tr>
                                            <th>Listing No</th>
                                            <th>Seller</th>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Total</th>
                                            <th>Update By</th>
                                            <th>Status</th>
                                            <th>Verified</th>
                                           
                                           
                                        </tr>
                                    </thead>
                                  <tbody>
                                        @foreach ($listings as $buyerlisting)
                                       
                                        @php
                                        $status_class = '';
                                        if($buyerlisting->systemstatus->name == 'in progress'){
                                          $status_class = 'bg-info';
                                        }elseif($buyerlisting->systemstatus->name == 'pending'){
                                          $status_class = 'bg-warning';
                                        }elseif($buyerlisting->systemstatus->name == 'rejected'){
                                          $status_class = 'bg-danger';
                                        }elseif($buyerlisting->systemstatus->name == 'active'){
                                          $status_class = 'bg-success';
                                        }elseif($buyerlisting->systemstatus->name == 'opened'){
                                          $status_class = 'bg-success';
                                        }
                                        elseif($buyerlisting->systemstatus->name == 'accepted'){
                                          $status_class = 'bg-success';
                                        } elseif($buyerlisting->systemstatus->name == 'closed'){
                                          $status_class = 'bg-danger';
                                        }
                                     
                                        @endphp
                                       
                                        <tr>
                                          <td>
                                          <a data-bs-toggle="modal" data-bs-target="#buyerLising" href="javascript:void(0)" id="{{$buyerlisting->buyer_list_id}}" onclick="showListing(this)">  {{ $buyerlisting->listing_no }}</a>
                                          </td>
                                          <td>{{ $buyerlisting->buyer->fullname }}</td>
                                            <td>{{ $buyerlisting->material->name}}</td>
                                            <td>{{ $buyerlisting->quantity." (".$buyerlisting->quantity_unit.")"  }}</td>
                                            <td>{{ $buyerlisting->expected_price_per_unit." (per ".$buyerlisting->quantity_unit.") "}} SAR</td>
                                            <td>{{ $buyerlisting->expected_price}} SAR</td>
                                            @if ( $buyerlisting->updated_by != null &&  $buyerlisting->updated_by != '')
                                            <td>{{ $buyerlisting->updatedByPerson->fullname }} {{  $buyerlisting->updated_at }}</td>
                                                @else
                                                <td></td>
                                            @endif
                                            <td><span class="badge {{ $status_class }}">{{ $buyerlisting->systemstatus->name}}</span></td>
                                            <td id="list_icon-{{$buyerlisting->buyer_list_id}}" style="text-align: center;">
                                              @if ($buyerlisting->is_verified == 1)
                                              <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                              
                                                  @else
                                                  <button type="button" id="btn-{{$buyerlisting->buyer_list_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$buyerlisting->buyer_list_id}}"  class=" btn btn-outline-primary verified_buyerListing">Verify</button>
                              
                                              @endif
                                              
                                              
                                              {{-- <label class="switch">
                                                <input type="checkbox" value="{{ $listing->is_verified }}" id="rowcheck-{{$listing->sell_list_id}}"  onchange="checkboxChange(this)" {{ $listing->is_verified==1 ? 'checked' : '' }} class="getvalue">
                                                <span class="slider round"></span>
                                              </label>
                                            </td> --}}
                                           
                                        </tr>
                                        @endforeach
                                    </tbody>
                             
                                </table>
                                @include('admin/modals/buyerlistingModal')

                                 </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
     
      <div class="tab-pane" id="order" class="container tab-pane fade">
        <div class="card">
          
              <div class="react-dataTable user-view-account-projects">
                 <div class="sc-fznJRM gRcmCX">
                      <div class="sc-fzoiQi finKZY">
                          <div class="sc-AxjAm iazphd rdt_Table" role="table">
                              <div class="sc-fzqNqU YAOvq">
                                      <div style="padding: 24px; overflow-x:auto;">
                                        <table id="datatable1" class="table table-striped" style="width:100%">
                                          <thead>
                                              <tr>
                                                  <th>Order No</th>
                                                  <th>Material</th>
                                                  <th>Quantity</th>
                                                  <th>Price</th>
                                                  <th>Total Price</th>
                                                   <th>Update By</th>
                                                  <th>Status</th>
                                                  <th>Verified</th>
                                                 
                                                 
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
                                            }elseif($order->systemstatus->name == 'completed'){
                                              $status_class = 'bg-success';
                                            }elseif($order->systemstatus->name == 'rejected'){
                                              $status_class = 'bg-danger';
                                            }elseif($order->systemstatus->name == 'active'){
                                              $status_class = 'bg-success';
                                            }elseif($order->systemstatus->name == 'opened'){
                                              $status_class = '';
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
                                                <td>{{ $order->quantity." (".$order->quantity_unit.")"  }}</td>
                                                <td>{{ $order->price}} SAR</td>
                                                <td>{{ $order->total_price}} SAR</td>
                                                @if ( $order->updated_by != null &&  $order->updated_by != '')
                                                <td>{{ $order->updatedByPerson->fullname }} {{  $order->updated_at }}</td>
                                                    @else
                                                    <td></td>
                                                @endif
                                                <td><span class="badge {{ $status_class }}">{{ $order->systemstatus->name}}</span></td>
                                                
                                               <td id="order_icon-{{$order->order_id}}" style="text-align: center;">
                                                @if (  $order->is_verified  ==1)
                                                <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                                                    @else
                                                    <button type="button" id="btn-{{$order->order_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$order->order_id}}"  class=" btn btn-outline-primary verified_order">Verify</button>
                                                @endif
                                              </td>
                                               
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                      </table>
                                      </div>
                                      @include('admin/modals/order_Modal')

                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="tab-pane" id="payment" class="container tab-pane fade">
         <div class="card">
          
             <div class="react-dataTable user-view-account-projects">
                <div class="sc-fznJRM gRcmCX">
                    <div class="sc-fzoiQi finKZY">
                          <div class="sc-AxjAm iazphd rdt_Table" role="table">
                              <div class="sc-fzqNqU YAOvq">
                                  <div style="padding: 24px; overflow-x:auto;">
                                    <table id="datatable2" class="table table-striped" style="width:100%; overflow-x:auto;">
                                      <thead>
                                        <tr>
                                          <th>Payment NO</th>
                                          <th>Seller Name</th>
                                          <th>Order No</th>
                                          <th>Material</th>
                                          <th>Quantity</th>
                                          <th>Price</th>
                                           <th>Update By</th>
                                          <th>Status</th>
                                          {{-- <th>Verified</th> --}}
                                         
                                         
                                      </tr>
                                      </thead>
                                      <tbody>
                                          @foreach ($payments as $payment)
                                        
                                          @php
                                          $status_class = '';
                                          if($payment->systemstatus->name == 'in progress'){
                                            $status_class = 'bg-info';
                                          }elseif($payment->systemstatus->name == 'pending'){
                                            $status_class = 'bg-warning';
                                          }elseif($payment->systemstatus->name == 'rejected'){
                                            $status_class = 'bg-danger';
                                          }elseif($payment->systemstatus->name == 'active'){
                                            $status_class = 'bg-success';
                                          }elseif($payment->systemstatus->name == 'opened'){
                                            $status_class = 'bg-success';
                                          }
                                          elseif($payment->systemstatus->name == 'accepted'){
                                            $status_class = 'bg-success';
                                          }  elseif($payment->systemstatus->name == 'completed'){
                                            $status_class = 'bg-success';
                                          } elseif($payment->systemstatus->name == 'closed'){
                                            $status_class = 'bg-danger';
                                          }
                                       
                                          @endphp
                                         
                                          <tr>
                                            <td>
                                            <a data-bs-toggle="modal" data-bs-target="#paymentModal" href="javascript:void(0)" id="{{$payment->pay_id}}" onclick="showpayment(this)">  {{ $payment->pay_no }}</a>
                                            </td>
                                            <td>{{ $payment->order->offer->seller->fullname }}</td>
                                              <td>{{ $payment->order->material->name}}</td>
                                              <td>{{ $payment->order->order_no}}</td>
                                              <td>{{ $payment->order->quantity." (".$payment->order->quantity_unit.")"  }}</td>
                                              <td>{{ $payment->order->price." (per ".$payment->order->quantity_unit.") "}} SAR</td>
                                              @if ( $payment->updated_by != null &&  $payment->updated_by != '')
                                              <td>{{ $payment->updatedByPerson->fullname }} {{  $payment->updated_at }}</td>
                                                  @else
                                                  <td></td>
                                              @endif
                                              <td><span class="badge {{ $status_class }}">{{ $payment->systemstatus->name}}</span></td>
                                              {{-- <td id="payment_icon-{{$payment->pay_id}}" style="text-align: center;">
                                                @if (  $payment->is_verified  ==1)
                                                <img src="{{ url('/') }}/assets/img/shield-tick.png" alt="" style="height: 20px">
                                                    @else
                                                    <button type="button" id="btn-{{$payment->pay_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$payment->pay_id}}"  class=" btn btn-outline-primary verified_payment">Verify</button>
                                                @endif
                                              </td> --}}
                                             
                                          </tr>
                                          @endforeach
                                      </tbody> 
                                    
                                  </table>
                                  @include('admin/modals/paymentModal')
                                   </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="tab-pane" id="driver" class="container tab-pane fade">
        <div class="card">
          
              <div class="react-dataTable user-view-account-projects">
                 <div class="sc-fznJRM gRcmCX">
                      <div class="sc-fzoiQi finKZY">
                          <div class="sc-AxjAm iazphd rdt_Table" role="table">
                              <div class="sc-fzqNqU YAOvq">
                                      <div style="padding: 24px; overflow-x:auto;">
                                        <table id="driver_table" class="table table-striped" style="width:100%">
                                          <thead>
                                            <tr>
                                                <th>Serial No</th>
                                                <th>Driver Name</th>
                                                <th>Buyer Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Date</th>
                                                <th>Update By</th>
                                                <th>Status</th>
                                                <th>Verified</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                       
                                            @foreach ($driver  as $drivers)
                                            @php
                                            $status_class = '';
                                            if($drivers->systemstatus->name == 'inactive'){
                                              $status_class = 'bg-info';
                                            }elseif($drivers->systemstatus->name == 'active'){
                                              $status_class = 'bg-success';
                                            }
                                            
                                            @endphp
                                           
                                            <tr>
                                                <td>
                                                  <a data-bs-toggle="modal" data-bs-target="#driveModal" href="javascript:void(0)" id="{{$drivers->driver_id}}" onclick="showDriver(this)">  {{ $drivers->driver_id}}</a>
                                                </td>
                                                <td>{{ $drivers->fullname}}</td>
                                               
                                                <td>{{ $drivers->buyer->fullname ?? ""}}</td>
                                           
                                                <td>{{ $drivers->email  }}</td>
                                                <td>{{$drivers->mobile}} </td>
                                                 <td>{{ $drivers->created_at }}</td>
                                                  @if ( $drivers->updated_by != null &&  $drivers->updated_by != '')
                                                <td>{{ $drivers->updatedByPerson->fullname }} {{  $drivers->updated_at }}</td>
                                                    @else
                                                    <td></td>
                                                @endif
                                                <td><span class="badge {{ $status_class }}">{{ $drivers->systemstatus->name}}</span></td>
                                                           
                                              <td id="check_icon-{{$drivers->driver_id}}" style="text-align: center">
                                                @if ($drivers->is_verified == 1)
                                                <img src="{{asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                                
                                                    @else
                                                    <button type="button" id="btn-{{$drivers->driver_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$drivers->driver_id}}"  class=" btn btn-outline-primary verify_driver">Verify</button>
                                
                                                @endif
                                            </td>
                                                {{-- <td><label class="switch">
                                                    <input type="checkbox" value="{{ $buyers->is_verified }}" id="rowcheck-{{$buyers->buyer_id}}"  onchange="checkboxChange(this)" {{ $buyers->is_verified==1 ? 'checked' : '' }} class="getvalue">
                                                    <span class="slider round"></span>
                                                  </label>
                                                </td> --}}
                                               
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        
                                      </table>
                                      </div>
                                      @include('admin/modals/driverModal')

                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    
    </div>
    <div class="tab-pane" id="address" class="container tab-pane fade">
      <div class="card">
       
          <div class="react-dataTable user-view-account-projects">
             <div class="sc-fznJRM gRcmCX">
                 <div class="sc-fzoiQi finKZY">
                       <div class="sc-AxjAm iazphd rdt_Table" role="table">
                           <div class="sc-fzqNqU YAOvq">
                               <div style="padding: 24px; overflow-x:auto;">
                              
                                 <table id="datatable4" class="table table-striped" style="width:100%; overflow-x:auto;">
                                   <thead>
                                     <tr>
                                       <th>Address</th>
                                       {{-- <th>Seller Name</th> --}}
                                    
                                    

                                     
                                   
                                       {{-- <th>Verified</th> --}}
                                      
                                      
                                   </tr>
                                   </thead>
                                   <tbody>
                                       @foreach ($buyerAddress as $buyerAddresses)
                                      
                                      
                                       <tr>
                                        
                                             
                                           <td>{{ $buyerAddresses->address}}</td>
                                           {{-- <td>{{ $payment->order->order_no}}</td> --}}
                                      
                                          
                                       </tr>
                                       @endforeach
                                   </tbody> 
                                 
                               </table>
                             
                                </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
  </div></div></div></div></div>
 @include('admin/modals/rechargeModal')   




<script>
  $(document).ready(function () {
      
    });  $(document).ready(function () {
        $('#driver_table').DataTable({
          "order": [[ 3, "desc" ]], //or asc 
          "columnDefs" : [{"targets":5, "type":"date-eu"}],
      });
    });
    $(document).ready(function () {
      $('#datatable1').DataTable({
        "order": [[6, "desc" ]], //or asc 
        "columnDefs" : [{"targets":3, "type":"date-eu"}],
    });
  });   
   $(document).ready(function () {
      $('#datatable4').DataTable();
  });
  $(document).ready(function () {
      $('#datatable2').DataTable({
        "order": [[6, "desc" ]], //or asc 
        "columnDefs" : [{"targets":3, "type":"date-eu"}],
    });
  });
</script>
<script>
 $('.buyer_edit').on("click", function(){
 $('#sellerEdit').modal('show');
 });  
 $('#edit_seller_form').submit(function(e){
    e.preventDefault();
   
   
    
    $.ajax({
            url:  "{{ route('buyer.update')}}",
		        type: "POST",
		        data: $(this).serialize(),
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                        alert(response.message);
                      
                    }
                }
        }); 
 });
  // $('.update_buyer').on("click", function(){

  //     var buyer_id = $('#buyer_id').val();
  
  // var fullname = $('#fullname').val();
  // var fullname_ar = $('#fullname_ar').val();
  // var iqama_cr_no = $('#iqma').val();
 
  // var email = $('#email').val();
  // var mobile = $('#mobile').val();
  // var status = $('#status').val();
  
//   var data = {
//                  'fullname': fullname,
//                  'fullname_ar':fullname_ar,
//                  'iqama_cr_no':iqama_cr_no,
//                  'email':email,
//                  'mobile':mobile,
//                  'status':status,
//                 }
//                 // $('#sellerEdit').modal('hide');
  
 

           
//  }); 

//    $('td').delegate('.verified_buyer', 'click',function (e){
//                 e.preventDefault();
//                var buyer_id = $(this).val();
//                alert(buyer_id);
//                 $('.confirm_btn').attr('id','rowcheck-'+buyer_id);
            
//               $('#deletestudentmodal').modal('show');
//    }); 
   $( ".verified_buyer" ).on( "click", function(e) {
    e.preventDefault();
    var buyer_id = $(this).val();
              
              $('.confirm_btn').attr('id','rowcheck-'+buyer_id);
            
             $('#deletestudentmodal').modal('show');
});
function Buyer_verified(data){
  // console.log(data);
        var buyer_id = data.id;
        if($(this).hasClass('statechange')){
            buyer_id=buyer_id.replace('modalcheck-','');
            // alert( buyer_id);
        }else{
            buyer_id=buyer_id.replace('rowcheck-','');
        }
        $('#deletestudentmodal').modal('hide');

        // alert(listing_id);
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('buyer_verified.update') }}",
		        type: "POST",
		        data: {"id": buyer_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                       
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var buyer= response.buyer;
                          if(buyer.is_verified == 1){
                            alert('Verified Buyer');
                            // $('.modal-backdrop').remove();
                            
                            $(`#buyer_btn-${buyer_id}`).hide(); 
                            $(`#buyer_btn-${buyer_id}`).addClass('d-none'); 
                                  
                    
                            $('#buyer_status').append('<img src="{{asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            // {{asset('/assets/img/shield-tick.png')}}"
                            // $('#rowcheck-'+buyer_id).prop('checked',true);
                          }else{

                            
                          }
                            
                        }
                    }
                }
        });
}
$('td').delegate('.verified_buyerListing', 'click',function (e){
                e.preventDefault();
               var listing_id = $(this).val();
              //  alert(listing_id);
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
                 
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var listing= response.buyerlisting;
                          if(listing.is_verified == 1){
                            alert('Verified Seller Listing');
                            $('.modal-backdrop').remove();
                            $(`#btn-${listing_id}`).hide();
                          
                            
                        
                            $('#list_icon-'+listing_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_buyerlisting-'+listing_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');

                            // $('#rowcheck-'+listing_id).prop('checked',true);
                          }else{
                            // $('#rowcheck-'+listing_id).prop('checked',false);
                          }
                            
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
                  //  console.log(listing.listing_no );
                   $(".buyerlistingClass").empty();
                      // console.log(listing);
                      $('#single_record tbody').html("");
                      $('.modal-title').text('Listing No: '+listing.listing_no);
                      $('.price').text(listing.expected_price_per_unit+" (per "+listing.quantity_unit+") ");
                      $('.total').text(listing.expected_price+'SAR');
                      $('.seller').text('buyer: '+listing.buyer.fullname);
                      $('.selermobile').text('Mobile: '+listing.buyer.mobile);
                      $('.material').text(listing.material.name );
                      $('.address').text(listing.address.address );
                      if(listing.systemstatus.name == 'rejected'){
                        var status_class = 'bg-danger';
                      }else if (listing.systemstatus.name == 'completed') {
                        var status_class = 'bg-success';
                      }else if (listing.systemstatus.name == 'opened') {
                        var status_class = 'bg-success';
                      }else if (listing.systemstatus.name == 'in progress') {
                        var status_class = 'bg-info';
                      }else if (listing.systemstatus.name == 'closed') {
                        var status_class = 'bg-danger';
                      }else if (listing.systemstatus.name == 'pending') {
                        var status_class = 'bg-warning';
                      }else if (listing.systemstatus.name == 'active') {
                        var status_class = 'bg-info';
                      }else if (listing.systemstatus.name == 'accepted') {
                        var status_class = 'bg-success';
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
                   
                    
                
                }
        });
}

//order
$('td').delegate('.verified_order', 'click',function (e){
                e.preventDefault();
               var order_id= $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+order_id);
              
               $('#orderconfirmation').modal('show');
            });
function showOrderdata(data){
  // console.log(data);
        var order_id = data.id;
  
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('orderList')}}"+"/"+order_id,
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
           
                   var order = response.orders;
                  //  console.log(order);
                   $(".orderClass").empty();
                   $(".status").removeClass("bg-success bg-warning bg-danger ");
                      $('#single_record tbody').html("");
                      $('.modal-title').text('Order No: '+order.order_no);
                      $('.price').text(order.price+'SAR');
                      $('.tprice').text(order.total_price+'SAR');
                      $('.sellerlisting').text(order.offer.listing_source);

                      $('.seller').text('Seller: '+order.offer.seller.fullname);
                      $('.selermobile').text('Mobile Number: '+order.offer.seller.mobile);
                      $('.buyer').text('Buyer: '+order.offer.buyer.fullname);
                      $('.buyermobile').text('Mobile Number: '+order.offer.buyer.mobile);
                     
                      // $('.selermobile').text('Mobile: '+listing.seller.mobile);
                      $('.orderClass').attr('id','icon_order-'+order.order_id);  
                      $('.statechange').attr('id',order.order_id);
                 

                      $('.material').text(order.material.name );
                      $('.address').text(order.address.address );
                     
                      // $('.statechange').attr('id',order.order_id);
                      if(order.is_verified == 0){
                       
                        $('#icon_order-'+order.order_id).append('<button type="button" id="btn-'+order.order_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+order.order_id+'"  class=" btn btn-outline-primary verified_order">Verify</button>');

                        // $('.statechange').prop('checked',false);
                      }
                      else{
                       
                        $('#icon_order-'+order.order_id).append('<img class="verified_icon" src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');

                        // $('.statechange').prop('checked',true);

                      }
                      $('.weight').text(order.quantity+'('+order.quantity_unit+')');

                      if(order.systemstatus.name == 'rejected'){
                        var status_class = 'bg-danger';
                      }else if (order.systemstatus.name == 'completed') {
                        var status_class = 'bg-success';
                      }else if (order.systemstatus.name == 'in progress') {
                        var status_class = 'bg-info';
                      }else if (order.systemstatus.name == 'pending') {
                        var status_class = 'bg-warning';
                      }else if (order.systemstatus.name == 'active') {
                        var status_class = 'bg-info';
                      }else if (order.systemstatus.name == 'accepted') {
                        var status_class = 'bg-success';
                      }
                      
                      $('.status').addClass(status_class);
                      $('.status').text(order.systemstatus.name);
                      $('.driver').text('Driver: '+order.driver.fullname);
                      $('.drivermobile').text('Mobile Number: '+order.driver.mobile);
                }
        });
}
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
                            $('.modal-backdrop').remove();
                            $(`#btn-${order_id}`).hide();
                          
                            $('#order_icon-'+order_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_order-'+order_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            // $('#rowcheck-'+order_id).prop('checked',true);
                          }else{
                            // $('#rowcheck-'+order_id).prop('checked',false);
                          }
                            
                        }
                    }
                }
        });
}


// payment 
function showpayment(data){
  var pay_id = data.id;
             
  $.ajax({
            url: "{{ route('paymentList')}}"+"/"+pay_id,
		        type: "GET",
		        //data: {"_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
           
                   var payment = response.payment;
                   $(".paymentClass").empty();
                   $(".status").removeClass("bg-success bg-warning bg-danger ");

                      $('#single_record tbody').html("");
                      $('.modal-title').text('Payment No: '+payment.pay_no);
                      $('.price').text(payment.order.price+'SAR');
                      $('.tprice').text(payment.order.total_price+'SAR');
                      $('.seller').text('Seller: '+payment.order.offer.seller.fullname);
                      $('.selermobile').text('Mobile: '+payment.order.offer.seller.mobile);
                      $('.material').text(payment.order.material.name );
                      $('.buyer').text('Buyer: '+payment.order.offer.buyer.fullname);
                      $('.buyermobile').text('Mobile Number: '+payment.order.offer.buyer.mobile);
                      // $('.address').text(payment.order.address.address );
                      if(payment.systemstatus.name == 'rejected'){
                        var status_class = 'bg-danger';
                      }else if (payment.systemstatus.name == 'completed') {
                        var status_class = 'bg-success';
                      }else if (payment.systemstatus.name == 'in progress') {
                        var status_class = 'bg-info';
                      }else if (payment.systemstatus.name == 'pending') {
                        var status_class = 'bg-warning';
                      }else if (payment.systemstatus.name == 'active') {
                        var status_class = 'bg-info';
                      }else if (payment.systemstatus.name == 'accepted') {
                        var status_class = 'bg-success';
                      }
                      $('.orderno').text(payment.order.order_no);
                      $('.sellerlisting').text(payment.order.offer.listing_source);

                      $('.status').addClass(status_class);
                      $('.status').text(payment.systemstatus.name);
                      $('.driver').text('Driver: '+payment.order.driver.fullname);
                      $('.drivermobile').text('Mobile Number: '+payment.order.driver.mobile);
                      $('.weight').text(payment.order.quantity+'('+payment.order.quantity_unit+')');
                      $('.paymentClass').attr('id','icon_payment-'+payment.pay_id); 

                      // $('.statechange').attr('id',payment.pay_id);
                      // if(payment.is_verified == 0){
                      //   $('#icon_payment-'+payment.pay_id).append('<button type="button" id="btn-'+payment.pay_id+'" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="'+payment.pay_id+'"  class=" btn btn-outline-primary verified_payment">Verify</button>');

                      //   // $('.statechange').prop('checked',false);
                      // }
                      // else{
                      //   $('#icon_payment-'+payment.pay_id).append('<img src="{{ url('/') }}/assets/img/shield-tick.png" alt="" style="height: 20px">');

                      //   // $('.statechange').prop('checked',true);

                      // }
               
                   
                    
                
                }
        });
}
$('td').delegate('.verified_payment', 'click',function (e){
                e.preventDefault();
               var pay_id= $(this).val();
                $('.confirm_btn').attr('id','rowcheck-'+pay_id);
              
               $('#paymentconfirmation').modal('show');
            });
  function paymentStatus(data){
    var pay_id = data.id;
  
        if($(this).hasClass('statechange')){
          pay_id=pay_id.replace('modalcheck-','');
        }else{
          pay_id=pay_id.replace('rowcheck-','');
        }
        $('#paymentconfirmation').modal('hide');

        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('paymentstatus.update') }}",
		        type: "POST",
		        data: {"id": pay_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                        
                        if($(this).hasClass('statechange')){
         
                        }else{
                          var payment= response.payment;
                          if(payment.is_verified == 1){
                            alert('Verified Payment');
                       
                            $(`#btn-${pay_id}`).hide();
                          
                            $('#payment_icon-'+pay_id).append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            $('#icon_payment-'+pay_id).html('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                            // $('#rowcheck-'+order_id).prop('checked',true);
                          }else{
                            // $('#rowcheck-'+order_id).prop('checked',false);
                          }
                            
                        }
                    }
                }
        });
  }          
</script>

@stop
