<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Serie;
use App\Category;
use Storage;
use DB;
use Session;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Serie::all();
        $title = 'Manage Series';
        return view('admin.series.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Serie';
        $type = 'new';
        $categories = Category::all();
        return view('admin.series.form',['title' => $title, 'type' => $type, 'categories' => $categories]);
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
            'description' => 'required',
            'year' => 'required'
        ]);

        DB::beginTransaction();
        $errors = 0;
        $serie = new Serie();
        $serie->title = $request->input('title');
        $serie->description = $request->input('description');
        $serie->year = $request->input('year');

        if($request->hasFile('poster')){
            $serie->poster = explode('/',$request->poster->store('public/series'))[2];
        }else{
           $serie->poster = NULL; 
        }

        if($serie->save()){
            $categories = $request->input('categories');
            foreach($categories as $category){
                $serie->categories()->attach($category);
            }
        }else{
            $errors++;
        }

        if($errors > 0){
            DB::rollback();
            Session::flash('error','Error Inserting Record!!');
        }else{
            DB::commit();
            Session::flash('success','Record Inserted Successfully!!');
        }

        return redirect()->route('series.index');
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
        $data = Serie::findorfail($id);
        $title = 'Edit Serie';
        $type = 'edit';
        $categories = Category::all();
        return view('admin.series.form', ['title' => $title, 'data' => $data, 'type' => $type, 'categories' => $categories]);
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
            'description' => 'required',
            'year' => 'required'
        ]);

        DB::beginTransaction();
        $errors = 0;

        $serie = Serie::findorfail($id);
        $serie->title = $request->input('title');
        $serie->description = $request->input('description');
        $serie->year = $request->input('year');

        if($request->hasFile('poster')){
            Storage::delete('public/series/'.$serie->poster);
            $serie->poster = explode('/',$request->poster->store('public/series'))[2];
        }

        if($serie->update()){

            $serie->categories()->detach();
            $categories = $request->input('categories');

            foreach($categories as $category){
                $serie->categories()->attach($category);
            }
        }else{
            $errors++;
        }

        if($errors > 0){
            DB::rollback();
            Session::flash('error','Error Updating Record!!');
        }else{
            DB::commit();
            Session::flash('success','Record Updated Successfully!!');
        }

        return redirect()->route('series.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $serie = Serie::findorfail($id); 
        Storage::delete('public/series/'.$serie->poster);
        if($serie->delete()){
            Session::flash('success','Record Deleted Successfully!!');
        }else{
            Session::flash('error', 'Error Deleting Record!!');
        }

        return redirect()->route('series.index');
    }
}
