<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Article;
use App\Page;
use App\Autocomplete;
use App\PressReview;

class SearchController extends Controller
{

    public function index()
    {
    	$articles = Article::all();
    	//dd($articles);
    	return view('pages.articles', compact('articles'));
    }

    public function search()
    {
        session_start();

        $text = $_GET['text'];
        $type = $_GET['type'];
        $page = isset($_GET['page'])?intval($_GET['page']):1;
        $from = ($page>0)?(($page-1)*10):0;




        $searchUri = $_SERVER['REQUEST_URI'];
        
        $_SESSION['searchUri'] = $searchUri;

        //For newspaper search
        if( $type == 'newspaper'){

            return view('pages.newspaper', $this->newsPaperSearch());

        }else if( $type == 'review'){

            $_GET['size'] = 10;
            $regexp = (isset($_GET['regexp']))? $_GET['regexp'] : false;

            $result = $this->reviewSearch();

            $builturl="recherche?text=$text&type=$type&page=";

            return view('pages.review', compact('result', 'text', 'builturl', 'type', 'page', 'regexp'));

        }else{

            if($text == ''){
                $params = $this->paramNoText($from);
                $regexp = (isset($_GET['regexp']))? $_GET['regexp'] : false;
            }else{

                if( $type == 'articles'){

                    if( isset($_GET['regexp'])){
                        $params = $this->paramArticleRegexp($text, $from);
                        $regexp = $_GET['regexp'];
                    }else{
                        $params = $this->paramArticle($text, $from);
                        $regexp = false;
                    }


                }elseif( $type == 'titles'){

                    if( isset($_GET['regexp'])){
                        $params = $this->paramTitleRegexp($text, $from);
                        $regexp = $_GET['regexp'];
                    }else{
                        $params = $this->paramTitle($text, $from);
                        $regexp = false;
                    }
                }
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

            $tags = array();
            if( isset($_GET['tags']) && count($_GET['tags']) != 0){
                $tags = $_GET['tags'];
                $params['filter']['bool']['must']['terms']['Tags'] = $tags;
            }
            $tags = json_encode($tags);

            //Sorting
            $sort = isset($_GET['sort'])?$_GET['sort']:0;
            if( $sort ){
                if( $sort == 'dateAsc'){
                    $params['sort'] =  array( 'Date' => array( 'order' => 'asc'));
                }

                if( $sort == 'dateDsc'){
                    $params['sort'] =  array( 'Date' => array( 'order' => 'desc'));
                }

                if( $sort == 'viewsDsc'){
                    $params['sort'] =  array( 'Views' => array( 'order' => 'desc'));
                }
            }


            $articles = Article::search($params);
            //dd($articles);
            $configLength = 10;
            
            foreach ($articles as $review) {
                if($review->highlight('Words.Word'))
                {
                    $wordTab = [];
                    $i = 0;
                    $j = -1;
                    foreach ($review['Words'] as $line) {
                        $string = str_replace(['<em>','</em>'],'',$review->highlight('Words.Word'));
                        //if(($j < 0) && ((substr($line['Word'], 0, strlen($string))) == $string)){
                        if(($j < 0) && (strpos($line['Word'],$string) !== false)){
                            $j = $i+$configLength;
                            $wordTab[$i] = $review->highlight('Words.Word');
                        }
                        else $wordTab[$i] = $line['Word'];
                        if($j > 0 && $i > $j) break;
                        $i++;
                    }
                    //print_r($wordTab);
                    $beg = (($j-$configLength*2) > 0)?($j-$configLength*2):0;
                    $review['Words'] = '';
                    for($k = $beg; $k<$j ;$k++)
                    {
                        if(!isset($wordTab[$k])) break;
                        $review['Words'] .= $wordTab[$k].' ';
                    }
                    $review['Words'] .= '...';
                }
                else{
                        $i = 0;
                        $sample = '';
                        foreach ($review['Words'] as $line) {
                            $sample .= $line['Word'].' ';
                            if($i > $configLength*2) break;
                            $i++;
                        }
                        $review['Words'] = $sample.'...';
                }
            }

            $builturl="recherche?text=$text&type=$type&dateMin=$dateMin&dateMax=$dateMax&sort=$sort&page=";

            $paramsAutocompl = [
                'query' => [
                    'match' => [
                        'Name' => 'tags'
                    ]
                ]
            ];
            $savedTags = Autocomplete::search($paramsAutocompl);
            $savedTags = json_encode($savedTags[0]['Data']);


            return view('pages.recherche', compact('articles', 'text', 'dateMin', 'dateMax', 'builturl', 'type', 'page', 'defaultMin', 'defaultMax', 'regexp', 'savedTags', 'tags'));
        }
    }





    public function newsPaperSearch()
    {
        $text = $_GET['text'];
        $type = $_GET['type'];
        $page = isset($_GET['page'])?intval($_GET['page']):1;
        $from = ($page>0)?(($page-1)*20):0;


        $params = [
            'query' => [
                'filtered' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['match' => [
                                    'Title' => [
                                        'query' => $text,
                                        'operator' => 'and'
                                    ]
                                ]],
                                [ 'match' => [
                                    'NumberPage' => 1
                                ]]
                            ]
                        ]
                    ],

                ]
            ],
            'from' => $from,
            'size' => 21
        ];


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

            if( $sort == 'viewsDsc'){
                $params['sort'] =  array( 'Views' => array( 'order' => 'desc'));
            }            
        }


        $pages = Page::search($params);
        //dd($articles);

        $builturl="recherche?text=$text&type=$type&dateMin=$dateMin&dateMax=$dateMax&sort=$sort&page=";

        return compact('pages', 'text', 'dateMin', 'dateMax', 'builturl', 'type', 'page', 'defaultMin', 'defaultMax');
    }

    public function paramArticle($text, $from){
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

        return $params;

    }



    public function paramTitle($text, $from){

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

        return $params;

    }

    public function paramArticleRegexp($text, $from){
        $params = [
            'query' => [
                'filtered' => [
                    'query' => [
                        'bool' => [
                            'should' => [
                                'regexp' => [
                                    'Title' => $text
                                ],
                                'regexp' => [
                                    'Words.Word' => $text
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

        return $params;

    }



    public function paramTitleRegexp($text, $from){
        
        $params = [
            'query' => [
                'filtered' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                'regexp' => [
                                    'Title' => $text
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

        return $params;

    }

    public function paramNoText($from){

        $params = [
            'query' => [
                'filtered' => [
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

        return $params;
    }

    public function reviewSearch()
    {
        $text = $_GET['text'];
        $page = isset($_GET['page'])?intval($_GET['page']):1;
        $size = $_GET['size'];
        $from = ($page>0)?(($page-1)*$size):0;
        $regexp = isset($_GET['regexp']);

        if( $regexp ){

            $params = [
                'query' => [
                    'bool' => [
                        'should' => [
                            'regexp' => [
                                'name' => $text
                            ],
                            'regexp' => [
                                'description' => $text
                            ]
                        ]
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'name' => new \stdClass,
                        'description' => new \stdClass
                    ]
                ],
                'from' => $from,
                'size' => $size+1
            ];

        }else{

           $params = [
                'query' => [
                    'bool' => [
                        'should' => [
                            'match' => [
                                'name' => [
                                    'query' => $text,
                                    'operator' => 'and'
                                ]
                            ],
                            'match' => [
                                'description' => [
                                    'query' => $text,
                                    'operator' => 'and'
                                ]
                            ]
                        ]
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        'name' => new \stdClass,
                        'description' => new \stdClass
                    ]
                ],
                'from' => $from,
                'size' => $size+1
            ];
        }


        $result = PressReview::search($params);
            
        foreach ($result as $review) {
            if( strlen($review['description']) > 500){
                $review['description'] = substr($review['description'], 0, 500).'...';
            }
        }

        return $result;


    }
}
