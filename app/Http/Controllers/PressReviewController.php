<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PressReview;
use App\User;
use App\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
		$pressreview->articles = [];
		$pressreview->save();

		$pressreviewobject = ['name' => $name, 'description' => $description, '_id' => $pressreview->_id];
		$user->push('createdReviews',$pressreviewobject);

    	return view('pages.pressreviewinsert', compact('pressreview'));
    }

    public function delete($id)
    {
    	//$user = Auth::user();
    	$pressreview = PressReview::find($id);
    	$reviews = $user->createdReviews;
    	foreach($reviews as $i => $pr)
    	{
    		if($pr['_id'] == $id)
    		{
    			$user->pull('createdReviews',$reviews[$i]);
	    		$pressreview->delete();
	    		return Redirect::to('profil')->with(['message' => 'Revue de presse supprimée.', 'status' => 'success']);
    		}
    	}
        return Redirect::to('profil')->with(['message' => "La revue de presse n'a pas pu être supprimée.", 'status' => 'fail']);
    }

    public function update($id)
    {
        //$user = Auth::user();
        $pressreview = PressReview::find($id);
        $data = $_GET['data'];
        $newarticles = [];
        $ids = explode(",", $data);
        foreach($ids as $articleid)
        {
            foreach($pressreview->articles as $article)
            {
                if($article['id'] == $articleid){
                    array_push($newarticles, $article);
                }
            }
        }
        $pressreview->articles = $newarticles;
        if($pressreview->save())
        {
            return Redirect::to('profil')->with(['message' => 'Revue de presse a été mise à jour.', 'status' => 'success']);
        }
        else{
            return Redirect::to('profil')->with(['message' => "La revue de presse n'a pas pu être mise à jour.", 'status' => 'fail']);
        }
    }

    public function addArticle(){
            
            $idArticle = $_GET['idArticle'];
            $idPage = $_GET['idPage'];
            $date = $_GET['date'];
            $newsPaper = $_GET['newspaper'];
            $title = $_GET['title'];
            $description = $_GET['description'];

            $idRev = $_GET['idReview'];
            $nameRev = $_GET['nameReview'];

            $articleToAdd = [
                 "id" => $idArticle,                         
                 "IdPage" => $idPage,                      
                 "Title" => $title,  
                 "TitleNewsPaper" => $newsPaper,                      
                 "date" => $date,                                      
                 "description" => $description
            ];

            $pressreview = PressReview::find($idRev);
            $pressreview->push('articles',$articleToAdd);

            $review = [
                "_id" => $idRev,
                "Name" => $nameRev
            ];

            $article = Article::find($idArticle);
            $article->push('Reviews', $review);

    }

    public function newReviewWithArticle(){

        $name = $_GET['nameReview'];
        $description = $_GET['descriptionRev'];
        $user = Auth::user();
        $user_id = $user->_id;
        $user_name = $user->name;

        $pressreview = new PressReview;
        $pressreview->name = $name;
        $pressreview->description = $description;
        $pressreview->owner_id = $user_id;
        $pressreview->owner_name = $user_name;
        $pressreview->articles = [];
        $pressreview->save();

        $pressreviewobject = ['name' => $name, 'description' =>
        $description, '_id' => $pressreview->_id];
        $user->push('createdReviews',$pressreviewobject);

        $_GET['idReview'] = $pressreview->_id;
        $this->addArticle();
    }

    public function addToContrib(){

        $idRev = $_GET['idReview'];
        $nameRev = $_GET['nameReview'];
        $descriptionRev = $_GET['descriptionRev'];

        $user = Auth::user();

        $contrib = $user->contribReviews;

        $pressreviewobject = ['name' => $nameRev, 'description' => $descriptionRev, '_id' => $idRev];
        $user->push('contribReviews',$pressreviewobject);

    }



}

?>