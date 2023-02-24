<?php

namespace App\Http\Controllers;

use App\Events\OurExampleEvent;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
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
    private function getSharedData($user){
        $currentlyFollowing = 0;

        if(auth()->check()){
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id],['followeduser','=', $user->id]])->count();
            //if the currently following equals to 1 then this will be rendered in the profile showing remove following, since they are already followed
        }
        //the posts() function is from Usermodel where the relationship is user can post many posts
//        $thePosts = $pizza->posts()->get();
//        echo "<pre>";
//        print_r($thePosts->toArray());
//        echo "</pre>";
        //here we passed the avatar represeting the photo from the attribute and
        View::share('sharedData', ['currentlyFollowing'=> $currentlyFollowing, 'avatar'=> $user->avatar,
            'username' =>$user->username, 'postCount'=>$user->posts()->count(),
            'followerCount'=>$user->followers()->count(), 'followingCount'=> $user->followingTheseUsers()->count()]);
        // can share a variable and available in the share templates

    }

    public function profile(User $user){
        $this->getSharedData($user);
        return view('profile-posts', [ 'posts' => $user->posts()->latest()->get()]);
    }

    public function profileRaw(User $user){
        //here the render will just send text
        return response()->json(['theHTML'=> view('profile-posts-only',['posts' => $user->posts()->latest()->get()])->render(),
            'docTitle' => $user->username. "'s Profile"]);
    }

    public function profileFollowers(User $user){
        $this->getSharedData($user);
//         $user->followers()->latest()->get();

        return view('profile-followers', [ 'followers' => $user->followers()->latest()->get()]);
    }
    public function profileFollowersRaw(User $user){
        return response()->json(['theHTML'=> view('profile-followers-only',['followers' => $user->followers()->get()])->render(),
            'docTitle' => $user->username. "'s Followers"]);
            }

    public function profileFollowing(User $user){
        $this->getSharedData($user);

        return view('profile-following', ['following' => $user->followingTheseUsers()->latest()->get()]);
    }

    public function profileFollowingRaw(User $user){
        return response()->json(['theHTML'=> view('profile-following-only',['following' => $user->followingTheseUsers()->get()])->render(),
            'docTitle' => 'who '. $user->username. " Follows"]);
    }



    public function logout(){

        event(new OurExampleEvent(['username'=> auth()->User()->username, 'action'=>'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out.');

    }

    public function showCorrectHomepage(){
        if(auth()->check()){
            return view('homepage-feed',['posts' => auth()->user()->feedposts()->latest()->paginate(4)]);

        }else {
            return view('homepage');
        }
    }

    public function loginApi(Request $request){
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        //this will only run true if it is a valid username and pw
        if(auth()->attempt($incomingFields)){
            //
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        return 'sorry';
    }

    public function login(Request $request){
        $incomingField = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        //here we are creating the associative array of the incomingfield above
        if(auth()->attempt(['username'=>$incomingField['loginusername'], 'password'=>$incomingField['loginpassword']])){
            $request->session()->regenerate();
            event(new OurExampleEvent(['username'=> auth()->user()->username, 'action'=>'login']));
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
