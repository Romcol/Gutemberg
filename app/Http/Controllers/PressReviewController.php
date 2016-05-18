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

	public function index($id)
    {
    	$pressreview = PressReview::find($id);

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
    	$user = Auth::user();
    	$user_id = $user->_id;
    	$user_name = $user->name;

    	$pressreview = new PressReview;
		$pressreview->name = $name;
		$pressreview->description = $description;
		$pressreview->owner_id = $user_id;
		$pressreview->owner_name = $user_name;
		$pressreview->articles = "[]";
		$pressreview->save();

		$pressreviewobject = ['name' => $name, 'description' => $description, '_id' => $pressreview->_id];
		$user->push('createdReviews',$pressreviewobject);

    	return view('pages.pressreviewinsert', compact('pressreview'));
    }

    public function delete($id)
    {
    	$user = Auth::user();
    	$pressreview = PressReview::find($id);
    	$reviews = $user->createdReviews;
    	foreach($reviews as $i => $pr)
    	{
    		if($pr['_id'] == $id)
    		{
    			$user->pull('createdReviews',$reviews[$i]);
	    		$pressreview->delete();
	    		return 'Revue de presse supprimée';
    		}
    	}

    	return "L'action n'a pas pu être effectuée.";
    }

}
?>