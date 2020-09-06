<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;
use Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::all();
        $title = 'Manage Users';
        return view('admin.users.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $title = 'New User';
        $type = 'new';

        return view('admin.users.form',['title' => $title, 'type' => $type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'password' => 'required'
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->client_id = $request->input('client_id');
        $user->password = bcrypt($request->input('password'));

        if($user->save()){
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::findorfail($id);
        $title = 'Edit User';
        $type = 'edit';
        return view('admin.users.form', ['title' => $title, 'data' => $data, 'type' => $type]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required'
        ]);

        $user = User::findorfail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->client_id = $request->input('client_id');
        if( !empty($request->input('password')) ){
            $user->password = bcrypt($request->input('password'));
        }

        $user->role = $request->input('role');

        if($user->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findorfail($id); 
        if($user->status == 'A'){
            $user->status = 'I';
        }else{
           $user->status = 'A'; 
        }

        if($user->update()){
            Session::flash('success','Record Status Change Successfully!!');
        }else{
            Session::flash('error', 'Error Changing Status Record!!');
        }

        return redirect()->route('users.index');
    }

    public function profile(){
        $current_page = null;
        $file = 'point.txt';
        if(Storage::exists($file)){
            $current_page = trim(Storage::get($file));
        }
        return view('admin.users.profile',['title' => 'Manage Profile', 'current_page' => $current_page]);
    }

    public function update_profile(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);

        $user = User::findorfail(Auth::user()->id);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if($user->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('profile');
    }

    public function change_password(Request $request){
        $request->validate([
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = User::findorfail(Auth()->user()->id);

        $user->password = bcrypt($request->password);

        if($user->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('profile');
    }
}
