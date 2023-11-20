<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use Illuminate\Support\Facades\Storage;
use Session;
use App\Category;

class ChannelsController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Channel::all();
        $title = 'Manage Channels';
        return view('admin.channels.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Channel';
        $type = 'new';
        $categories = Category::where('is_for_channel','=','Y')->get();
        return view('admin.channels.form',['title' => $title, 'type' => $type, 'categories' => $categories]);
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
            'title' => 'required',
            'category_id' => 'required',
            'url' => 'required'
        ]);

        $channel = new Channel();
        $channel->title = $request->input('title');
        $channel->category_id = $request->input('category_id');
        $channel->description = $request->input('description');

        if($request->hasFile('poster')){
            $channel->poster = explode('/',$request->poster->store('public/channels'))[2];
        }else{
            $channel->poster = NULL;
        }

        $channel->url = $request->input('url');

        if($channel->save()){
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('channels.index');
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
        $data = Channel::findorfail($id);
        $title = 'Edit Channel';
        $type = 'edit';
        $categories = Category::where('is_for_channel','=','Y')->get();
        return view('admin.channels.form', ['title' => $title, 'data' => $data, 'type' => $type, 'categories' => $categories]);
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
            'title' => 'required',
            'category_id' => 'required',
            'url' => 'required'
        ]);

        $channel = Channel::findorfail($id);
        $channel->title = $request->input('title');
        $channel->category_id = $request->input('category_id');
        $channel->description = $request->input('description');

        if($request->hasFile('poster')){
            Storage::delete('public/channels/'.$channel->poster);
            $channel->poster = explode('/',$request->poster->store('public/channels'))[2];
        }

        $channel->url = $request->input('url');

        if($channel->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('channels.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $channel = Channel::findorfail($id); 
        /*if($channel->status == 'A'){
            $channel->status = 'I';
        }else{
           $channel->status = 'A'; 
        }*/
        Storage::delete('public/channels/'.$channel->poster);
        if($channel->delete()){
            Session::flash('success','Record Deleted Successfully!!');
        }else{
            Session::flash('error', 'Error Deleting Record!!');
        }

        return redirect()->route('channels.index');
    }

    public function getByPhone($category_id = null){

        if(isset($category_id) and !empty($category_id)){

            $channels = Channel::where([
                ['status','=','A'],
                ['category_id','=',$category_id]
            ])->get();

        }else{

            $channels = Channel::where('status','=','A')->get();
            
        }
        

        return response()->json($channels);
    }

    public function getCategories(){
      $categories = Category::where('is_for_channel','=','Y')->get();
      return response()->json($categories);
    }
}
