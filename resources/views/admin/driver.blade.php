



   @extends('layout.app')
   @section('content')
  <main id="main" class="main">
    
    @if(in_array("add_driver", Session::get('roles')))
    <button type="button" class="btn btn-primary btn-sm add_driver">Add</button>
    @endif
    <form action="{{ route('driverList') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12 mt-1">
        <div class="row">
           
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input placeholder="Driver Name" type="text"
                    class="form-control form-control-sm" value="{{ isset($data['fullname']) ? $data['fullname'] : "" }}"  name="fullname"></div>
         
                    <div class="mb-1 col-6 col-sm-6 col-md-3"><input type="date"
                      class="form-control form-control-sm" value="{{ isset($data['date']) ? $data['date'] : "" }}"  name="date"></div>
            {{-- <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control flatpickr-input"
                    placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                    ></div> --}}
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select form-select-sm" name="buyername" id="buyername" aria-label="Default select example">
                  <option selected value="">Select buyer From List</option>
                    @foreach ($buyerlist as $buyerlists)
                  
                      <option {{ isset($data['buyername']) &&  $data['buyername'] == $buyerlists->fullname ? "selected" : ""}} value="{{$buyerlists->fullname }}">{{$buyerlists->fullname }}</option>
                  
                    @endforeach
                </select>
          </div>        
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select form-select-sm" name="is_verify" id="status" aria-label="Default select example">
                <option value="">Select Verification From List</option>
              
                <option {{ isset($data['is_verify']) && ($data['is_verify']==1)  ? "selected" : "" }} value="1">verified</option>
                <option {{ isset($data['is_verify']) && ($data['is_verify']==0)  ? "selected" : "" }} value="0">unverified</option>


              </select>
           </div>
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
                     <a href="{{ route('driverList') }}">
                      <button type="submit" class="btn btn-primary btn-sm  search"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('driverList') }}">
                        <button type="button" class="btn btn-secondary btn-sm " style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <div>
    <div class="table_responsive">
      <table id="datatable" class="table table-striped">
        <thead>
            <tr>
                <th>Serial No</th>
                <th>Driver Name</th>
                <th>Buyer Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Date</th>
                <th>Updated By</th>
                <th>Status</th>
                <th>Verified</th>
                <th>Edit</th>
               
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
                 <td> {{ $drivers->created_at }} </td>
                 @if ($drivers->updated_by != null &&  $drivers->updated_by != '')
                 <td>{{$drivers->updatedByPerson->fullname}} {{$drivers->updated_at}}</td>
                     @else
                     <td></td>
                 @endif
                <td><span class="badge {{ $status_class }}">{{ $drivers->systemstatus->name}}</span></td>
                           
              <td id="check_icon-{{$drivers->driver_id}}" style="text-align: center">
                @if ($drivers->is_verified == 1)
                <img src="{{ asset("/assets/img/shield-tick.png")}}" alt="" style="height: 20px">
  
                    @else
                    <button type="button" id="btn-{{$drivers->driver_id}}" style="height: 30px; width:56px; font-size:10px; font-weight: bold;" value="{{$drivers->driver_id}}"  class=" btn btn-outline-primary verify_driver">Verify</button>
  
                @endif
            </td>
                {{-- <td><label class="switch">
                    <input type="checkbox" value="{{ $buyers->is_verified }}" id="rowcheck-{{$buyers->buyer_id}}"  onchange="checkboxChange(this)" {{ $buyers->is_verified==1 ? 'checked' : '' }} class="getvalue">
                    <span class="slider round"></span>
                  </label>
                </td> --}}
               <td>
                <a data-bs-toggle="modal" data-bs-target="#edit_driver" href="javascript:void(0)" id="{{$drivers->driver_id}}" onclick="edit_driver(this)"><i class="fa fa-pencil-square"></i></a>
  
               </td>
            </tr>
            @endforeach
        </tbody>
      
    </table>
    </div>
   
   </div>



    {{-- //add driver modal --}}
    <div class="modal fade" id="add_drivers" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title1" id="exampleModalLabel">Add Driver</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="add_driver_data">
              <div class="row">
                <div class="col">
                  <input type="text" maxlength="30" minlength="5" class="form-control input_Length" value="" placeholder="Enter Name" name="driver[fullname]" id="fullname" required>
                  <div class="lengthmessage">
                   @csrf
                  </div>
                </div>
                <div class="col">
                  <input type="text" maxlength="30" minlength="5" class="form-control input_Length" placeholder="Enter name_ar" value="" name="driver[fullname_ar]" id="fullname_ar" required>
                  <div class="lengthmessage">

                  </div>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col">
                  <input type="email" maxlength="30" minlength="5" class="form-control input_Length" value="" placeholder="Enter Email" name="driver[email]" id="email" required>
                  <div class="lengthmessage">

                  </div>
                </div>
                <div class="col">
                  <select class="form-select" name="driver[buyer_id]">
                    <option selected>Select Buyer from list</option>
                     @foreach ($buyerlist as $buyerlists)
                     <option value="{{ $buyerlists->buyer_id }}">{{ $buyerlists->fullname }}</option>
                     @endforeach
                   
                  </select>
                </div>
              
              </div><br>
              <div class="row">
                <div class="col">
                  <input type="text" maxlength="30" minlength="5" class="form-control" value="" placeholder="Enter Iqma Cr NO" name="driver[iqama_cr_no]" id="iqama_cr_no" required>
                  <div class="lengthmessage">

                  </div>
                </div>
                <div class="col">
                  <input type="file" class="form-control" placeholder="Enter Image">
                </div>
              </div><br>
              <div class="row">
                <div class="col">
                  <input type="text" maxlength="30" minlength="5" class="form-control input_Length"  value="" placeholder="+92*++++++*" name="driver[mobile]" id="mobile" required>
                  <div class="lengthno">

                  </div>
                </div>
              
              </div>
           
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary add_drivers">Add</button>
          </div>
        </div>
      </form>
      </div>
    </div>

{{-- // edit driver modal --}}

<div class="modal fade" id="edit_driver" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title2" id="exampleModalLabel">Edit Driver</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="add_driver_data">
          <div class="row">
            <div class="col">
              <input type="text" maxlength="30" minlength="5" class="form-control input_Length" value="" placeholder="Enter Name" name="driver[fullname]" id="fullname1" required>
              <div class="lengthmessage">
               @csrf
              </div>
            </div>
            <div class="col">
              <input type="text" maxlength="30" minlength="5" class="form-control input_Length" placeholder="Enter name_ar" value="" name="driver[fullname_ar]" id="fullname_ar1" required>
              <div class="lengthmessage">

              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col">
              <input type="email" maxlength="30" minlength="5" class="form-control input_Length" value="" placeholder="Enter Email" name="driver[email]" id="email1" required>
              <div class="lengthmessage">

              </div>
            </div>
            <div class="col">
              <select class="form-select" name="buyer_id" id="buyer_id">
                <option selected>Select Buyer from list</option>
                 @foreach ($buyerlist as $buyerlists)
                 <option value="{{ $buyerlists->buyer_id }}">{{ $buyerlists->fullname }}</option>
                 @endforeach
               
              </select>
            </div>
          </div><br>
          <div class="row">
            <div class="col">
              <input type="text" maxlength="30" minlength="5" class="form-control" value="" placeholder="Enter Iqma Cr NO" name="driver[iqama_cr_no]" id="iqama_cr_no1" required>
              <div class="lengthmessage">

              </div>
            </div>
            <div class="col">
              <input type="file" class="form-control" placeholder="Enter Image">
            </div>
          </div><br>
          <div class="row">
            <div class="col">
              <input type="text" maxlength="30" minlength="5" class="form-control input_Length"  value="" placeholder="+92*++++++*" name="driver[mobile]" id="mobile1" required>
              <div class="lengthno">
               <input type="hidden" id="driver_id" value="" name="driver_id">
              </div>
            </div>
          
          </div>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary Update_drivers">Update</button>
      </div>
    </div>
  </form>
  </div>
</div>
 @include('admin/modals/driverModal')

  </main>
 <!-- End #main -->
@stop
  <!-- ======= Footer ======= -->

