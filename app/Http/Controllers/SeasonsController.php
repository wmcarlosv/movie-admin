<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Season;
use App\Serie;
use Session;

class SeasonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Season::all();
        $title = 'Manage Seasons';
        return view('admin.seasons.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Season';
        $type = 'new';
        $series = Serie::all();
        return view('admin.seasons.form',['title' => $title, 'type' => $type, 'series' => $series]);
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
            'serie_id' => 'required',
            'title' => 'required',
            'position' => 'required'
        ]);

        $season = new Season();
        $season->serie_id = $request->input('serie_id');
        $season->title = $request->input('title');
        $season->position = $request->input('position');

        if($season->save()){
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('seasons.index');
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
        $data = Season::findorfail($id);
        $title = 'Edit Season';
        $type = 'edit';
        $series = Serie::all();
        return view('admin.seasons.form', ['title' => $title, 'data' => $data, 'type' => $type, 'series' => $series]);
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
            'serie_id' => 'required',
            'title' => 'required',
            'position' => 'required'
        ]);

        $season = Season::findorfail($id);
        $season->serie_id = $request->input('serie_id');
        $season->title = $request->input('title');
        $season->position = $request->input('position');

        if($season->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('seasons.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $season = Season::findorfail($id); 

        if($season->delete()){
            Session::flash('success','Record Deleted Successfully!!');
        }else{
            Session::flash('error', 'Error Deleting Record!!');
        }

        return redirect()->route('seasons.index');
    }
}
