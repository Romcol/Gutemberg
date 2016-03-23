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
    	return view('pages.viewer', compact('pages'));
    }
}
