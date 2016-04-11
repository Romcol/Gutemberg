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

        if ( !file_exists(public_path().'\\images\\'.$filename)) {
           $filename = 'default.dzi';
        }
                
    	return view('pages.viewer', compact('pages','article', 'filename'));
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

        }

        

        return $article;

    }
}
