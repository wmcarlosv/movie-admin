<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Application;
use Session;

class ApplicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Application::all();
        $title = 'Manage Applications';
        return view('admin.applications.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Application';
        $type = 'new';

        return view('admin.applications.form',['title' => $title, 'type' => $type]);
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
            'about' => 'required',
            'version' => 'required',
            'app_code' => 'required'
        ]);

        $application = new Application();
        $application->name = $request->input('name');
        $application->about = $request->input('about');
        $application->version = $request->input('version');
        $application->play_store_url = $request->input('play_store_url');
        $application->privacy_policy = $request->input('privacy_policy');
        $application->url_qualify = $request->input('url_qualify');
        $application->url_more_apps = $request->input('url_more_apps');
        $application->app_code = $request->input('app_code');

        if($application->save()){
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('applications.index');
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
        $data = Application::findorfail($id);
        $title = 'Edit Application';
        $type = 'edit';
        return view('admin.applications.form', ['title' => $title, 'data' => $data, 'type' => $type]);
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
            'about' => 'required',
            'version' => 'required',
            'app_code' => 'required'
        ]);

        $application = Application::findorfail($id);
        $application->name = $request->input('name');
        $application->about = $request->input('about');
        $application->version = $request->input('version');
        $application->play_store_url = $request->input('play_store_url');
        $application->privacy_policy = $request->input('privacy_policy');
        $application->url_qualify = $request->input('url_qualify');
        $application->url_more_apps = $request->input('url_more_apps');
        $application->app_code = $request->input('app_code');

        if($application->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('applications.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $application = Application::findorfail($id); 

        if($application->delete()){
            Session::flash('success','Record Deleted Successfully!!');
        }else{
            Session::flash('error', 'Error Deleting Record!!');
        }

        return redirect()->route('applications.index');
    }

    public function getData($code){
        $application = Application::select('name','about','version','play_store_url','privacy_policy','url_qualify','url_more_apps')->where('app_code','=',$code)->first();

        return response()->json($application);
    }
}
