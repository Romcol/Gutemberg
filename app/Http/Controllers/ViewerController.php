<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;
use App\Article;
use App\Autocomplete;
use App\Utility;

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

        session_start();
        if( isset($_SESSION['searchUri'])){
            $searchUri = $_SESSION['searchUri'];
        }else{
            $searchUri = null;
        }

        $article = $this->searchArticle();

        $filename = 'default.dzi';
        $typeImage = false;
        if ( file_exists(public_path().'/images/'.$pages[0]['Picture'].'.dzi')) {
           $filename = $pages[0]['Picture'].'.dzi';
        }else if(file_exists(public_path().'/images/'.$pages[0]['Picture'].'.jpg')) {
            $filename = $pages[0]['Picture'].'.jpg';
            $typeImage = true;
        }

        $keywords = '';
        $searchedKeywords = [];
        if( isset($_GET['search']) ){
            $keywords = $_GET['search'];
            $searchedKeywords = $this->searchKeyword();
        }

        $searchedKeywords = json_encode($searchedKeywords);

       // dd($pages);
        $paramsAutocompl = [
            'query' => [
                'match' => [
                    'Name' => 'tags'
                ]
            ]
        ];
        
        $savedTags = Autocomplete::search($paramsAutocompl);
        $savedTags = json_encode($savedTags[0]['Data']);



    	return view('pages.viewer', compact('pages','article', 'filename', 'keywords', 'searchedKeywords', 'searchUri', 'savedTags', 'typeImage'));
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
        //Coordinates array for occurrence arrows
        $wordTab = [];

        //Occurrence number per article
        $occurrenceArticle = array();

        $keywordTab = explode(' ', $keywords);
        foreach($keywordTab as $word){
            foreach ($result as $article) {
                $occurrenceArticle[$article['_id']] = 0;
                foreach($article['Words'] as $line){
                    if( strpos(strtolower($line['Word']), strtolower($word)) !== false ){
                        array_push($wordTab, $line['Coord']);
                        $occurrenceArticle[$article['_id']]++;
                    }
                }

                if( strpos(strtolower($article['Title']), strtolower($word)) !== false ){
                    array_push($wordTab, $article['TitleCoord']);
                    $occurrenceArticle[$article['_id']]++;
                }
            }
        }

        $functionResult = array($wordTab, $occurrenceArticle);

        return $functionResult;
    }

    public function addTag(){

        if( isset($_GET['article']) ){
            $id = $_GET['article'];
            $tag = $_GET['tag'];

            $tagArticle = Article::find($id);
            $tabTag = $tagArticle->Tags;
            if(!in_array($tag, $tabTag)){
                array_push($tabTag, $tag);
            }
            $tagArticle->Tags = $tabTag;
            $tagArticle->save();

            $tagData = Autocomplete::where('Name', '=', 'tags')->get();
            $tabTagData = $tagData[0]->Data;
            if(!in_array($tag, $tabTagData)){
                array_push($tabTagData, $tag);
            }
            $tagData[0]->Data = $tabTagData;
            $tagData[0]->save();
        }

    }

    public function removeTag(){

        if( isset($_GET['article']) ){
            $id = $_GET['article'];
            $tag = $_GET['tag'];

            $tagArticle = Article::find($id);
            $tabTag = $tagArticle->Tags;

            $tabTag = $this->removeElement($tag, $tabTag);

            $tagArticle->Tags = $tabTag;
            $tagArticle->save();
        }

    }

    public function removeElement($element, $array){
        $found = false;
        $size = count($array);
        for ($j = 0; $j < $size; $j++) {
            if( $found){
                if($j != $size-1){
                    $array[$j]=$array[$j+1];
                }else{
                    unset($array[$j]);
                }
            }else{
                if( $element == $array[$j]){
                    $found = true;
                    if($j != $size-1){
                        $array[$j]=$array[$j+1];
                    }else{
                        unset($array[$j]);
                    }
                }
            }
        }

        return $array;
    }
}
