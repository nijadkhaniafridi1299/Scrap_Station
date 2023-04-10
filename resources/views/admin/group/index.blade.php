@extends('layout.app')
@section('content')
<main id="main" class="main">
    @if(in_array("add_group", Session::get('roles')))
    <button type="button" class="btn btn-primary btn-sm " id="add_groups">Add</button>
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
        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
            <thead>
            <tr>
                <th>{{__("Group Id")}}</th>
                <th>{{__('Group Name')}}</th>
                <th>{{__('Group Description')}}</th>
                <!-- <th>{{__('Roles')}}</th> -->
                <th>{{__('URL')}}</th>
                <th>{{__('Edit')}}</th>
                <th>{{__('Enable/Disable')}}</th>
            </tr>
            </thead>
            <tbody>
                @for( $i=0, $count = count($groups); $i < $count; $i++)
                <tr>
                    <td>{{$groups[$i]['group_id']}}</td>
                    <td>{{$groups[$i]['group_name']}}</td>
                    <td>{{$groups[$i]['group_description']}}</td>
                    <td>
                    @if (isset($groups[$i]['url']) && isset($groups[$i]['url']['role_name']))
                        {{$groups[$i]['url']['role_name']}}
                    @endif
                    </td>
    
                    <td><a href="javascript:void(0)" value="{{$groups[$i]['group_id']}}" onclick="editing_group(this)"><i class="fa fa-pencil-square"></i></a></td>
                    <td><a href="#" class="status" rel="Delete">
                    <?php if($groups[$i]['status'] == 1) { ?>
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
    <div class="modal fade" id="add_group_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title2" id="title_group"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create_update_group">
                    <div class="row">
                      <div class="col">
                        <label for="user-first-name">{{ __("Group Name") }} <span class="required">*</span>
						</label>
						<input type="text" id="group_name" name="group[group_name]" required="required" class="form-control col-md-3 col-xs-6" placeholder="{{__('Name')}}" value="{{ isset($data['group_name']) ? $data['group_name'] : ''}}">
                      @csrf
                      </div>
                      <div class="col">
                        <label for="user-company-id">{{ __("Group Description") }} <span class="required">*</span>
                        </label> 
						<input type="text" id="group_description" name="group[group_description]" class="form-control col-md-3 col-xs-6" required placeholder="{{__('Description')}}" value="{{ isset($data['group_description']) ? $data['group_description'] : ''}}">
                         <input type="hidden" name="group[group_id]" id="group_id">
                      </div>

                    </div>
                    <br>
                    <div class="row">
                      <div class="col">
                        <label for="area_id">{{__('Group Roles')}}</label>
                        <?php
                            $role_ids = [];
                            if (isset($_POST['group']) && isset($_POST['group']['role_id'])) {
                                $role_ids = $_POST['group']['role_id'];
                            }
                        ?>
                        <div id="extra_roles" style="max-height: 200px; overflow-y: auto; border: 2px solid #CCC !important; padding: 3px;" >
                            @for($i=0, $count = count($roles); $i < $count; $i++)
                                <div><input value="{{ $roles[$i]['role_id'] }}" class="group_id_empty" type="checkbox" id="role_ides_{{$roles[$i]['role_id']}}" name="group[role_id][]" onclick="onExtraRolesChanged(event)"
                                {{ (in_array($roles[$i]['role_id'], $role_ids)) ? ' checked="checked"' : '' }}/> {{ $roles[$i]['role_name'] }}</div>
                            @endfor
                        </div>
                        <br>
                        <button type="button" id='select_all'>{{__('All Roles')}}</a> &nbsp; <button type="button" id='deselect_all'>{{__('Deselect All')}}</a>
                
                       
                      </div>
                      <div class="col">
                            <label for="user-designation-id">{{ __("Default URL") }} <span class="required">*</span></label>
                            <div class="input-group">
                                <?php
                                    $url = null;
                                    if (isset($_POST['group']) && isset($_POST['group']['url'])) {
                                        $url = $_POST['group']['url'];
                                    }
                                ?>
                                <select name="group[url]" class="form-control col-md-3 col-xs-6" id="default_page" >
                                    <option value="">--{{__("Select One")}}--</option>
                                    @if (count($role_ids) > 0)
                                        @for($i=0, $count = count($roles); $i < $count; $i++)
                                            @if (in_array($roles[$i]['role_id'], $role_ids) && isset($roles[$i]['class']) && $roles[$i]['class'] != '')
                                            <option value="{{ $roles[$i]['url'] }}" {{ (isset($url) && $roles[$i]['url'] == $url) ? ' selected ' : ''}}>{{$roles[$i]['role_name']}}</option>
                                            @endif
                                        @endfor
                                    @endif
                                </select>
                            </div>
                      </div>
                    </div><br>
                    <div class="row">
                      <div class="col">
                            <label for="user-status">{{ __("Group Status") }} <span class="required">*</span>
                            </label>
                            
                                <input class="form-check-input" type="radio" name="group[status]" id="enable" value="1" required="required" 
                                    @if (isset($data['status']))
                                        @if ($data['status'] == 1)
                                            checked
                                        @endif
                                    @else 
                                        checked
                                    @endif
                                    >
                                    <label class="form-check-label" for="Enable">
                                    {{__('Enable')}}
                                    </label>
                       </div>
                      <div class="col">
                        <input class="form-check-input" type="radio" name="group[status]" id="disable" value="9" required="required">
                        <label class="form-check-label" for="Disable">
                        {{__('Disable')}}
                        </label>
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
 <!-- End #main -->

<script>
    function editing_group(data){
        $('#title_group').html('Edit Group');
		$('#submitbtn').text('Update');
		// $('#modal-footer_add').html('');
		$('#add_group_modal').modal('show');
		var group_id = $(data).attr('value');
        $.ajax({
			url:  "{{ route('add_group')}}"+"/"+group_id,
				type: "POST",
				data:{ "_token": "{{csrf_token()}}"},
				datatype: "json",
				success:function (response){
                    $('#deselect_all').click();
					var group = response.group;
                    var check = []; 
					check = JSON.parse(group.role_id);
                    //console.log(check);
                    $.each(check,function(index,val){
                      $('#role_ides_'+val).prop('checked',true);
                        
                    });
					$('#group_name').val(group.group_name);
					$('#group_description').val(group.group_description);
					if(group.status = 1){ $('#enable').val(group.status);}
					else{ $('#disable').val(group.status);}
                    $('#group_id').val(group.group_id);
				}
	    });
    }
    $(document).ready(function(){
      $('#add_groups').on('click', function(){
            $('#title_group').html('Add group');
            $('#submitbtn').text('Add');
            $('#add_group_modal').modal('show');
      });
      $('#create_update_group').submit(function(e){
           e.preventDefault();
           if( $('#group_id').val()){
            var group_id = $('#group_id').val();
                $.ajax({
                        url:  "{{ route('update_group')}}"+"/"+group_id,
                            type: "POST",
                            data: $(this).serialize(),
                            datatype: "json",
                            success:function (response){
                                if (response.code == 202){
                                    $('#add_group_modal').modal('hide');
                                    $('#add_group_modal').on('hidden.bs.modal', function () {
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
           else{
                $.ajax({
                        url:  "{{ route('add_group')}}",
                            type: "POST",
                            data: $(this).serialize(),
                            datatype: "json",
                            success:function (response){
                                if (response.code == 201){
                                    $('#add_group_modal').modal('hide');
                                    $('#add_group_modal').on('hidden.bs.modal', function () {
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
        $('#extra_roles input:checkbox').prop('checked',false);

        //get selected value of user group.
        displayRole();
        //$('#default_page').html('<option value="">--Select One--</option>');
    }); 
    $('#select_all').click(function() {
        $('#extra_roles input:checkbox').prop('checked',true);
        //get selected value of user group.
        displayRole();
        //$('#default_page').html('<option value="">--Select One--</option>');
    });
    });
    function onExtraRolesChanged(e) {
  var selected_role_ids = [];//$('#public-methods').val();
  //console.log(selected_role_ids);
  $('#extra_roles input:checkbox:checked').each(function() {
      selected_role_ids.push($(this).val());
  });
//   var currentUrl = '<?php echo (isset($currentUser)? $currentUser['url']: '')?>';
//   var html = '<option value="">--Select One--</option>';
//   for (var x = 0; x < roles.length; x++) {
//     //console.log(jQuery.inArray(roles[x].role_id.toString(), selected_role_ids));
//     if(jQuery.inArray(roles[x].role_id.toString(), selected_role_ids) != -1){
//       //console.log(roles[x].class);
//       if (roles[x].class) {
//         html += '<option value="' + roles[x].url + '"';
//         if (roles[x].url == currentUrl) {
//             html += ' selected="selected" ';
//         }
//         html += '>' + roles[x].role_name + '</option>';
//       }
//     }
//   }
  //console.log(html);
//   $('#default_page').html(html);
}
function displayRole() {
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