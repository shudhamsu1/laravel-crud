<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public function storeAvatar(Request $request){
//        $request->file('avatars')->store('public/avatars');
        $request->validate([
            //The request comes from the form
            //here we are doing basic validation on the server-side.
            'avatar' => 'required|image|max:6000'
        ]);
        //if the request is validated
//        $request->file('avatar')->store('public/avatars');
            $user = auth()->user();
            $filename= $user->id.'-'.uniqid() . '.jpg';

        //make: returns the raaw data saved
        //below here we have used composer require intervention/image package to make the file small which can be fit in the avatar
        //and encoded the format
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        //The imgData didnt create any file in our folers/drive, its going to give us the raw data that needs to be saved somewhere
        Storage::put('public/avatars/'.$filename,$imgData);
        //From the line above we are storing the image file in a specific path that we can see above

        //the oldavatar is the picture which is there if there is one. It is saved here so that we can delete this once new
        //picture has been updated
        $oldAvatar = $user->avatar;

        //the line is pointing that $user->avatar value is filename and then saving it in the database which is done by eloquent
        $user->avatar = $filename;
        $user->save();

        //if oldavatar doesnt equal to fallback avatar then they had oldAvatar i.e. picture so we are going to delete it here
        if($oldAvatar != "/fallback-avatar.jpg" ){
            Storage::delete(str_replace('/storage/', "public/", $oldAvatar));
        }

        return back()->with('success', 'Congrats on the new avatar');

    }


    public function showAvatarForm(){
        return view('avatar-form');
    }
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
            //here we passed the avatar represeting the photo from the attribute and
        return view('profile-posts', ['avatar'=> $user->avatar,'username' =>$user->username, 'posts' => $user->posts()->latest()->get(), 'postCount'=>$user->posts()->count()]);
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
