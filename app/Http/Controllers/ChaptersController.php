<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Chapter;
use App\Season;
use App\Serie;
use Session;

class ChaptersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Chapter::all();
        $title = 'Manage Chapters';
        return view('admin.chapters.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Chapter';
        $type = 'new';
        $series = Serie::all();
        return view('admin.chapters.form',['title' => $title, 'type' => $type, 'series' => $series]);
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
            'season_id' => 'required',
            'title' => 'required',
            'position' => 'required',
            'api_code' => 'required'
        ]);

        $chapter = new Chapter();
        $chapter->season_id = $request->input('season_id');
        $chapter->title = $request->input('title');
        $chapter->position = $request->input('position');
        $chapter->api_code = $request->input('api_code');

        if($chapter->save()){
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('chapters.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seasons = Season::where('serie_id',$id)->get();
        return response()->json($seasons);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Chapter::findorfail($id);
        $title = 'Edit Chapter';
        $type = 'edit';
        $series = Serie::all();
        $seasons = Season::where('serie_id',$data->season->serie->id)->get();

        return view('admin.chapters.form', ['title' => $title, 'data' => $data, 'type' => $type, 'series' => $series, 'seasons' => $seasons]);
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
            'season_id' => 'required',
            'title' => 'required',
            'position' => 'required',
            'api_code' => 'required'
        ]);

        $chapter = Chapter::findorfail($id);
        $chapter->season_id = $request->input('season_id');
        $chapter->title = $request->input('title');
        $chapter->position = $request->input('position');
        $chapter->api_code = $request->input('api_code');

        $chapter = Chapter::findorfail($id);
        $chapter->name = $request->input('name');

        if($chapter->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('chapters.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $chapter = Chapter::findorfail($id); 

        if($chapter->delete()){
            Session::flash('success','Record Deleted Successfully!!');
        }else{
            Session::flash('error', 'Error Deleting Record!!');
        }

        return redirect()->route('chapters.index');
    }
}
