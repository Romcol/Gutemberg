<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Article;

class ArticlesController extends Controller
{

    public function index()
    {
    	$articles = Article::all();
    	//dd($articles);
    	return view('pages.articles', compact('articles'));
    }

    public function search()
    {
        $text = $_GET['text'];
        $type = $_GET['type'];
        $page = isset($_GET['page'])?intval($_GET['page']):1;
        $from = ($page>0)?(($page-1)*10):0;

        if( $type == 'articles'){

            $params = [
                'query' => [
                    'filtered' => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    'match' => [
                                        'Title' => [
                                            'query' => $text,
                                            'operator' => 'and'
                                        ]
                                    ],
                                    'match' => [
                                        'Words.Word' => [
                                            'query' => $text,
                                            'operator' => 'and'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => []
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'Title' => new \stdClass,
                        'Words.Word' => new \stdClass
                    ]
                ],
                'from' => $from,
                'size' => 11
            ];


        }elseif( $type == 'titles'){

            $params = [
                'query' => [
                    'filtered' => [
                        'query' => [
                            'bool' => [
                                'must' => [
                                    'match' => [
                                        'Title' => [
                                            'query' => $text,
                                            'operator' => 'and'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => []
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'Title' => new \stdClass
                    ]
                ],
                'from' => $from,
                'size' => 11
            ];
        }

        $regex = '/^[1-2][0-9]{3}(-([1-9]|0[1-9]|1[0-2])(-[0-9]{1,2})?)?$/';
        $defaultMin = '1845-01-01';
        $defaultMax = '1945-12-31';

        //Filters
        if( isset($_GET['dateMin']) && $_GET['dateMin'] != '' ){

            $dateMin = str_replace(' ', '', $_GET['dateMin']);
            $tabDate = explode('-', $_GET['dateMin']);

            if( preg_match($regex, $dateMin) && (count($tabDate) < 3 || checkdate(intval($tabDate[1]), intval($tabDate[2]), intval($tabDate[0])))){

                $params['query']['filtered']['filter']['bool']['must']['range']['Date']['gte'] = $dateMin;


            }else{

                $defaultMin = 'Date erronée !';
                $dateMin = '';

            }

        }else{

            $dateMin = '';

        }

        if( isset($_GET['dateMax']) && $_GET['dateMax'] != ''){

            $dateMax = str_replace(' ', '', $_GET['dateMax']);
            $tabDate = explode('-', $_GET['dateMax']);

            if( preg_match($regex, $dateMax) && (count($tabDate) < 3 || checkdate(intval($tabDate[1]), intval($tabDate[2]), intval($tabDate[0])))){

                $params['query']['filtered']['filter']['bool']['must']['range']['Date']['lte'] = $dateMax;

            }else{

                $defaultMax = 'Date erronée !';
                $dateMax = '';

            }
        }else{

            $dateMax = '';
            
        }

        //Sorting
        $sort = isset($_GET['sort'])?$_GET['sort']:0;
        if( $sort ){
            if( $sort == 'dateAsc'){
                $params['sort'] =  array( 'Date' => array( 'order' => 'asc'));
            }

            if( $sort == 'dateDsc'){
                $params['sort'] =  array( 'Date' => array( 'order' => 'desc'));
            }
        }


        $articles = Article::search($params);
        //dd($articles);
        $configLength = 10;
        
        foreach ($articles as $article) {
            if($article->highlight('Words.Word'))
            {
                $wordTab = [];
                $i = 0;
                $j = -1;
                foreach ($article['Words'] as $line) {
                    $string = str_replace(['<em>','</em>'],'',$article->highlight('Words.Word'));
                    //if(($j < 0) && ((substr($line['Word'], 0, strlen($string))) == $string)){
                    if(($j < 0) && (strpos($line['Word'],$string) !== false)){
                        $j = $i+$configLength;
                        $wordTab[$i] = $article->highlight('Words.Word');
                    }
                    else $wordTab[$i] = $line['Word'];
                    if($j > 0 && $i > $j) break;
                    $i++;
                }
                //print_r($wordTab);
                $beg = (($j-$configLength*2) > 0)?($j-$configLength*2):0;
                $article['Words'] = '';
                for($k = $beg; $k<$j ;$k++)
                {
                    if(!isset($wordTab[$k])) break;
                    $article['Words'] .= $wordTab[$k].' ';
                }
                $article['Words'] .= '...';
            }
            else{
                    $i = 0;
                    $sample = '';
                    foreach ($article['Words'] as $line) {
                        $sample .= $line['Word'].' ';
                        if($i > $configLength*2) break;
                        $i++;
                    }
                    $article['Words'] = $sample.'...';
            }
        }

        $builturl="recherche?text=$text&type=$type&dateMin=$dateMin&dateMax=$dateMax&sort=$sort&page=";

        return view('pages.recherche', compact('articles', 'text', 'dateMin', 'dateMax', 'builturl', 'type', 'page', 'defaultMin', 'defaultMax'));
    }
}
