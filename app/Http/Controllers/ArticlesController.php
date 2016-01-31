<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Article;

class ArticlesController extends Controller
{
    //

    public function index()
    {
    	//$articles = DB::collection('article')->get();
    	$articles = Article::all();
    	return view('pages.articles', compact('articles'));
    }

    public function search()
    {
    	$texte = $_GET['texte'];
    	$articles = Article::where('titre', 'regexp', "/$texte/")->get();
    	return view('pages.articles', compact('articles'));
    }
}
