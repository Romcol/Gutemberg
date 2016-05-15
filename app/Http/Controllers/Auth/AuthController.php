<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

            $validData = $validator->getData();
            $param = [
                'query' => [
                    'match' => [
                        'name' => [
                            'query' => $validData['name'],
                            'operator' => 'and'
                        ]
                    ]
                ]
            ];

            if( $result->total() != 0){
                $validator->errors()->add('name', 'Nom déjà existant dans la base');
            }

            $param2 = [
                'query' => [
                    'match' => [
                        'email' => [
                            'query' => $validData['email']
                        ]
                    ]
                ]
            ];

            $result = User::search($param2);
            if( $result->total() != 0){
                $validator->errors()->add('email', 'Mail déjà existant dans la base');

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
}
