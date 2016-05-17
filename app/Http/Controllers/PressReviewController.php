<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PressReview;
use App\User;
use Illuminate\Support\Facades\Auth;

class PressReviewController extends Controller
{

	public function index()
    {
    	$id = $_GET['id'];

    	$pressreview = PressReview::find($id);
    	//dd($pressreviews);

    	return view('pages.pressreview', compact('pressreview'));
    }

    public function make()
    {
    	return view('pages.pressreviewcreator');
    }

    public function insert(Request $request)
    {
    	$name = $request->input('name');
    	$description = $request->input('description');
    	$user_id = Auth::user()->_id;
    	$user_name = Auth::user()->name;

    	$pressreview = new PressReview;
		$pressreview->name = $name;
		$pressreview->description = $description;
		$pressreview->owner_id = $user_id;
		$pressreview->owner_name = $user_name;
		$pressreview->articles = "[]";
		$pressreview->save();

		$user = User::find($user_id);
		$pressreviewobject = ['name' => $name, 'description' => $description, '_id' => $pressreview->_id];
		$user->push('createdReviews',$pressreviewobject);
		
    	return view('pages.pressreviewinsert', compact('name','description'));
    }

}
?>