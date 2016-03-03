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
        $texte = $_GET['text'];
        $type = $_GET['type'];

        if( $type == 'articles'){

            $params = [
                'query' => [
                    'bool' => [
                        'should' => [
                            'match' => [
                                'Title' => $texte
                            ],
                            'match' => [
                                'Words.Word' => $texte
                            ]
                        ]
                    ]
                ]
            ];

        }elseif( $type == 'titles'){

            $params = [
                'query' => [
                    'match' => [
                        'Title' => $texte
                    ]
                ]
            ];
        }

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
        }
        //$articles = Article::where('Title', 'regexp', "/$texte/")->get();
        //dd($articles);
        return view('pages.newarticles', compact('articles'));
    }
}
