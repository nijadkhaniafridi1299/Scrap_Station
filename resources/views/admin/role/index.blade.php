@extends('layout.app')
@section('content')
  <!-- End Sidebar-->
<main id="main" class="main">
    
    @if(in_array("add_role", Session::get('roles')))
    <button type="button" class="btn btn-primary btn-sm " id="add_role">Add</button>
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
		<table id="datatable" class="table table-striped" cellspacing="0">
			<thead>
			<tr>
				<th>{{__("Role Id")}}</th>
				<th>{{__('Role Name')}}</th>
				<th>{{__('Role Key')}}</th>
				<th>{{__('URL')}}</th>
				<th>{{__('Action')}}</th>
				<th>{{__('Date')}}</th>
			</tr>
			</thead>
			<tbody>
				@for( $i=0, $count = count($roles); $i < $count; $i++)
				<tr>
					<td>{{$roles[$i]['role_id']}}</td>
					<td>{{$roles[$i]['role_name']}}</td>
					<td>{{$roles[$i]['role_key']}}</td>
					<td>{{$roles[$i]['url']}}</td>
	
					<td><a href="javascript:void(0)" value="{{$roles[$i]['role_id']}}" onclick="editing_role(this)"><i class="fa fa-pencil-square"></i></a>
					 <a href="javascript:void(0)" class="status" rel="Delete">	
					<?php if($roles[$i]['status'] == 1) { ?>
						{{__('Disable')}}</a></td>
					<?php } else { ?>
						{{__('Enable')}}</a></td>
					<?php } ?>
					<td>{{$roles[$i]['created_at']}}</td>
				</tr>
				@endfor
			</tbody>
		</table>
	 </div>
</fieldset>

<div class="modal fade" id="add_role_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title2" id="title_role"></h5>
		  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
		  <form id="create_role">
			<div class="row">
			  <div class="col">
				<label class="" for="user-first-name">{{ __("Role Name") }} <span class="required">*</span>
				</label>
				<input type="text" name="role[role_name]" id="role_name" required="required" class="form-control " placeholder="{{__('Name')}}" value="">

				<div class="lengthmessage">
				 @csrf
				</div>
			  </div>
			
			</div>
			<br>
			<div class="row">
			  <div class="col">
				<label class="" for="user-status">{{ __("Role Status") }} <span class="required">*</span>
				</label>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="role[status]" id="enable" value="1" required="required" 
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
				<div class="form-check">
					<input class="form-check-input" type="radio" name="role[status]" id="disable" value="9" required="required">
					<label class="form-check-label" for="Disable">
					{{__('Disable')}}
					</label>
				</div>
			  </div>
			  <div class="col">
				<label for="user-email">{{ __("URL") }} <span class="required">*</span>
				</label>
				<select name="role[url]" class="form-control" id="url" required>
					<option value="">--{{__("Select One")}}--</option>
					@foreach($urls as $url)
					<option value="{{$url}}" {{ (isset($_POST['role']) && isset($_POST['role']['url']) && $_POST['role']['url'] == $url) ? 'selected' : ''}}>{{$url}}</option>
					@endforeach
				</select>
				<input type="hidden" id="role_id" name="role[role_id]" value="">
			  </div>
			</div><br>
			<div class="row">
			  <div class="col">
				<label class="" for="user-email">{{ __("Display Icon") }} <span class="required">*</span>

				<select name="role[class]" class="form-control" id="icon">
					<option value="">--{{__("Select One")}}--</option>
					@foreach($icons as $icon)
					<option value="{{$icon['class']}}" {{ (isset($_POST['role']) && isset($_POST['role']['class']) && $_POST['role']['class'] == $icon['class']) ? 'selected' : ''}}>
					<i class="{{$icon['class']}}"></i> {{ $icon['class'] }} </option>
					@endforeach
				</select>
			  </div>
			  <div class="col">
				<label class="" for="user-designation-id">{{ __("Display In Menu") }}
				</label>
				<input type="checkbox" name="role[is_menu]"  value="1" checked style="margin:14px 0 0;" id="display_in_menu_chk"  class="empty_is_menu">
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
    $(document).ready(function(){
		$('#add_role').on('click', function(){
                $('#title_role').html('Add Role');
				$('#submitbtn').text('Add');
				// $('#modal-footer_add').html('');
				$('#add_role_modal').modal('show');
		});
		$('#display_in_menu_chk').change(function() {
		
			if ($(this).is(':checked')) {
			$(this).val('1'); // set value to 1 if checkbox is checked
			} else {
			$(this).val('0'); // set value to 0 if checkbox is unchecked
			}
		});

        $('#create_role').submit(function(e){
           e.preventDefault();
		   if($('#role_id').val()){
			var role_id = $('#role_id').val();
			$.ajax({
					url:  "{{ route('update_role')}}"+"/"+role_id,
						type: "POST",
						data: $(this).serialize(),
						datatype: "json",
						success:function (response){
							if (response.code == 202){
								$('#add_role_modal').modal('hide');
								$('#add_role_modal').on('hidden.bs.modal', function () {
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
					url:  "{{ route('add_role')}}",
						type: "POST",
						data: $(this).serialize(),
						datatype: "json",
						success:function (response){
							if (response.code == 201){
								$('#add_role_modal').modal('hide');
								$('#add_role_modal').on('hidden.bs.modal', function () {
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
		
});
function editing_role(data){

	$('#title_role').html('Edit Role');
		$('#submitbtn').text('Update');
		// $('#modal-footer_add').html('');
		$('#add_role_modal').modal('show');
		var role_id = $(data).attr('value');
		
		$.ajax({
			url:  "{{ route('add_role')}}"+"/"+role_id,
				type: "POST",
				data:{ "_token": "{{csrf_token()}}"},
				datatype: "json",
				success:function (response){
					var role = response.role;
					console.log( role);
                    $('.empty_is_menu').empty();
					$('#role_name').val(role.role_name);
					if(role.status = 1){ $('#enable').val(role.status);}
					else{ $('#disable').val(role.status);}
                    $('#role_id').val(role.role_id);
					if(role.is_menu == 1){ 
					    $('#display_in_menu_chk').prop('checked', true);
					    $('#display_in_menu_chk').val(role.is_menu);
					}
					else if (role.is_menu == 0){
						$('#display_in_menu_chk').prop('checked', false); 
						$('#display_in_menu_chk').val(role.is_menu);

					}
					$('#icon').val(role.class);
					$('#url').val(role.url);
					// $("#url").val(role.url).change();
					//$('#url option[value='+role.url+']').prop('selected',true);
				}
	});
				
}
</script>
@stop