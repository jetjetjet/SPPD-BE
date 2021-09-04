<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Validator;

use App\Helpers\Utils;

class UserControllerbak extends Controller
{
  public function grid(Request $request)
	{
		$results = $this->responses;

		$results['data'] = User::all();
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function getDataById(Request $request, $id)
	{
		$results = $this->responses;
		$user = User::find($id);
		$user->role = $user->getRoleNames()[0] ?? null;

		$results['data'] = $user;
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}

	public function save(Request $request)
	{
		$results = $this->responses;

		$inputs = $request->all();
		$rules = array(
			'nip' => 'required|unique:users,nip',
			'email' => 'required',
			'full_name' => 'required',
			'jenis_kelamin' => 'required',
		);

		$validator = Validator::make($inputs, $rules);
		// Validation fails?
		if ($validator->fails()){
      $results['messages'] = Array($validator->messages()->first());
      return response()->json($results, $results['state_code']);
    }

		try{
			DB::beginTransaction();
			$defaultPassword = bcrypt('12345678');
			$id = $request->id ?? null;
			$user = User::updateOrCreate([
				'nip' => $inputs['nip'],
				'full_name' => $inputs['full_name'],
				'password' => $defaultPassword,
				'email' => $inputs['email'],
				'jenis_kelamin' => $inputs['jenis_kelamin'],
				'address' => $inputs['address'] ?? null,
				'phone' => $inputs['phone'] ?? null,
				'ttl' => $inputs['ttl'] ?? null
			],[
				'id' => $id
			]);

			$user->assignRole('Super Admin');
			if($user->wasRecentlyCreated){
				$file = Utils::imageUpload($request, 'profile');
				if($file != null) $user->path_foto = $file->path;

				array_push($results['messages'], 'Berhasil menambahkan user baru.');
			}else{
				array_push($results['messages'], 'Berhasil mengubah user.');
				$role->syncPermissions($request->roleperms);
			}
			
			$results['success'] = true;
			$results['state_code'] = 200;
			DB::commit();
		}catch(\Exception $e){
			DB::rollBack();
			array_push($results['messages'], $e->getMessage());
		}
		return $results;
	}

	public function delete($id)
	{
		$results = $this->responses;
		if($id == 1){
			array_push($results['messages'], 'User ini tidak dapat dihapus.');
			return response()->json($results, $results['state_code']);
		}

		$role = User::delete($id);

		array_push($results['messages'], 'Berhasil menghapus peran.');
		$results['state_code'] = 200;
		$results['success'] = true;

		return response()->json($results, $results['state_code']);
	}
}
