<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;
use App\Article;

class ViewerController extends Controller
{

    public function index()
    {
        $id = $_GET['id'];
        $params = [
            'query' => [
                'match' => [
                    '_id' => $id
                ]
            ]
        ];
    	$pages = Page::search($params);
        //dd($pages);

        $article = $this->searchArticle();

        $filename = $pages[0]['Picture'].'.dzi';

        if ( !file_exists(public_path().'/images/'.$filename)) {
           $filename = 'default.dzi';
        }

        $keywords = '';
        $searchedKeywords = [];
        if( isset($_GET['search']) ){
            $keywords = $_GET['search'];
            $searchedKeywords = $this->searchKeyword();
        }

        $searchedKeywords = json_encode($searchedKeywords);

       // dd($pages);

    	return view('pages.viewer', compact('pages','article', 'filename', 'keywords', 'searchedKeywords'));
    }

    public function searchArticle(){
        $article = 'null';
        if( isset($_GET['article']) ){
            $id = $_GET['article'];
            $paramsArticle = [
                'query' => [
                    'match' => [
                        '_id' => $id
                    ]
                ]
            ];
            $article =  Article::search($paramsArticle);

            $viewArticle = Article::find($id);
            $viewArticle->Views = $viewArticle->Views + 1;
            $viewArticle->save();


            $id = $article[0]['IdPage'];
            $titleNews = $article[0]['TitleNewsPaper'];
            $date = $article[0]['Date'];
            $title = ($article[0]['Title'] != '(Sans Titre)')? $article[0]['Title'] : '';
            $paramsClose = [
                'query' => [
                    'filtered' => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    'match' => [
                                        'TitleNewsPaper' => [
                                            'query' => $titleNews,
                                            'operator' => 'and'
                                        ]
                                    ],
                                    'match' => [
                                        'Date' => [
                                            'query' => $date
                                        ]
                                    ],
                                    'match' => [
                                        'Title' => [
                                            'query' => $title
                                        ]
                                    ]
                                ],
                                'must_not' =>[
                                    'match' => [
                                        'Title' => [
                                            'query' => '(Sans titre)',
                                            'operator' => 'and'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'filter' => [
                            'not' => [
                                'term' => [
                                    'IdPage' => $id
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $result = Article::search($paramsClose);

            $article[0]['Close'] = $result;

        }

        

        return $article;

    }

    public function searchKeyword(){
        $id = $_GET['id'];
        $keywords = $_GET['search'];
        $paramsSearch = [
            'query' => [
                'bool' => [
                    'should' => [
                        'match' => [
                            'Title' => [
                                'query' => $keywords,
                                'operator' => 'and'
                            ]
                        ],
                        'match' => [
                            'Words.Word' => [
                                'query' => $keywords,
                                'operator' => 'and'
                            ]
                        ]
                    ],
                    'must' => [
                        'match' => [
                            'IdPage' => $id
                        ]
                    ]
                ]
            ],
            'size' => 20
        ];

        $result = Article::search($paramsSearch);
        $wordTab = [];
        $keywordTab = explode(' ', $keywords);
        foreach($keywordTab as $word){
            foreach ($result as $article) {
                foreach($article['Words'] as $line){
                    if( strpos(strtolower($line['Word']), strtolower($word)) !== false ){
                        array_push($wordTab, $line['Coord']);
                    }
                }

                if( strpos(strtolower($article['Title']), strtolower($word)) !== false ){
                    array_push($wordTab, $article['TitleCoord']);
                }
            }
        }

        return $wordTab;
    }
}
