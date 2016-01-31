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
    	$articles = Article::all();
    	//dd($articles);
    	return view('pages.articles', compact('articles'));
    }

    public function search()
    {
    	$texte = $_GET['texte'];
    	$articles = Article::where('titre', 'regexp', "/$texte/")->get();
    	//dd($articles);
    	return view('pages.articles', compact('articles'));
    }
}
