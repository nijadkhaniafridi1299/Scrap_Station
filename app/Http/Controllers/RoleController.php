<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Role;

class RoleController extends Controller
{
    public function index(Request $request){

        $roles = Role::get()->toArray();
        $role = Role::pluck('url')->toArray();
        $urls = [];
        $icons = Role::select('class')->where('class', '<>', '')->distinct()->get()->toArray();
        
        $routes = \Route::getRoutes()->get();
    
        foreach($routes as $route) {
            $route_name = $route->getName();
            if (isset($route_name) && stripos($route_name, 'debug') === FALSE && !in_array($route_name, $role)) {
                $urls[] = $route_name;
            }
        }
        sort($urls);
      
        return view("admin.role.index", [
            "title" => "User Roles",
            "roles" => $roles,
            "urls"=>$urls,
            "icons"=>$icons
        ]);
    }
    public function Create(Request $request, $id = null){
        if(!is_null($id)){
            $role = Role::find($id);
            return response()->json([
                'role'=>$role
            ]);
        }
        $message = "Role added successfully"; $code = 201;
        $data1 = $request->all();
        $data = $request->input('role');
        $request_log_id = $data1['request_log_id'];
        
        unset($data['request_log_id']);
        if(!isset($data['is_menu'])){
            $data['is_menu'] = 0;
        }
        $displayInMenu = $request->input('is_menu');   
        if ($displayInMenu == 1) {
            if (!isset($data['class'])) {
                $errors[] = "Please select display menu icon.";
            }
        }
        $model = new Role();
        $role = $model->add($data);
        if (!is_object($role)) {
            $errors = \App\Message\Error::get('role.add');
        }
        if (isset($errors) && count($errors) > 0) {
            
            return respondWithError($errors,$request_log_id); 
        }
        return respondWithSuccess($role, 'USERROLE', $request_log_id, $message, $code);
    }
    public function Update(Request $request, $role_id = null){
        $errors = [];
        $message = "Role Updated successfully";  $code = 202;
        $data1 = $request->all();
        $data = $request->input('role');
        $request_log_id = $data1['request_log_id'];
        
        unset($data['request_log_id']);
        if(!isset($data['is_menu'])){

            $data['is_menu'] = 0;
        }
        $displayInMenu = $request->input('is_menu');
        if ($displayInMenu == 1) {
            if (!isset($data['class'])) {
                $errors[] = "Please select display menu icon.";
            }
        }
        $role = new Role();
        $role = $role->change($data,$role_id);
        if (!is_object($role)) {
            $errors = \App\Message\Error::get('role.change');
        }
        if (isset($errors) && count($errors) > 0) {
            
            return respondWithError($errors,$request_log_id); 
        }
        return respondWithSuccess($role, 'USERROLE', $request_log_id, $message, $code);
      
    }
}
