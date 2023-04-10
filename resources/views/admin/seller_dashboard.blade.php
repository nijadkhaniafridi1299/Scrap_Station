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
                              <span class="avatar-content" style="border-radius: 0px; font-size: calc(48px); width: 100%; height: 100%;">@php foreach(explode(" ",$seller->fullname) as $ft) {echo strtoupper($ft[0]);} @endphp </span>
                          </div>
                          <div class="d-flex flex-column align-items-center text-center">
                          <div class="user-info">
                              <h4 class="userfullname"><span class="fw-bold text-capitalize">{{ $seller->fullname ?? ""}}</span>
                                @if ($seller->is_verified == 1  ) 
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
                          <div class="ms-75"><h4 class="mb-0 wallet_amount">{{ $seller->wallet_amount ?? ""}}</h4></div>
                          <span class="rounded p-75 badge bg-light-primary">
                          </span><small>Wallet Amount</small>
                          @if (in_array("recharge", Session::get('roles')))
                           <i class="fa fa-plus-circle adding_recharge" id="{{ $seller->seller_id }}" title="Add Recharge"></i>
                               
                          @endif
                      </div>
                      
                  <div class="d-flex flex-column align-items-start">
                     <div class="ms-75"><h4 class="mb-0 ">{{ $seller->deal_in_progress ?? ""}}</h4></div>
                     <span class="rounded p-75 badge bg-light-primary">
                     </span>
                     <small>Deal Progress</small>
                  </div>
                  <div class="d-flex flex-column align-items-start me-2">
                  <div class="ms-75"><h4 class="mb-0">{{ $seller->deal_completed ?? "" }}</h4></div>
                     <span class="rounded p-75 badge bg-light-primary">
                     </span><small>Deal Completed</small>
                  </div>
              </div>
              <h4 class="fw-bolder border-bottom pb-50 mb-1">Details</h4>
              <div class="info-container">
                <ul class="list-unstyled">
                      <li class="mb-75">
                      <span class="fw-bolder me-25">Email:</span>
                      <span>{{ $seller->email ?? ""}}</span>
                      </li>
                      <li class="mb-75">
                          <span class="fw-bolder me-25">Status:</span>
                          @php
                          $status_class = '';
                          if ($seller->systemstatus->name == 'active') {
                              $status_class = 'bg-success';
                          }elseif ($seller->systemstatus->name == 'inactive') {
                              $status_class = 'bg-info';
                          }
                          @endphp
                          <span class=" badge {{  $status_class }} ">{{ $seller->systemstatus->name}}</span>
                      </li>
                    
                      <li class="mb-75">
                          <span class="fw-bolder me-25">Mobile #:</span>
                          <span>{{ $seller->mobile }}</span>
                      </li>
                      <li class="mb-75">
                       
                        <span>{{ $seller->addressdetail->address ?? ""}}</span>
                    
                    </li>
                    <li class="mb-75">
                      <span class="fw-bolder me-25">Type:</span>

                      <span>{{ $seller->type ?? ""}}</span>
                  
                  </li>
                  </ul>
              </div>
             <div class="d-flex justify-content-center pt-2 mr-1">
                @if($seller->is_verified == 0)
                 <button type="button" class="ms-1 btn btn-outline-primary btn-sm  btn-space verified_buttion" style="margin-right: 3%;" id="rowcheck-{{$seller->seller_id}}" onclick="verifing_sellers(this)" >Verify</button>
                 @endif
                  <button type="button" class="btn btn-primary btn-sm  sellerupdate">Edit</button>
              

                  <div class="modal fade" id="sellerEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title1" id="exampleModalLabel">Edit Seller</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <form>
                            <div class="row">
                              <div class="col">
                                <input type="text"  class="form-control input_Length" value="{{ $seller->fullname }}" placeholder="Enter Name" name="name" id="fullname" required>
                                <div class="lengthmessage">

                                </div>
                              </div>
                              <div class="col">
                                <input type="text" class="form-control input_Length" placeholder="Enter name_ar" value="{{ $seller->fullname_ar }}" name="fullname_ar" id="fullname_ar" required>
                                <div class="lengthmessage">

                                </div>
                              </div>
                            </div>
                            <br>
                            <div class="row">
                              <div class="col">
                                <input type="email" class="form-control input_Length" value="{{  $seller->email  }}" placeholder="Enter Email" name="email" id="email" required>
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
                                <input type="text" maxlength="30" minlength="5" class="form-control" value="{{ $seller->iqama_cr_no }}" placeholder="Enter Iqma Cr NO" name="iqma" id="iqma" required>
                                <div class="lengthmessage">

                                </div>
                              </div>
                              <div class="col">
                                <input type="file" class="form-control" placeholder="Enter Image" name="image" id="image" required>
                              </div>
                            </div><br>
                            <div class="row">
                              <div class="col">
                                <input type="text"  class="form-control input_Length"  value="{{ $seller->mobile }}" placeholder="+92*++++++*" name="mobile" id="mobile" required>
                                <div class="lengthno">

                                </div>
                              </div>
                              <div class="col">
                                <input type="hidden" class="form-control input_Length" value="{{ $seller->seller_id }}"  name="seller_id" id="seller_id" required>
                              
                              </div>
                            </div>
                          </form>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="button" class="btn btn-primary update_seller">Update</button>
                        </div>
                      </div>
                    </div>
                  </div>


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
      <div class="order-0 col-md-7 order-md-1 col-lg-7 col-xl-8">
        <ul class="mb-2 nav nav-pills">
           
              <li class="nav-item">
                  <a class="nav-link active"  data-bs-toggle="tab" href="#seller_listing">
                      <span class="fw-bold">Seller Listing</span>
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
                              <form action="{{ url('seller_dashboard/'.$seller->seller_id)  }}" method="GET">
                                {{-- @csrf --}}
                                <div class="col-md-12 col-lg-12">
                                  <div class="row" style="margin-top: 3%;">
                                      <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="Seller Name" type="text"
                                              class="form-control form-control-sm" value="{{ isset($data['fullname']) ? $data['fullname'] : "" }}"  name="fullname"></div>
                                   
                                      
                                      <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control form-control-sm"
                                              placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                                              ></div>
                                      <div class="mb-1 col-6 col-sm-6 col-md-3"> <select class="form-select form-select-sm" name="is_verify" id="is_verify" aria-label="Default select example">
                                        <option value="">Select From List</option>
                                      
                                        <option {{ isset($data['is_verify']) ? "selected" : "" }} value="1">verified</option>
                                        <option {{ isset($data['is_verify'])  ? "selected" : "" }} value="0">unverified</option>
                        
                        
                                      </select>
                                    </div>
                                              <div class="mb-1 col-6 col-sm-6 col-md-3"><select class="form-select form-select-sm" name="status" id="status" aria-label="Default select example">
                                                <option selected value="">Select From List</option>
                                                 @foreach ($systemstatus as $system)
                                               
                                                <option {{ isset($data['status']) &&  $data['status'] ==$system->value? "selected" : ""}} value="{{ $system->value }}">{{$system->name }}</option>
                                                
                                                 @endforeach
                                              </select></div>
                                      <div class="justify-content-md-start  col-md-6 col-lg-6">
                                          <div class="row">
                                              <div class="mb-1 col-6 col-sm-6 col-md-3">
                                                {{-- <input type="button" class="btn btn-primary " value="Search"> --}}
                                               <a href="{{ url('seller_dashboard/'.$seller->seller_id)  }}">
                                                <button type="submit" class="btn btn-primary btn-sm search"
                                                style="width: max-content;"><span>Search</span>
                                              
                                          </button>
                                               </a>
                                              </form>
                                              </div>
                                              <div class="mb-1 col-6 col-sm-6 col-md-3">
                                                <a href="{{ url('seller_dashboard/'.$seller->seller_id) }}">
                                                  <button type="button" class="btn btn-secondary btn-sm" style="width: max-content;">Reset</button>
                                                </a>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                                <div style="padding: 24px; overflow-x:auto;">
                                  <table class="table table-striped" style="width:100%; ">
                                    <thead>
                                        <tr>
                                            <th>Listing No</th>
                                            <th>Seller</th>
                                            <th>Material</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Total</th>
                                            <th>Updated By</th>
                                            <th>Status</th>
                                            <th>Verified</th>
                                           
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($listings as $listing)
                                       
                                        @php
                                        $status_class = '';
                                        if($listing->systemstatus->name == 'in progress'){
                                          $status_class = 'bg-info';
                                        }elseif($listing->systemstatus->name == 'pending'){
                                          $status_class = 'bg-warning';
                                        }elseif($listing->systemstatus->name == 'rejected'){
                                          $status_class = 'bg-danger';
                                        }elseif($listing->systemstatus->name == 'active'){
                                          $status_class = 'bg-success';
                                        }elseif($listing->systemstatus->name == 'opened'){
                                          $status_class = 'bg-success';
                                        }
                                        elseif($listing->systemstatus->name == 'accepted'){
                                          $status_class = 'bg-success';
                                        } elseif($listing->systemstatus->name == 'closed'){
                                          $status_class = 'bg-danger';
                                        }
                                     
                                        @endphp
                                       
                                        <tr>
                                          <td>
                                          <a data-bs-toggle="modal" data-bs-target="#exampleModal" href="javascript:void(0)" id="{{$listing->sell_list_id}}" onclick="showListing(this)">  {{ $listing->listing_no }}</a>
                                          </td>
                                          <td>{{ $listing->seller->fullname }}</td>
                                            <td>{{ $listing->material->name}}</td>
                                            <td>{{ $listing->quantity." (".$listing->quantity_unit.")"  }}</td>
                                            <td>{{ $listing->expected_price_per_unit." (per ".$listing->quantity_unit.") "}} SAR</td>
                                            <td>{{ $listing->expected_price}} SAR</td>
                                            @if ( $listing->updated_by != null &&  $listing->updated_by != '')
                                            <td>{{ $listing->updatedByPerson->fullname }}</td>
                                                @else
                                                <td></td>
                                            @endif
                                            <td><span class="badge {{ $status_class }}">{{ $listing->systemstatus->name}}</span></td>
                                            <td id="check_icon-{{$listing->sell_list_id}}" style="text-align: center;">
                                              @if ($listing->is_verified == 1)
                                              <img src="{{ asset('/assets/img/shield-tick.png')}}" alt="" style="height: 20px">
                              
                                                  @else
                                                  <button type="button" id="btn-{{$listing->sell_list_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$listing->sell_list_id}}"  class=" btn btn-outline-primary verified_seller_listing">Verify</button>
                              
                                              @endif
                                              
                                              
                                              {{-- <label class="switch">
                                                <input type="checkbox" value="{{ $listing->is_verified }}" id="rowcheck-{{$listing->sell_list_id}}"  onchange="checkboxChange(this)" {{ $listing->is_verified==1 ? 'checked' : '' }} class="getvalue">
                                                <span class="slider round"></span>
                                              </label> --}}
                                            </td>
                                           
                                        </tr>
                                        @endforeach
                                    </tbody>
                                  
                                </table>
                                @include('admin/modals/sellerListingModal')

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
                                                  <th>Updated By</th>
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
                                                <td>{{ $order->updatedByPerson->fullname }} {{ $order->updated_at}}</td>
                                                    @else
                                                    <td></td>
                                                @endif
                                                <td><span class="badge {{ $status_class }}">{{ $order->systemstatus->name}}</span></td>
                                                
                                               <td id="check_icon-{{$order->order_id}}" style="text-align: center;">
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
                                          <th>Total Price</th>
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
                                              <td>{{ $payment->order->total_price." (per ".$payment->order->quantity_unit.") "}} SAR</td>
                                              @if ( $payment->updated_by != null &&  $payment->updated_by != '')
                                              <td>{{ $payment->updatedByPerson->fullname }} {{ $payment->updated_at}}</td>
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
                                              </td>
                                              --}}
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
                                         @foreach ($sellerAddress as $buyerAddresses)
                                        
                                        
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
    </div>
     
    </div>
  
  </div></div></div></div>
 @include('admin/modals/rechargeModal')





 





<script>
   $('.sellerupdate').on("click", function(e){
    e.preventDefault();
 
   $('#sellerEdit').modal('show');
   });

   
    function verifing_sellers(data){
  // console.log(data);
        var seller_id = data.id;
       
        if($(this).hasClass('statechange')){
          seller_id=seller_id.replace('modalcheck-','');
        }else{
          seller_id=seller_id.replace('rowcheck-','');
        }
       
        // var value = $(this).val();
        // alert(value);  
        //      if($(this).is(":checked")){
        //       alert('check');
        //       value =1;
        //      }
             
        $.ajax({
            url: "{{ route('seller_verified.update') }}",
		        type: "POST",
		        data: {"id": seller_id, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                 
                        
                        
                          var seller= response.seller;
                          if(seller.is_verified == 1){
                            alert('Verified Seller');
                            $('.verified_buttion').hide();
                            $('.verified_buttion').addClass('d-none');
                            $('.userfullname').append('<img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">');
                           // $('#rowcheck-'+seller.seller_id).hide();
                          }
                            
                        
                    }
                }
        });
}








  var minLength = 5;
var maxLength = 35;
var mobileL = 11;
$(document).ready(function(){
    $("#fullname,#fullname_ar, #email, #iqma").each(function(){
      $(this).on('keydown keyup change', function(){
          var char = $(this).val();
          var charLength = $(this).val().length;
          if(charLength < minLength){
            // $('#name, #name_ar, #email').text('Length is short, minimum' +minLength+' required.');
            $(this).next('.lengthmessage').html('<div class="text-danger">Length is short, minimum '+minLength+' required.</div>');

          }else if(charLength > maxLength){
            // $('#name, #name_ar, #email').text('Length is not valid, maximum '+maxLength+' allowed.');
            $(this).next('.lengthmessage').html('<div class="text-danger">Length is  maximum '+maxLength+' required.</div>');
             
              $(this).val(char.substring(0, maxLength));
          }
          else{
            $(this).next('.lengthmessage').html('<div></div>');
          }
         
      });
  });

}); 

//seller Update
$(document).ready(function(){
  $('.update_seller').on("click", function(){
    var id = $('#seller_id').val();
    var fullname = $('#fullname').val();
    var fullname_ar = $('#fullname_ar').val();
    var iqama_cr_no = $('#iqma').val();
   
    var email = $('#email').val();
    var mobile = $('#mobile').val();
    var status = $('#status').val();
    var data = {
                 'fullname': fullname,
                 'fullname_ar':fullname_ar,
                 'iqama_cr_no':iqama_cr_no,
                 'email':email,
                 'mobile':mobile,
                 'status':status,
                }
                $('#sellerEdit').modal('hide');
             $.ajax({
            url:  "{{ route('seller.update')}}"+"/"+id,
		        type: "PUT",
		        data: {data, "_token": "{{csrf_token()}}"},
		        datatype: "json",
               
                success:function (response){
                    if (response.status == 200){
                        alert(response.message);
                      
                    }
                }
        });    
 
  });
});
</script>
@stop