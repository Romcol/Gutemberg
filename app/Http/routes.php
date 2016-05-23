<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::get( 'pressreview', 'PressReviewController@index');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');

    Route::get('/', function () {
	    return view('pages.home');
	});

	Route::get('articles', 'SearchController@index');

	Route::get('recherche', 'SearchController@search');

	Route::get('visionneuse/page/{page_id}/article/{article_id}/recherche/{search}', 'ViewerController@index');
	Route::get('visionneuse/page/{page_id}/article/{article_id}', 'ViewerController@index');
	Route::get('visionneuse/page/{page_id}', 'ViewerController@index');

	Route::get('changeArticle', 'ViewerController@searchArticle');
	Route::get('newSearch', 'ViewerController@searchKeyword');
	Route::get('newTag', 'ViewerController@addTag');
	Route::get('removeTag', 'ViewerController@removeTag');

	Route::get('revue/create', 'PressReviewController@make');

	Route::post('revue/create', 'PressReviewController@insert');

	Route::get('revue/{id}', 'PressReviewController@index');

	Route::get('revue/{id}/delete', 'PressReviewController@delete');

	Route::get('revue/{id}/update', 'PressReviewController@update');

	Route::get('addArticle', 'PressReviewController@addArticle');

	Route::get('addFavorite', 'PressReviewController@addFavorite');

	Route::get('removeFavorite', 'PressReviewController@removeFavorite');

	Route::get('addArticleToOther', 'PressReviewController@addArticleToOther');

	Route::get('addToContrib', 'PressReviewController@addToContrib');

	Route::get('newReview', 'PressReviewController@newReviewWithArticle');	

	Route::get('searchReview', 'SearchController@reviewSearch');

	Route::get('profil', 'UserController@index');

	Route::get('register', function () {
	    return view('auth.register');
	});
	Route::get('login', function () {
	    return view('auth.login');
	});
});
