<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Movie;
use App\Category;
use Illuminate\Support\Facades\Storage;
use Session;
use DB;

class MoviesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = (isset($_GET['q']) and !empty($_GET['q'])) ? $_GET['q']: '';

        if(isset($q) and !empty($q)){
            $data = Movie::where('title','like','%'.$q.'%')->orWhere('description','like','%'.$q.'%')->orWhere('year','like','%'.$q.'%')->paginate(5);
        }else{
            $data = Movie::paginate(5);
        }

        $title = 'Manage Movies';
        return view('admin.movies.index',['title' => $title, 'data' => $data, 'q' => $q]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Movie';
        $type = 'new';
        $categories = Category::all();
        return view('admin.movies.form',['title' => $title, 'type' => $type, 'categories' => $categories]);
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
            'year' => 'required',
            'api_code' => 'required'
        ]);

        $movie = new Movie();
        $movie->title = $request->input('title');
        $movie->description = $request->input('description');
        $movie->year = $request->input('year');
        $movie->api_code = $request->input('api_code');

        if($request->hasFile('poster')){
            $movie->poster = explode('/',$request->poster->store('public/movies'))[2];
        }else{
            $movie->poster = NULL;
        }

        if($movie->save()){
            $categories = $request->input('categories');
            foreach($categories as $category){
                $movie->categories()->attach($category);
            }
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('movies.index');
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
        $data = Movie::findorfail($id);
        $title = 'Edit Movie';
        $type = 'edit';
        $categories = Category::all();
        return view('admin.movies.form', ['title' => $title, 'data' => $data, 'type' => $type, 'categories' => $categories]);
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
            'year' => 'required',
            'api_code' => 'required'
        ]);

        $movie = Movie::findorfail($id);
        $movie->title = $request->input('title');
        $movie->description = $request->input('description');
        $movie->year = $request->input('year');
        $movie->api_code = $request->input('api_code');

        if($request->has('poster')){
            Storage::delete('public/movies/'.$movie->poster);
            $movie->poster = explode('/',$request->poster->store('public/movies'))[2];
        }

        if($movie->update()){
            $movie->categories()->detach();
            $categories = $request->input('categories');
            foreach($categories as $category){
                $movie->categories()->attach($category);
            }
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('movies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movie = Movie::findorfail($id); 
        if($movie->status == 'A'){
            $movie->status = 'I';
        }else{
           $movie->status = 'A'; 
        }

        if($movie->update()){
            Session::flash('success','Record Status Change Successfully!!');
        }else{
            Session::flash('error', 'Error Changing Status Record!!');
        }

        return redirect()->route('movies.index');
    }
}
