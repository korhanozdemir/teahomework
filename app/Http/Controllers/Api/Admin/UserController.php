<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        $paginage =  Setting::where('name', 'adminCpPaginate')->get()->pluck('value')->first();

        return User::
        with("roleName")
        ->paginate($paginage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'code' => 'required',
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'role' => 'required|numeric'
        ];
        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return response([
            'data' => $user,
            'message' => 'Kullanıcı Oluşturuldu'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $validationRules = [
            'code' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'numeric'
        ];

        $validator = Validator::make($request->all(),$validationRules);

        if($validator ->fails()){
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        $user->code = $request->code ? $request->code : $user->code;
        $user->name = $request->name ? $request->name : $user->name;
        $user->password = $request->password ? bcrypt($request->password) : $user->password;
        $user->email = $request->email ? $request->email : $user->email;
        $user->role = $request->role ? $request->role : $user->role;
        $user->save();

        return response([
            'data' => $user,
            'message' => 'Kullanıcı Güncellendi'
        ]);




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response([
            'message' => 'Kullanıcı Silindi'
        ]);


    }
}
