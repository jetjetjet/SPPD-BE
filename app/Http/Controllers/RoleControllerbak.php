<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use DB;
use Validator;

class RoleControllerbak extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;
		$results['data'] = Role::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}


	public function getDataById(Request $request, $id)
	{
		$data = Role::find($id);
		$perms = Permission::all()->pluck('name');
		
		$hasPermission = DB::table('role_has_permissions')
			->select('permissions.name')
			->join('permissions', 'role_has_permissions.permission_id', 'permissions.id')
			->where('role_id', $id)->get()->pluck('name')->all();
		
		$arrPerms = array();
		foreach($perms as $key => $perm){
			$module = explode('-', $perm);
			$uppModule = ucwords($module[0]);
			if(!isset($arrPerms[$uppModule])){
				$arrPerms[$uppModule] = $uppModule; 
				
				$arrPerms[$uppModule] = array();
				$arrPerms[$uppModule]['module'] = $uppModule;
				$arrPerms[$uppModule]['actions'] = array();
			}

			$action = array();
			$action['raw'] = $module[1];
			$action['value'] = $perm;
			$action['active'] = in_array($perm, $hasPermission) ? true : false;
			array_push($arrPerms[$uppModule]['actions'], $action);
		}
		ksort($arrPerms);
		
		$data->user = $data->id != null ? User::role($data->name)->get() : [] ;

		$results = $this->responses;
		$results['data'] = array(
			'header' => $data,
			'perms' => $arrPerms
		);
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function getPermission(Request $request)
	{
		$results = $this->responses;
		$perms = Permission::all()->pluck('name');

		$arrPerms = array();
		foreach($perms as $key => $perm){
			$module = explode('-', $perm);
			$uppModule = ucwords($module[0]);
			if(!isset($arrPerms[$uppModule])){
				$arrPerms[$uppModule] = $uppModule; 
				
				$arrPerms[$uppModule] = array();
				$arrPerms[$uppModule]['module'] = $uppModule;
				$arrPerms[$uppModule]['actions'] = array();
			}

			$action = array();
			$action['raw'] = $module[1];
			$action['value'] = $perm;
			$action['active'] = false;
			array_push($arrPerms[$uppModule]['actions'], $action);
		}
		ksort($arrPerms);

		$results['data'] = $arrPerms;
		$results['state_code'] = 200;
		$results['success'] = true;
		
		return response()->json($results, $results['state_code']);
	}

	public function save(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'name' => 'required|unique:roles,name',
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }
		
		//Save or Update
		try{
			$id = $request->id ?? null;
			$role = Role::updateOrCreate([
				'guard_name' => 'sanctum',
				'name' => $request->name
			],[
				'id' => $id
			]);

			//get all permission from UI
			$perms = [];
			$inputPerms = json_decode($request->perms);
			foreach($inputPerms as $arPerm){
				foreach($arPerm->actions as $act){
					if($act->active) array_push($perms, $act->value);
				}
			}

			if($role->wasRecentlyCreated){
				$role->givePermissionTo($perms);
				array_push($results['messages'], 'Berhasil menambahkan peran baru.');
			} else {
				$role->syncPermissions($perms);
				array_push($results['messages'], 'Berhasil mengubah peran.');
			}
			$results['success'] = true;
			$results['state_code'] = 200;
		}catch(\Exception $e){

			array_push($results['messages'], $e->getMessage());
		}

		return response()->json($results, $results['state_code']);
	}

	public function delete($id)
	{
		$results = $this->responses;
		if($id == 1){
			array_push($results['messages'], 'Peran ini tidak dapat dihapus.');
			return response()->json($results, $results['state_code']);
		}

		$role = Role::destroy($id);

		array_push($results['messages'], 'Berhasil menghapus peran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
