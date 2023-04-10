

   @extends('layout.app')
   @section('content')
    <form action="{{ route('complaint_list') }}" method="GET">
      {{-- @csrf --}}
      <div class="col-md-12 col-lg-12 mt-1">
        <div class="row">
           
            <div class="mb-1 col-6 col-sm-6 col-md-3"><input type="date" id="smallInput"
                    class="form-control form-control-sm" value="{{ isset($data['date']) ? $data['date'] : "" }}"  name="date"></div>
         
           
            {{-- <div class="mb-1 col-6 col-sm-6 col-md-3"><input class="form-control flatpickr-input"
                    placeholder="Material" id="name" name="mname" value="{{ isset($data['mname']) ? $data['mname'] : "" }}" style="background-color: rgb(255, 255, 255);" type="text"
                    ></div> --}}
            <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select form-select-sm" name="source" id="" aria-label="Default select example">
                  <option selected value="">Select source From List</option>
                    @foreach ($source_list as $sources)
                  
                      <option {{ isset($data['source']) &&  $data['source'] == $sources->Value ? "selected" : ""}} value="{{$sources->Value}}">{{$sources->Name }}</option>
                  
                    @endforeach
                </select>
           </div>        
            {{-- <div class="mb-1 col-6 col-sm-6 col-md-3">
              <select class="form-select" name="is_verify" id="status" aria-label="Default select example">
                <option value="">Select Verification status List</option>
              
                <option {{ isset($data['is_verify']) && ($data['is_verify']==1)  ? "selected" : "" }} value="1">verified</option>
                <option {{ isset($data['is_verify']) && ($data['is_verify']==0)  ? "selected" : "" }} value="0">unverified</option>


              </select>
           </div> --}}
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
                     <a href="{{ route('complaint_list') }}">
                      <button type="submit" class="btn btn-primary btn-sm  search"
                      style="width: max-content;"><span>Search</span>
                    
                </button>
                     </a>
                    </form>
                    </div>
                    <div class="mb-1 col-6 col-sm-6 col-md-3">
                      <a href="{{ route('complaint_list') }}">
                        <button type="button" class="btn btn-secondary btn-sm" style="width: max-content;">Reset</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="table-responsive">
      <table id="datatable" class="table table-striped" style="100%;">
        <thead>
            <tr><th>Complaint No</th>
                <th>Source</th>
                <th>No</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Date</th>
                <th>Updated By</th>
                 <th>Status</th>
                 <th>Edit</th>
               
            </tr>
        </thead>
        <tbody>
       
        
          
      @foreach ($complaint  as $complaints)
        @php
            $status_class = '';
            if($complaints->systemstatus->name == 'inactive'){
              $status_class = 'bg-info';
            }elseif($complaints->systemstatus->name == 'active'){
              $status_class = 'bg-success';
            }
      @endphp
      <tr> 
          <td>
            <a data-bs-toggle="modal" data-bs-target="#complaint_modal" href="javascript:void(0)" id="{{$complaints->complaint_id}}" onclick="showcomplaint(this)">   {{ $complaints->complaint_no}}</a>
          </td>           
          <td>{{ $complaints->item_source}}</td>
          <td>{{ $complaints->complaintAgainstOP->ref_no }}</td>
          <td>{{ $complaints->complaint_type  }}</td>
          <td> {{ $complaints->complaint_priority }} </td>
        <td> {{$complaints->created_at}} </td>
        @if ( $complaints->updated_by != null &&  $complaints->updated_by != '')
        <td>{{ $complaints->updatedByPerson->fullname }} {{$complaints->updated_at}}</td>
          @else
          <td></td>
      @endif
        <td   title="{{$complaints->reason }}"><span id="status-{{ $complaints->complaint_id }}" class="badge {{ $status_class }}" >{{ $complaints->systemstatus->name}}</span></td>
        {{-- @if ($complaints->system_status->value == 1) --}}
        @if($complaints->status == 1)
        <td data-bs-toggle="modal" data-bs-target="#unactive_status" href="javascript:void(0)" id="{{$complaints->complaint_id}}" onclick="status(this)"><i id="icon-{{ $complaints->complaint_id }}" class="fa fa-pencil-square"></i></a>
        </td>
        @else
        <td></td>
        @endif
        
        {{-- @else
        <td></td>   
        @endif --}}
                        
      </tr>
      @endforeach
              </tbody>
            
    </table>
     </div>
    @include('admin/modals/complaint_modal')
    @include('admin/modals/picture_modal')
 @stop