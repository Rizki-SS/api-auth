<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response::json
     */
    public function index()
    {
        $users = User::all(); 
        return response()->json(['users' => $users], 200); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response::json
     */
    public function store(UserRequest $request)
    {
        try {
            $request->validated();

            $input = $request->all(); 
            $input['password'] = bcrypt($input['password']); 
            $user = User::create($input); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
            return response()->json(['success'=>$success], 200); 
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage()   
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response::json
     */
    public function show(int $id)
    {
        $user = User::find($id);
        if ($user == [])
            return response()->json(['message' => 'user not found'], 404);

        $user['roles'] = $user->roles;
        return response()->json(['data'=>$user], 200); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response::json
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $request->validated();

            $input = $request->all();
            $user = User::find($id);
            if ($user == [])
                return response()->json(['message' => 'user not found'], 404);

            if(!empty($input['password'])){ 
                $input['password'] = bcrypt($input['password']);
            }else{
                $input = Arr::except($input,array('password'));    
            }

            $user->update($input);
            $user->assignRole($input['roles']);

            return response()->json(['user'=>$user], 200); 
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage()   
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
