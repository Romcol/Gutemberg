<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;
use App\Article;
use App\Autocomplete;
use App\Utility;
use Illuminate\Support\Facades\Input;

class ViewerController extends Controller
{

    public function index($page_id,$article_id = null, $search = null)
    {
        
    	$page = Page::find($page_id);

        session_start();
        if( isset($_SESSION['searchUri'])){
            $searchUri = $_SESSION['searchUri'];
        }else{
            $searchUri = null;
        }

        $article = $this->searchArticle($article_id);

        $filename = 'default.dzi';
        $typeImage = false;

        if ( file_exists(public_path().'/images/'.$page['Picture'].'.dzi')) {
           $filename = $page['Picture'].'.dzi';
        }else if(file_exists(public_path().'/images/'.$page['Picture'].'.jpg')) {
            $filename = $page['Picture'].'.jpg';
            $typeImage = true;
        }

        $keywords = '';
        $searchedKeywords = [];
        if( $search != null ){
            $keywords = $search;
            $searchedKeywords = $this->searchKeyword($page_id,$keywords);
        }

        $searchedKeywords = json_encode($searchedKeywords);

        $paramsAutocompl = [
            'query' => [
                'match' => [
                    'Name' => 'tags'
                ]
            ]
        ];
        
        $savedTags = Autocomplete::search($paramsAutocompl);
        $savedTags = json_encode($savedTags[0]['Data']);



    	return view('pages.viewer', compact('page','article', 'filename', 'keywords', 'searchedKeywords', 'searchUri', 'savedTags', 'typeImage'));
    }

    public function searchArticle($id = null){
        $article = null;
        if($id == null && isset($_GET['article'])) $id = $_GET['article'];
        if($id != null){

            $article =  Article::find($id);
            $article->Views = $article->Views + 1;
            $article->save();

            $id_page = $article['IdPage']->{'$id'};
            $titleNews = $article['TitleNewsPaper'];
            $date = $article['Date'];
            $title = ($article['Title'] != '(Sans Titre)')? $article['Title'] : '';

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
                                    'IdPage' => $id_page
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $result = Article::search($paramsClose);

            $article['Close'] = $result;

        }

        

        return $article;

    }

    public function searchKeyword($page_id = null,$keywords = null){
        if($page_id == null && isset($_GET['id'])) $page_id = $_GET['id'];
        if($keywords == null && isset($_GET['search'])) $keywords = $_GET['search'];

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
                            'IdPage' => $page_id
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
