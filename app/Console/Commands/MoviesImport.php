<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use App\Category;
use App\Movie;
use DB;
use Illuminate\Support\Facades\Storage;

class MoviesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:movies';

    protected $movieData = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import List Movies!!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->setImportMovies();
        $this->saveMovies();
    }

    public function setImportMovies(){
        $init = 1;
        $file = 'point.txt';

        if(Storage::exists($file)){
            
            $data = Storage::get($file);
            $init = (trim($data)+1);
            Storage::put($file, $init);
            
        }else{
            Storage::put($file, $init);
        }

        $url = "https://pelisplushd.net/peliculas?page=".$init;
        
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $crawler->filter('a.Posters-link')->each(function($node){
            $dataArray = $this->getIndividualMovieInfo($node->attr('href'));
            array_push($this->movieData, $dataArray);
        });
        
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

    public function saveMovies(){

        $errors = 0;

        $titles = [];
        $descriptions = [];
        $years = [];
        $posters = [];
        $categories = [];
        $api_codes = [];

        for($z=0;$z < count($this->movieData); $z++){

            array_push($titles, $this->movieData[$z]['title']);
            array_push($descriptions, $this->movieData[$z]['description']);
            array_push($years, $this->movieData[$z]['year']);
            array_push($posters, $this->movieData[$z]['poster']);
            array_push($categories, $this->movieData[$z]['categories']);
            array_push($api_codes, $this->movieData[$z]['api_code']);
        }

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
            print 'Error Importing Movies!!';
        }else{
            DB::commit();
            print count($titles).' Movies Imported Successfully!!';
        }

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
}
