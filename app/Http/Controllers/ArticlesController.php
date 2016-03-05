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

        if( $type == 'articles'){

            $params = [
                'query' => [
                    'bool' => [
                        'should' => [
                            'match' => [
                                'Title' => $text
                            ],
                            'match' => [
                                'Words.Word' => $text
                            ]
                        ]
                    ]
                ],
                'size' => 10
            ];

        }elseif( $type == 'titles'){

            $params = [
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'Title' => $text
                            ]
                        ]
                    ]
                ],
                'size' => 10
            ];
        }


        $dateMin = "";
        $dateMax = "";
        //Filters
        if( isset($_GET['dateMin']) && $_GET['dateMin'] != ""){
            $params['query']['bool']['must']['range']['Date']['gte'] = $_GET['dateMin'];
            $dateMin = $_GET['dateMin'];
        }

        if( isset($_GET['dateMax']) && $_GET['dateMax'] != ""){
            $params['query']['bool']['must']['range']['Date']['lte'] = $_GET['dateMax'];
            $dateMax = $_GET['dateMax'];
        }

        //Sorting
        if( isset($_GET['sort']) ){
            /*if( $_GET['sort'] == 'title'){                      //Nécessite uen configuration spéciale sur elasticsearch
                $params['sort'] =  'Title';
            }

            if( $_GET['sort'] == 'newspaper'){
                $params['sort'] =  'TitleNewsPaper';
            }*/ 

            if( $_GET['sort'] == 'dateAsc'){
                $params['sort'] =  array( 'Date' => array( 'order' => 'asc'));
            }

            if( $_GET['sort'] == 'dateDsc'){
                $params['sort'] =  array( 'Date' => array( 'order' => 'desc'));
            }
        }

        $dMax = date_create_from_format('Y', '1000');
        $dMin = date_create('now');
        $articles = Article::search($params);
        foreach ($articles as $oneArticle) {
            $i = 0;
            $sample = '';
            foreach ($oneArticle['Words'] as $line) {
                $sample = $sample.$line['Word'];
                $i++;
                if( $i >= 5) break;
            }
            $oneArticle['Words'] = $sample.'...';

            if( $dMax < date_create($oneArticle['Date'])){
                $dMax = date_create($oneArticle['Date']);
            }
            if( $dMin > date_create($oneArticle['Date'])){
                $dMin = date_create($oneArticle['Date']);
            }
        }

        $dMin = $dMin->format('Y');
        $dMax = $dMax->format('Y');

        //$articles = Article::where('Title', 'regexp', "/$texte/")->get();
        //dd($articles);
        return view('pages.newarticles', compact('articles', 'text', 'dateMin', 'dateMax', 'dMin', 'dMax'));
    }
}
