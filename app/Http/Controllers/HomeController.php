<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Article;
use App\PressReview;

class HomeController extends Controller
{
    public function index()
    {
        session_start();
        $_SESSION['searchUri'] = null;

        $params = [
          'sort' => [
            'Views' => [
              'order' => 'desc'
            ]
          ],
          'size' => 5
        ];

        $articles = Article::search($params);

        $params2 = [
          'sort' => [
            'created' => [
              'order' => 'desc'
            ]
          ],
          'size' => 5
        ];

        $reviews = PressReview::search($params2);
        return view('pages.home', compact('articles', 'reviews'));
    }
}
