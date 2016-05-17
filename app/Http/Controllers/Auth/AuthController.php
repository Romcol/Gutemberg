<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = array(
            'required'=> 'Le champ doit être rempli.',
            'max'    => 'Le nom et l\'adresse ne doivent pas dépasser :max caractères.',
            'confirmed' => 'Confirmer le mot de passe.',
            'min'      => 'Le mot de passe doit contenir au moins :min caractères',
        );

        $validator =  Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:6',
            ],
            $messages 
        );

        $validator->after(function($validator){
            $validData = $validator->getData();
            $param = [
                'query' => [
                    'term' => [
                        'name' => $validData['name']
                    ]
                ]
            ];

           $result = User::search($param);
            if( $result->total() != 0){
                $validator->errors()->add('name', 'Nom déjà existant dans la base');
            }

            $param2 = [
                'query' => [
                    'term' => [
                        'email' => $validData['email']
                    ]
                ]
            ];

            $result = User::search($param2);
            if( $result->total() != 0){
                $validator->errors()->add('email', 'Mail déjà existant dans la base');
            }

        });

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->createdReviews = array();
        $user->contribReviews = array();
        $user->favoriteArticles = array();
        $user->save();
        return $user;
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
                ? 'Informations de connexion incorrectes.' : '';
    }
}
