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

    public function getByPhone($order = 'last_upload'){

        if($order == 'last_upload'){
          $movies = Movie::where('status','=','A')->orderBy('created_at','DESC')->paginate(18);
        }

        if($order == 'Year'){
          $movies = Movie::where('status','=','A')->orderBy('year','DESC')->paginate(18);
        }

        if($order == 'populars'){
          $movies = Movie::where('status','=','A')->orderBy('views','DESC')->orderBy('downloads','DESC')->paginate(18);
        }

        
        return response()->json($movies);
    }

    public function movieById($id = NULL){
        $movie = Movie::findorfail($id);
        return response()->json($movie);
    }

    public function getCategoriesByMovie($movie_id){
        $categories = DB::select(
            DB::raw("SELECT
                        c.name,
                        c.id
                        from movie_categories mc
                        inner join categories as c on (c.id = mc.category_id)
                    WHERE mc.movie_id = ".$movie_id));

        return response()->json($categories);
    }

    public function getMoviesByCategories(Request $request){
        $categories = explode(',',$request->input('categories'));
        $cc = count($categories);
        $string = "";
        foreach ($categories as $key => $value) {
            if( ($key+1) < $cc ){
                $string.="'".$value."',";
            }else{
                $string.="'".$value."'";
            }   
        }

        $currentMovieID = $request->input('current_id');

        $movies = DB::select(DB::raw("select 
                                            m.id,
                                            m.title,
                                            m.poster,
                                            m.api_code,
                                            coalesce(m.views, 0) views,
                                            coalesce(m.downloads, 0) downloads
                                      from movie_categories mc
                                            inner join movies m on (m.id = mc.movie_id)
                                            inner join categories c on (c.id = mc.category_id)
                                      where c.name in (".$string.") and m.id <> $currentMovieID
                                            group by m.id, m.title, m.poster, m.api_code, views, downloads order by (views+downloads) DESC
                                      limit 5"));

        return response()->json($movies);
    }

    public function getCategories(){
      $categories = DB::select(DB::raw("SELECT 
                                          c.id, 
                                          c.name,
                                          (select 
                                              count(mc.id) 
                                            from movie_categories as mc 
                                            inner join movies as m on (m.id = mc.movie_id)
                                            where m.status = 'A' and mc.category_id = c.id
                                          ) as qty
                                        FROM categories c 
                                        ORDER BY c.name ASC"));

      return response()->json($categories);
    }

    public function searchData($type, $q){

      $data = [];

      if($type == "category"){

        $data = DB::table('movies')
              ->select('movies.id','movies.title','movies.poster','movies.views','movies.downloads','movies.api_code')
              ->join('movie_categories','movies.id','=','movie_categories.movie_id')
              ->where('movie_categories.category_id','=',$q)
              ->where('movies.status','=','A')->paginate(18);
      }

      if($type == "search"){
        $data = Movie::where('title','like','%'.$q.'%')->orWhere('description','like','%'.$q.'%')->orWhere('year','like','%'.$q.'%')->paginate(18);
      }

      return response()->json($data);
    }
}
