<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
//    public function loadUserView($user){
//        return view('users',['name'=>$user]);
//    }
//here in the parameter the User is the instance of the model user
    public function profile(User $user){

        //the posts() function is from Usermodel where the relationship is user can post many posts
//        $thePosts = $pizza->posts()->get();
//        echo "<pre>";
//        print_r($thePosts->toArray());
//        echo "</pre>";

        return view('profile-posts', ['username' =>$user->username, 'posts' => $user->posts()->latest()->get(), 'postCount'=>$user->posts()->count()]);
    }




    public function logout(){
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out.');

    }

    public function showCorrectHomepage(){
        if(auth()->check()){
            return view('homepage-feed');

        }else {
            return view('homepage');
        }
    }

    public function login(Request $request){
        $incomingField = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        //here we are creating the associative array of the incomingfield above
        if(auth()->attempt(['username'=>$incomingField['loginusername'], 'password'=>$incomingField['loginpassword']])){
            $request->session()->regenerate();
//            return "Congrats";
            return redirect('/')->with('success', 'You have successfully loggged in.');
        }else{
            return redirect('/')->with('failure', 'Invalid login');
        }
    }

    public function register(Request $request){
        $incomingField = $request->validate([
            //Rule:unique('name of the table', and 'name of column)
            'username' => ['required','min:4', 'max:30', Rule::unique('users','username')],
            'email' => ['required', 'email', Rule::unique('users','email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);
        //bcrypt is hasing the password
        $incomingField['password'] = bcrypt($incomingField['password']);

//        Here we are sending the incomingfield from above to the User model and sending it to the databas
        //in the line below and creating a new user data in the database
        $user = User::create($incomingField);
        auth()->login($user);
        //this wil send the cookie session value so that the user browser will be connect automatically in the line above

        return redirect('/')->with('success', 'Thank you for creating an accout');
    }

}
