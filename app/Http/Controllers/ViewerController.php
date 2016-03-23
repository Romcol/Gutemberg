<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;

class ViewerController extends Controller
{

    public function index()
    {
    	$pages = Page::all();
        //dd($pages);
    	return view('pages.viewer');
    }
}
