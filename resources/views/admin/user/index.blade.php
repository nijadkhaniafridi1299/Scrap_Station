@extends('layout.app')
@section('content')

  <!-- End Sidebar-->
<main id="main" class="main">
    @if(in_array("add_user", Session::get('roles')))
    <button type="button" class="btn btn-primary btn-sm " id="add_users">Add</button>
    @endif
    <fieldset class="well">
        <div class="clearfix">
            <div class="pull-right tableTools-container2">
                <div class="dt-buttons btn-overlap btn-group">
                </div>
            </div>
        </div>
        <!-- div.table-responsive -->
    
        <!-- div.dataTables_borderWrap -->
        <div class="table_responsive">
            <table id="datatable" class="table table-striped"  cellspacing="0">
                <thead>
                    <tr>
                        <th>{{__('User Id')}}</th>
                        <th>{{__('User Name')}}</th>
                        <th>{{__('User Email')}}</th>
                        <th>{{__('User Group')}}</th>
                        <th>{{__('Last Login')}}</th>
                        <th>{{__('Created at')}}</th>
                        <th>{{__('Edit')}}</th>
                        <th>{{__('Enable/Disable')}}</th>
                    </tr>
                </thead>
                <tbody>
                @for( $i=0, $count = count($users); $i < $count; $i++)
                    <tr>
                        <td>{{$users[$i]['user_id']}}</td>
                        
                        <td>{{$users[$i]['first_name']}} {{$users[$i]['last_name']}}</td>
                        <td>{{$users[$i]['email']}} </td>
                        <td>
                            @if (isset($users[$i]['group']) && isset($users[$i]['group']['group_name'])) 
                                {{ $users[$i]['group']['group_name'] }} 
                            @endif
                        </td>
                        <td><?php
                            $date = date_create($users[$i]['last_login']);
                        ?>{{ date_format($date, 'd M y h:i') }}</td>
                        <td>{{$users[$i]['created_at'] }}</td>
                        <td><a href="javascript:void(0)" value="{{$users[$i]['user_id']}}" onclick="editing_user(this)"><i class="fa fa-pencil-square"></i></a></td>
                        <td><a href="#" class="status" rel="Delete">
                        <?php if($users[$i]['status'] == 1) { ?>
                            {{__('Disable')}}</a></td>
                        <?php } else { ?>
                            {{__('Enable')}}</a></td>
                        <?php } ?>
    
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </fieldset>
    <div class="modal fade" id="add_user_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title2" id="title_user"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="create_update_users">
                <div class="row">
                  <div class="col">
                    <label  for="user-first-name">{{ __("First Name") }} <span class="required">*</span>
                    </label>
                    <input type="text" name="user[first_name]" id="first_name" required="required" class="form-control col-md-3 col-xs-6" placeholder="{{__('Name')}}" value="{{ isset($data['first_name']) ? $data['first_name'] : ''}}">
                     @csrf
                    <input type="hidden" name="" id="user_id">
                  </div>
                  <div class="col">
                    <label  for="user-last-name">{{ __("Last Name") }} <span class="required">*</span>
                    </label>
				    <input type="text" name="user[last_name]" id="last_name" required="required" class="form-control col-md-3 col-xs-6" placeholder="{{__('Name')}}" value="{{ isset($data['last_name']) ? $data['last_name'] : ''}}">
                  </div>
                  <div class="col">
                   <label for="group-id">{{ __("User Group") }} <span class="required">*</span></label>
                      <select name="user[group_id]" id="group_id" class="form-control col-md-3 col-xs-6" required="required" id="groupType" onchange="displayRole()">
                        <option value="">  -- {{__('Select One')}} --  </option>
                        @for ($i = 0; $i < count($groups); $i++)
                        <option value="{{ $groups[$i]["group_id"] }}" {{ isset($data['group_id']) && $data['group_id'] == $groups[$i]["group_id"] ? 'selected' : ''}}>{{ $groups[$i]["group_name"]}}</option>
                        @endfor
                    </select>                 
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col">
                    <label for="user-gender">{{ __("User Gender") }} <span class="required">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user[gender]" id="Male" value="1" required="required" 
                            @if (isset($data['gender']))
                                @if ($data['gender'] == 1)
                                    checked
                                @endif
                            @else
                                checked
                            @endif
                        <?php //isset($data['gender']) && $data['gender'] == 'male'  ? 'checked' : ''?> >
                        <label class="form-check-label" for="Male">
                        {{__('Male')}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user[gender]" id="Female" value="2" required="required" {{ isset($data['gender']) && $data['gender'] == 2 ? 'checked' : ''}}>
                        <label class="form-check-label" for="Female">
                        {{__('Female')}}
                        </label>
                    </div>                 
                  </div>
                  <div class="col">
                    <label  for="user-email">{{ __("User Email") }} <span class="required">*</span>
                    </label>
					<input type="text" name="user[email]" id="email" required="required" class="form-control col-md-3 col-xs-6" placeholder="{{__('Email')}}" value="{{ isset($data['email']) ? $data['email'] : ''}}">
                  
                  </div>
                  <div class="col">
                    <label for="user-status">{{ __("User Status") }} <span class="required">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user[status]" id="enable" value="1" required="required" 
                        @if (isset($data['status']))
                            @if ($data['status'] == 1)
                                checked
                            @endif
                        @else 
                            checked
                        @endif
                        <?php //isset($data['status']) && $data['status'] == 1 ? 'checked' : ''?>>
                        <label class="form-check-label" for="Enable">
                        {{__('Enable')}}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="user[status]" id="disable" value="9" required="required">
                        <label class="form-check-label" for="Disable">
                        {{__('Disable')}}
                        </label>
                    </div>
                   </div>
                </div><br>
                
                <div class="row">
                    <div class="col">
                        <label for="area_id">{{__('Select Extra Roles')}}</label>
                         <div>
                          <?php
                          $selected_role_ids = [];
                          
                          if (isset($_POST['user']) && isset($_POST['user']['role_id'])) {
                              $selected_role_ids = $_POST['user']['role_id'];
                          }
                      
                      ?>
                              <div id="extra_roles" style="max-height: 200px; overflow-y: auto; border: 2px solid #CCC !important; padding: 3px;" >
                              @for($i=0, $count = count($roles); $i < $count; $i++)
                                  <div><input value="{{ $roles[$i]['role_id'] }}" type="checkbox" id="role_ides_{{$roles[$i]['role_id']}}" name="user[role_id][]" onchange="doNoUncheckGroupRoles(this)"
                                  {{ (in_array($roles[$i]['role_id'], $selected_role_ids)) ? ' checked="checked"' : '' }}/> {{ $roles[$i]['role_name'] }}</div>
                              @endfor
                              </div>
                              <span style="font-size:11px;"><p>*{{__('You can not uncheck roles which are selected in User Group')}}</p></span>
                              <button type="button" id='select_all'>{{__('All Roles')}}</a> &nbsp; <button type="button" id='deselect_all'>{{__	('Deselect All')}}</a>
                          </div>
                     </div>
           
                </div><br>
            </div>
            <div class="modal-footer" id="modal-footer_add">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" id="submitbtn" class="btn btn-primary">Added</button>
            </div>
          </div>
        </form>
        </div>
      </div>
    
</main>

<script>
 function doNoUncheckGroupRoles(e) {
//get selected user group roles.
  
    var role_id = e.value;
    groupType = document.getElementById("groupType");
    if (groupType != null) {
        strUser = document.getElementById("groupType").value;
        id = <?php echo json_encode(\App\Model\Group::where('status', 1)->get()->toArray()); ?>;
        for (var i = 0; i < id.length; i++) {
            if (strUser == id[i].group_id) {
            var check = JSON.parse(id[i].role_id);
            break;
            }
        }
        if (jQuery.inArray(parseInt(role_id), check) != -1 || check.length == 0) {
            e.checked = true;
        }
    }
    
}
    $(document).ready(function(){
      $('#add_users').on('click', function(){
            $('#title_user').html('Add User');
            $('#submitbtn').text('Add');
            $('#add_user_modal').modal('show');
      });
      $('#create_update_users').submit(function(e){
           e.preventDefault();
           if($('#user_id').val()){
            var user_id = $('#user_id').val();
            $.ajax({
                        url:  "{{ route('update_user')}}"+"/"+user_id,
                            type: "POST",
                            data: $(this).serialize(),
                            datatype: "json",
                            success:function (response){
                                if (response.code == 202){
                                    $('#add_user_modal').modal('hide');
                                    $('#add_user_modal').on('hidden.bs.modal', function () {
                                    $(this).find('form').trigger('reset');
                                    });
                                   alert(response.message); 
                                
                                }
                            
                                else{
                                    $('#add_user_modal').modal('show');
                                    alert(response.message);
                                }
                            }
                });
           }
           else{
                $.ajax({
                        url:  "{{ route('add_user')}}",
                        type: "POST",
                        data: $(this).serialize(),
                        datatype: "json",
                        success:function (response){
                            if (response.code == 201){
                                $('#add_user_modal').modal('hide');
                                $('#add_user_modal').on('hidden.bs.modal', function () {
                                $(this).find('form').trigger('reset');
                                });
                                alert(response.message); 
                            
                            }
                        
                            else{
                                $('#add_role_modal').modal('show');
                                alert(response.message);
                            }
                        }
                });
           }
           
        });
     

        $('#deselect_all').click(function() {
                $('#extra_roles input:checkbox').prop('checked', false);
                //get selected value of user group.
                // displayRole();
                //$('#default_page').html('<option value="">--Select One--</option>');
        });
        $('#select_all').click(function() {
            $('#extra_roles input:checkbox').prop('checked', true);
            // displayRole();
        });
    });
function editing_user(data){

    $('#title_user').html('Edit User');
    $('#submitbtn').text('Update');
    $('#add_user_modal').modal('show');
    $('#add_user_modal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
    });
    var user_id = $(data).attr('value');
    // alert(user_id);
        $.ajax({
			url:  "{{ route('add_user')}}"+"/"+user_id,
				type: "POST",
				data:{ "_token": "{{csrf_token()}}"},
				datatype: "json",
				success:function (response){
                    $('#deselect_all').click();
					var user = response.user;
                    var check = []; 
					check = JSON.parse(user.role_id);
                    $.each(check,function(index,val){
                      $('#role_ides_'+val).prop('checked',true);
                        
                    });
					$('#first_name').val(user.first_name);
					$('#last_name').val(user.last_name);
					$('#email').val(user.email);
					// $('#group_description').val(group.group_description);
					if(user.status = 1){ $('#enable').val(user.status);}
					else{ $('#disable').val(user.status);}
                    if(user.gender = 1){ $('#Male').val(user.gender);}
					else{ $('#Female').val(user.gender);}
                    $('#group_id').val(user.group_id);
                    $('#user_id').val(user.user_id);
				}
	    });
         
}
roles = <?php echo json_encode(\App\Model\Role::where('status', 1)->get()->toArray()); ?>;
function displayRole() {
       $('#deselect_all').click();
        var defaultPageDropDownHtml = '<option value="">-- Select One --</option>';
        $('#extra_roles input:checkbox').removeAttr('checked');

	    groupType = document.getElementById("groupType");

        if (groupType != null) {
            strUser = document.getElementById("groupType").value;
            id = <?php echo json_encode(\App\Model\Group::where('status', 1)->get()->toArray()); ?>;
            for (var i = 0; i < id.length; i++) {
                if (strUser == id[i].group_id) {
                    var check = JSON.parse(id[i].role_id);
                    break;
                }
            }

            var options = [];
            var values = [];
            var deselectRoles = [];
            var count = 0, count1 = 0;

            for (var x = 0; x < roles.length; x++) {
                
        if (jQuery.inArray(parseInt(roles[x].role_id), check) != -1 || check.length == 0){
                    options[count] =  {label: roles[x].role_name, value: roles[x].role_id, selected: true} ;

                    values[count] = ''+roles[x].role_id;

                    $('#extra_roles input:checkbox[value=' + roles[x].role_id + ']').prop('checked',true);
                    count++;
                
                    if (roles[x].class) {
                        defaultPageDropDownHtml += '<option value="' + roles[x].url + '" >' + roles[x].role_name + '</option>';
                    }
                
                }
                else{
                    deselectRoles[count1] = ''+roles[x].role_id;
                    count1++;
                }
            }
        }
    
	   $('#default_page').html(defaultPageDropDownHtml);
	
	// $('#public-methods').multiSelect('deselect', deselectRoles);
	// $('#public-methods').multiSelect('select', values);
}
    
</script>
@stop