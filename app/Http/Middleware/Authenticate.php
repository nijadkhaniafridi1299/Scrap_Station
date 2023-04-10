<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Auth;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }

    }

    public function authenticate($request,array $guards)
    {
        $route = $request->route();

		if(is_array($route)){
			list($controller, $method) = explode('@', $route[1]['uses']);
		}
		else{
			list($controller, $method) = explode('@', $route->action['uses']);
		}

        $body = $request->getContent();//json_encode($request->getContent(), JSON_UNESCAPED_UNICODE);

        try {
            $user = Auth::user();
            $data['user_id'] = (isset($user)) ? $user->user_id : null;
            $data['body'] = $body;
            $data['fname'] = $method;
            $data['controller'] = $controller;

            $fleet_request = new \App\Model\FleetRequest();
            $fleet_request = $fleet_request->add($data);

            $modules = \App\Model\FleetModule::get()->pluck('key')->toArray();

            $splitted_arr = explode("\\", $controller);
            $cont = $splitted_arr[count($splitted_arr) - 1];
            $cont = strtolower($cont);
            $cont = str_replace('controller', '', $cont);

            foreach($modules as $module) {
                if ($cont === strtolower($module)) {
                    $request->merge(['request_log_id' => $fleet_request->id]);
                }
            }
            $request->merge(['user_id' => $user->user_id]);
            $request->merge(['created_source' => "user"]);
            $request->merge(['created_by' => $user->user_id]);
            $request->merge(['updated_source' => "user"]);
        }
        catch (Exception $e) {
            $this->redirectTo($request);
        }
    }
}
