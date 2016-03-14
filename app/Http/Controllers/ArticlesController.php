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
                'highlight' => [
                    'fields' => [
                        'Title' => new \stdClass
                    ]
                ],
                'from' => $from,
                'size' => 11
            ];
        }


        $dateMin = "";
        $dateMax = "";
        //Filters
        if( isset($_GET['dateMin']) && $_GET['dateMin'] != "" && preg_match('/^[1-2][0-9]{3}/', $_GET['dateMin'])){
            if( $type == 'titles'){
                $params['query']['bool']['must']['1']['range']['Date']['gte'] = $_GET['dateMin'];
            }else{
                $params['query']['bool']['must']['range']['Date']['gte'] = $_GET['dateMin'];
            }
            $dateMin = $_GET['dateMin'];
        }

        if( isset($_GET['dateMax']) && $_GET['dateMax'] != "" && preg_match('/^[1-2][0-9]{3}/', $_GET['dateMax'])){
            if( $type == 'titles'){
                $params['query']['bool']['must']['1']['range']['Date']['lte'] = $_GET['dateMax'];
            }else{
                $params['query']['bool']['must']['range']['Date']['lte'] = $_GET['dateMax'];
            }    
            $dateMax = $_GET['dateMax'];
        }

        //Sorting
        $sort = isset($_GET['sort'])?$_GET['sort']:0;
        if( $sort ){
            /*if( $_GET['sort'] == 'title'){                      //Nécessite uen configuration spéciale sur elasticsearch
                $params['sort'] =  'Title';
            }

            if( $_GET['sort'] == 'newspaper'){
                $params['sort'] =  'TitleNewsPaper';
            }*/ 

            if( $sort == 'dateAsc'){
                $params['sort'] =  array( 'Date' => array( 'order' => 'asc'));
            }

            if( $sort == 'dateDsc'){
                $params['sort'] =  array( 'Date' => array( 'order' => 'desc'));
            }
        }


        $articles = Article::search($params);
        foreach ($articles as $oneArticle) {
            $i = 0;
            $sample = '';
            foreach ($oneArticle['Words'] as $line) {
                $sample .= $line['Word'].' ';
                $i++;
                //if( $i >= 10) break;
            }
            $oneArticle['Words'] = substr($sample, 0, 600).'...';
        }

        $builturl="recherche?text=$text&type=$type&dateMin=$dateMin&dateMax=$dateMax&sort=$sort&page=";

        return view('pages.recherche', compact('articles', 'text', 'dateMin', 'dateMax', 'builturl', 'type', 'page'));
    }
}
