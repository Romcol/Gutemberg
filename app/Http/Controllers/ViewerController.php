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
        }

    	return view('pages.viewer', compact('pages', 'article'));
    }
}
