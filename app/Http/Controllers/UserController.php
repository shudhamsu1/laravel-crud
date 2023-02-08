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
    public function register(Request $request){
        $incomingField = $request->validate([
            //Rule:unique('name of the table', and 'name of column)
            'username' => ['required','min:4', 'max:30', Rule::unique('users','username')],
            'email' => ['required', 'email', Rule::unique('users','email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);
        //bcrypt is hasing the password
        $incomingField['password'] = bcrypt($incomingField['password']);

//        Here we are sending the incomingfield from above to the User model and sending it to the database
        User::create($incomingField);
        return "hello from register";
    }

}
