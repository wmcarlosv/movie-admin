<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use App\Category;
use App\Movie;
use DB;
use Illuminate\Support\Facades\Storage;
use Session;
use App\Serie;

class HomeController extends Controller
{
    private $movieData = Array();
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Category::all();
        $movies = Movie::all();
        $series = Serie::all();

        return view('admin.dashboard',['ccat'=>$categories->count(), 'cmov' => $movies->count(), 'cser' => $series->count()]);
    }

    public function importMovies(){
        $title = 'Import Movies';
        $data = [];
        return view('admin.imports.index',['title' => $title, 'data' => $data, 'q' => '']);
    }

    public function setImportMovies(Request $request){

        $request->validate([
            'url' => 'required|url'
        ]);

        $title = 'Import Movies';

        $client = new Client();

        $url = $request->input('url');
        $search_type = $request->input('search_type');

        $crawler = $client->request('GET', $url);
        if(empty($search_type)){
            $crawler->filter('a.Posters-link')->each(function($node){
                $dataArray = $this->getIndividualMovieInfo($node->attr('href'));
                array_push($this->movieData, $dataArray);
            });
        }else{
            array_push($this->movieData, $this->getIndividualMovieInfo($url));
        }

        return view('admin.imports.index',['title' => $title, 'data' => $this->movieData, 'q' => $url, 'ts' => $search_type]);
    }

    public function getIndividualMovieInfo($url){

        global $categories;
        $categories = "";
        $movieData = Array();

        $client = new Client(); 
        $crawler = $client->request('GET', $url);

        $title = $crawler->filter('div.card h1.m-b-5')->text();
        $description = $crawler->filter('div.card div.text-large')->text();
        $poster = $crawler->filter('div.card img')->attr('src');
        $year = $crawler->filter('div.card span.text-semibold')->text();
        $crawler->filter('div.card a[href^="/generos/"]')->each(function($node){
            global $categories;
            $categories.=$node->text().",";
        });
        $categories = substr($categories, 0, -1);
        $urlApiCode = $crawler->filter('div.video-html iframe')->attr('src');

        $movieData['title'] = $title;
        $movieData['description'] = $description;
        $movieData['poster'] = $poster;
        $movieData['year'] = $year;
        $movieData['categories'] = $categories;
        $movieData['api_code'] = $this->getApiCode($urlApiCode);

        return $movieData;
    }

    public function getApiCode($url){
        $parts = explode("/v/", $url);
        $output = 'Not Api Code';
        if(count($parts) == 2){
            $output = $parts[1];
        }
        return $output;
    }

    public function saveMovies(Request $request){

        $errors = 0;

        $titles = $request->input('titles');
        $descriptions = $request->input('descriptions');
        $years = $request->input('years');
        $posters = $request->input('posters');
        $categories = $request->input('categories');
        $api_codes = $request->input('api_codes');

        DB::beginTransaction();

        for($i=0;$i<count($titles);$i++) {
            $movie = Movie::where(DB::raw("UPPER(title)"),'=',strtoupper($titles[$i]))->get();
           
            if($movie->count() == 0){

                $movie = new Movie();
                $movie->title = $titles[$i];
                $movie->description = $descriptions[$i];

                $imgUrl = $posters[$i];
                $imgExtension = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

                $imgName = preg_replace("/[^a-zA-Z]/", "", str_replace(" ", "_", $titles[$i]));

                $imgContents = file_get_contents($imgUrl);
                Storage::put('public/movies/'.$imgName.".".$imgExtension, $imgContents);

                $movie->poster = $imgName.".".$imgExtension;

                $movie->year = $years[$i];
                $movie->api_code = $api_codes[$i];


                if(!$movie->save()){
                    $errors++;
                }else{

                    $cats = explode(",", $categories[$i]);

                    if(count($cats) > 0 and !empty($cats[0]) ){
                        $movie->categories()->detach();
                        foreach($cats as $categoryName){

                            $category = $this->getCategory($categoryName);

                            $movie->categories()->attach($category);
                        }
                    }
                }
            }else{
                $movie = Movie::findorfail($movie[0]->id);
                $movie->title = $titles[$i];
                $movie->description = $descriptions[$i];

                $imgUrl = $posters[$i];
                $imgExtension = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                $imgName = str_replace(" ", "_", $titles[$i]);
                $imgContents = file_get_contents($imgUrl);
                Storage::put('public/movies/'.$imgName.".".$imgExtension, $imgContents);

                $movie->poster = $imgName.".".$imgExtension;

                $movie->year = $years[$i];
                $movie->api_code = $api_codes[$i];


                if(!$movie->update()){
                    $errors++;
                }else{

                    $cats = explode(",", $categories[$i]);
                    if(count($cats) > 0 and !empty($cats[0]) ){
                        $movie->categories()->detach();
                        foreach($cats as $categoryName){

                            $category = $this->getCategory($categoryName);

                            $movie->categories()->attach($category);
                        }
                    }

                }

            }
        }

        if($errors > 0){
            DB::rollback();
            Session::flash('error','Error Importing Movies!!');
        }else{
            DB::commit();
            Session::flash('success',count($titles).' Movies Imported Successfully!!');
        }

        return redirect()->route('import_movies');
    }

    public function getCategory($categoryName){
        $category_id = 0;

        if(isset($categoryName) and !empty($categoryName)){

            $cat = Category::where(DB::raw("UPPER(name)"),'=',strtoupper($categoryName))->get();
            
            if(count($cat) > 0){
                $category_id = $cat[0]->id;
            }else{
                $cat = new Category();
                $cat->name = $categoryName;
                if($cat->save()){
                    $category_id = $cat->id;
                }
            }
        }

        return $category_id;
    }

    public function importSeries(){
        $title = "Import Series";

        return view('admin.imports.series')->with(compact('title'));
    }
}
