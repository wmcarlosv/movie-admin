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

    protected $baseUrl;

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
        $this->baseUrl = env("URL_SEARCH");
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
        
        try{

            $url = $this->baseUrl."/peliculas?page=".$init;
            $client = new Client();
            $crawler = $client->request('GET', $url);
            $crawler->filter('a.Posters-link')->each(function($node){
                $dataArray = $this->getIndividualMovieInfo($node->attr('href'));
                if($dataArray){
                    array_push($this->movieData, $dataArray);
                }
                
            });

        }catch(\Exception $e){

            $data = Storage::get($file);
            $init = (trim($data)-1);
            Storage::put($file, $init);

        }

        
    }

    public function getIndividualMovieInfo($url){

        global $categories, $embed_urls, $cont;
        $categories = "";
        $cont = 0;
        $movieData = Array();
        $embed_urls = Array();
        
        try{
            $client = new Client(); 
            $url = $this->baseUrl.$url;
            $crawler = $client->request('GET', $url);
            $title = $crawler->filter('div.card h1.m-b-5')->text();
            $description = $crawler->filter('div.card div.text-large')->text();
            $poster = $crawler->filter('div.card img')->attr('src');
            $year = $crawler->filter('div.card span.text-semibold')->text();

            $crawler->filter('div.card a[href^="/generos/"]')->each(function($node){
                global $categories;
                $categories.=$node->text().",";
            });

            $crawler->filter('ul.TbVideoNv li')->each(function($node){
                global $embed_urls, $cont;
                $embed_urls[$cont]['url'] = $node->attr("data-url");
                $embed_urls[$cont]['tab'] = $node->attr("data-name");
                $embed_urls[$cont]['server'] = $node->text();
                $cont++;
            });

            $categories = substr($categories, 0, -1);
            $urlApiCode = "";
            $crawler->filter('script')->each(function($node){
                $find = stripos($node->text(), 'fembed.php?url=');
                if($find !== false){
                   $this->apiCode = substr($node->text(), ($find+15), 15); 
                }
                
            });

            $movieData['title'] = $title;
            $movieData['description'] = $description;
            $movieData['poster'] = $this->baseUrl.$poster;
            $movieData['year'] = $year;
            $movieData['categories'] = $categories;
            $movieData['embed_urls'] = json_encode($embed_urls);

            return $movieData;
        }catch(\Exception $e){
            return null;
        }
    }

    public function saveMovies(){

        $errors = 0;

        $titles = [];
        $descriptions = [];
        $years = [];
        $posters = [];
        $categories = [];
        $embed_urls = [];

        for($z=0;$z < count($this->movieData); $z++){

            array_push($titles, $this->movieData[$z]['title']);
            array_push($descriptions, $this->movieData[$z]['description']);
            array_push($years, $this->movieData[$z]['year']);
            array_push($posters, $this->movieData[$z]['poster']);
            array_push($categories, $this->movieData[$z]['categories']);
            array_push($embed_urls, $this->movieData[$z]['embed_urls']);
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
                $movie->direct_url = $embed_urls[$i];


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
                $movie->direct_url = $embed_urls[$i];


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
        }else{
            DB::commit();
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
