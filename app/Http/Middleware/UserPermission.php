<?php

namespace App\Http\Middleware;


use Closure;
use App\Models\User;
use App\Models\RolePrivilege;
use App\Models\Privilege;
use App\Exceptions\CustomException;

class UserPermission
{
    public function handle($request, Closure $next)
    {
        $controller = class_basename($request->route()->getAction()['controller']);

        $user = User::find($request->_session['id']);

        if (!$user) {
            return response([
                "message" => "User " . $request->_session['id'] . " not found!!"
            ], 401);
        }

        if (!$this->isUserHavePriviledge($user, $controller)) {
            return response([
                "message" => "You don't have permission to do this action!!"
            ], 401);
        }

        return $next($request);
    }


    public function isUserHavePriviledge($user, $controller)
    {
        $userRole = $user->role;
        $privilege = Privilege::where('description', $controller)->first();

        if (!$userRole) {
            throw new CustomException("User Have No Role : setup user role and relogin");
        }

        if (!$privilege) {
            throw new CustomException("Controller Method Not Registered On User Permission");
        }

        $container = RolePrivilege::where('role_id', $userRole->id)->where('privilege_id', $privilege->id)->get()->toArray();

        if ($container) {

            return true;
        }

        return false;
    }

    public function getViewOnly()
    {
        return [
            "Api\ActivityController@create",
        ];
    }
}
