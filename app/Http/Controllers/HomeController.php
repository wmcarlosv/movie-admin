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
use App\Season;
use App\Chapter;

class HomeController extends Controller
{
    private $movieData = Array();
    private $categories = Array();
    private $seasons = Array();
    private $chapters = Array();
    private $crawler;
    private $apiCode;
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
        $movies = Movie::where('status','=','A')->get();
        $series = Serie::all();
        $q = (isset($_GET['q']) and !empty($_GET['q'])) ? $_GET['q']: '';
        if(isset($q) and !empty($q)){
            $movies_availables = Movie::where('status','=','A')->where(function($query) use ($q){
                $query->orWhere('title','like','%'.$q.'%')
                ->orWhere('description','like','%'.$q.'%')
                ->orWhere('year','like','%'.$q.'%');
            })->orderBy('created_at','DESC')->paginate(5);
        }else{
            $movies_availables = Movie::where('status','=','A')->orderBy('created_at','DESC')->paginate(5);
        }
        

        return view('admin.dashboard',['ccat'=>$categories->count(), 'cmov' => $movies->count(), 'cser' => $series->count(), 'movies_availables' => $movies_availables, 'q' => $q]);
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
        $urlApiCode = "";
        $crawler->filter('script')->each(function($node){
            $find = stripos($node->text(), 'fembed.php?url=');
            if($find !== false){
               $this->apiCode = substr($node->text(), ($find+15), 15); 
            }
            
        });

        $movieData['title'] = $title;
        $movieData['description'] = $description;
        $movieData['poster'] = $poster;
        $movieData['year'] = $year;
        $movieData['categories'] = $categories;
        $movieData['api_code'] = $this->apiCode; //$this->getApiCode($this->apiCode);

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

    public function setImportSeries(Request $request){
        $request->validate([
            'url' => 'required'
        ]);

        $title = "Import Series";
        $url = $request->input('url');

        $data = (object) $this->getIndividualSerie($url);
        
        return view('admin.imports.series')->with(compact('title','data','url'));
    }

    public function getIndividualSerie($url){

        $categories = "";
        $serieData = Array();

        $client = new Client(); 
        $this->crawler = $client->request('GET', $url);

        $title = $this->crawler->filter('div.card h1.m-b-5')->text();
        $description = $this->crawler->filter('div.card div.text-large')->text();
        $poster = $this->crawler->filter('div.card img')->attr('src');
        $year = $this->crawler->filter('div.card span.text-semibold')->text();

        $this->crawler->filter('div.card a[href^="/generos/"]')->each(function($node){
            array_push($this->categories, $node->text());
        });

        $this->crawler->filter('div.card ul li.presentation a')->each(function($node){

           $this->crawler->filter($node->attr('href').' a')->each(function($node){
                array_push($this->chapters, ['title' => $node->text(), 'data' => $this->getDataSerie($node->attr('href'))]);
            });

            array_push($this->seasons, ['title' => $node->text(), 'chapters' => $this->chapters]);

            $this->chapters = [];
        });


        $serieData['title'] = $title;
        $serieData['description'] = $description;
        $serieData['poster'] = $poster;
        $serieData['year'] = $year;
        $serieData['categories'] = $this->categories;
        $serieData['seasons'] = $this->seasons;

        return $serieData;
    }

    public function getDataSerie($url){

        $serieData = Array();

        $client = new Client(); 
        $crawler = $client->request('GET', $url);

        $title = $crawler->filter('div.card h1.m-b-5')->text();

        $crawler->selectLink('PlusTo');

        $api_code = $crawler->filter('html')->text();
    
        if(empty($api_code)){
            $api_code = $crawler->filter('script')->eq(3)->text();
        }

        //$urlApiCode = $crawler->filter('div.video-html > iframe')->attr('src');

        $crawler->filter('script')->each(function($node){
            $find = stripos($node->text(), 'fembed.php?url=');
            if($find !== false){
               $this->apiCode = substr($node->text(), ($find+15), 15); 
            }
            
        });


        $serieData['title'] = $title;
        $serieData['api_code'] = $this->apiCode;

        return $serieData;
    }

    public function getApiCodeSerie($text){
        $result = "Not Api Code";
        /*$equals = explode("=", $text);

        if(count($equals) >= 3){
            $puntoycoma = explode(";", $equals[3]);
            if(count($puntoycoma) > 0){
                $coma = explode("'", $puntoycoma[0]);
                if(count($coma) > 0){
                    $result = $this->getApiCode($coma[1]);
                }
            }
            
        }*/

        $pos = strpos($text, "https://pelispop.net/v/");
        if ($pos !== false) {
            $result = substr($text, $pos, 35);
            $parts = explode("/v/", $result);
            $result = $parts[1];
        }

        return $result;
    }

    public function saveSeries(Request $request){

        $errors = 0;

        DB::beginTransaction();

        $serie = Serie::where(DB::raw("UPPER(title)"),'=',strtoupper($request->input('title')))->get();

        if($serie->count() > 0){
            $serie = Serie::findorfail($serie[0]->id);
        }else{
           $serie = new Serie();
        }

        $serie->title = $request->input('title');
        $serie->description = $request->input('description');
        $serie->year = $request->input('year');

        $imgUrl = $poster = $request->input('poster');
        $imgExtension = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
        $imgName = preg_replace("/[^a-zA-Z]/", "", str_replace(" ", "_", $request->input('title')));
        $imgContents = file_get_contents($imgUrl);
        Storage::put('public/series/'.$imgName.".".$imgExtension, $imgContents);

        $serie->poster = $imgName.".".$imgExtension;

        if($serie->save()){
            $serie->categories()->detach();

            foreach($request->input('categories') as $category){
                $serie->categories()->attach($this->getCategory($category));
            }

            $season_titles = $request->input('season_title');
            $season_positions = $request->input('season_position');

            foreach($season_titles as $key => $sas){

                $season = Season::where([
                    [DB::raw('UPPER(title)'),'=',$sas],
                    ['serie_id','=',$serie->id]
                ])->get();

                if($season->count() > 0){
                    $season = Season::findorfail($season[0]->id);
                }else{
                    $season = new Season(); 
                }
                    
                $season->title = $sas;
                $season->position = $season_positions[$key];
                $season->serie_id = $serie->id;

                if($season->save()){
                    $chapter_titles = $request->input('chapter_title_'.$key);
                    $chapter_position = $request->input('chapter_position_'.$key);
                    $chapter_api_code = $request->input('chapter_api_code_'.$key);

                    foreach($chapter_titles as $k => $cht){
                        $chapter = Chapter::where(DB::raw('UPPER(title)'),strtoupper($cht))->where('season_id',$season->id)->get();
                        
                        if($chapter->count() > 0){
                            $chapter = Chapter::findorfail($chapter[0]->id);
                        }else{
                            $chapter = new Chapter();  
                        }
                            
                        $chapter->title = $cht;
                        $chapter->position = $chapter_position[$k];
                        $chapter->api_code = $chapter_api_code[$k];
                        $chapter->season_id = $season->id;

                        if(!$chapter->save()){
                            $errors++;
                        }
                    }
                }else{
                    $errors++;
                }
            }
        }else{
            $errors++;
        }

        if($errors > 0){
            DB::rollback();
            Session::flash('error','Error Importing Serie!!');
        }else{
            Session::flash('success','Serie Imported Successfully!!!');
            DB::commit();
        }

        return redirect()->route('import_series');
    }

    public function getDataVideo(Request $request){
        $api_code = $request->input('api_code');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://pelispop.net/api/source/".$api_code);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_server_output = curl_exec ($ch);
        curl_close ($ch);

        return response()->json($remote_server_output);
        
    }

    public function getVideo(Request $request){

        $video_url = $request->input('video_url');

        ini_set('max_execution_time', 0);
        $useragent = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36";
        $v = $video_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 222222);
        curl_setopt($ch, CURLOPT_URL, $v);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        $size2 = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        header("Content-Type: video/mp4");
        $filesize = $size2;
        $offset = 0;
        $length = $filesize;
        if (isset($_SERVER['HTTP_RANGE'])) {
            $partialContent = "true";
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $offset = intval($matches[1]);
            $length = $size2 - $offset - 1;
        } else {
            $partialContent = "false";
        }
        if ($partialContent == "true") {
            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges: bytes');
            header('Content-Range: bytes '.$offset.
                '-'.($offset + $length).
                '/'.$filesize);
        } else {
            header('Accept-Ranges: bytes');
        }
        header("Content-length: ".$size2);


        $ch = curl_init();
        if (isset($_SERVER['HTTP_RANGE'])) {
            // if the HTTP_RANGE header is set we're dealing with partial content
            $partialContent = true;
            // find the requested range
            // this might be too simplistic, apparently the client can request
            // multiple ranges, which can become pretty complex, so ignore it for now
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $offset = intval($matches[1]);
            $length = $filesize - $offset - 1;
            $headers = array(
                'Range: bytes='.$offset.
                '-'.($offset + $length).
                ''
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 222222);
        curl_setopt($ch, CURLOPT_URL, $v);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_exec($ch);
    }
}
