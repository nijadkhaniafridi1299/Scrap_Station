<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Group;
use App\Model\Role;
class GroupController extends Controller
{
    function index(Request $request){
        $roles = Role::orderBy('role_name', 'asc')->get()->toArray();
        $groups = Group::with('url')->get()->toArray();
        return view("admin.group.index", [
            "title" => "User Groups",
            "groups" => $groups,
            "roles" => $roles,
        ]);
    }
    public function create(Request $request, $group_id = null)
    {
        if(!is_null($group_id)){
            $group = Group::find($group_id);
            return response()->json([
                'group'=>$group
            ]);
        }
        $data1 = $request->all();
        $data = $request->input('group');
      

        $request_log_id = $data1['request_log_id'];
        $message = "Group of Roles added successfully"; 
        $code = 201;
        unset($data1['request_log_id']);
        $model = new Group();
        $group = $model->add($data);
        if (!is_object($group )) {
            $errors = \App\Message\Error::get('group.add');
        }
        if (isset($errors) && count($errors) > 0) {
            
            return respondWithError($errors,$request_log_id); 
        }
        return respondWithSuccess($group, 'GROUP', $request_log_id, $message, $code);
    }
    public function update(Request $request, $group_id = null)
    {
        $errors = [];
        $message = "Group Updated successfully";  $code = 202;
        $data1 = $request->all();
        $data = $request->input('group');
        $request_log_id = $data1['request_log_id'];
        unset($data['request_log_id']);
        $group = new Group();
        $group = $group->change($data,$group_id);
        if (!is_object($group)) {
            $errors = \App\Message\Error::get('group.change');
        }
        if (isset($errors) && count($errors) > 0) {
            
            return respondWithError($errors,$request_log_id); 
        }
        return respondWithSuccess($group, 'GROUP', $request_log_id, $message, $code);
    }
}
