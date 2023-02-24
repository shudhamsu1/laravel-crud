<?php

namespace App\Http\Controllers;
use App\Jobs\SendNewPostEmail;
use Illuminate\Support\Facades\Mail;

use App\Models\PostModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PostController extends Controller
{
    public function search($term){
        $posts = PostModel::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }
    public function actuallyUpdate(PostModel $post, Request $request){
        $incomingField= $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]) ;

        $incomingField['title'] = strip_tags($incomingField['title']);
        $incomingField['body'] = strip_tags($incomingField['body']);

        //Since we already have the instance to the PostModel connected to the databse
        $post->update($incomingField);
        //redirect to the same form and we can use back() it will take back to the url we came from
        return back()->with('success', 'Your post have been updated');

    }



    //We can type hint the data from the database in the parameter in function below
    public function showEditForm(PostModel $post){
//        We need to    fetch the existing data from the databse with title and body values
        return view('edit-post',['post'=>$post]);
    }
    public function delete(PostModel $post){
//        if(auth()->user()->cannot('delete', $post)){
//            return 'You cannot do that';
//        }
        $post->delete();
        return redirect('/profile/'. auth()->user()->username)->with('success', 'Post successfully deleted');
    }

    public function deleteApi(PostModel $post){
//        if(auth()->user()->cannot('delete', $post)){
//            return 'You cannot do that';
//        }
        $post->delete();
        return "Post successfully deleted";
           }

    public function viewSinglePost(PostModel $post){
        //here the name of the parameter in the function doesnt have to be the same in route
//        if($post->user_id === auth()->user()->id)


        $post['body'] = strip_tags(Str::markdown($post->body),'<p></p>');
        return view('single-post',['post'=>$post]);
    }

    public function showCreateForm(){
        return view('create-post');
    }

    public function storeNewPost(Request $request){

        $incomingField = $request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);

        $incomingField['title'] = strip_tags($incomingField['title']);
        $incomingField['body'] = strip_tags($incomingField['body']);
        $incomingField['user_id']= auth()->id();

        $newPost = PostModel::create($incomingField);

        dispatch(new SendNewPostEmail(['sendTo' =>auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title ]));
        //since when we use post its taking time to load, we are letting jobs handle the mail part and its in jobs sendNewPost
        //here we are using Mail from Mail/NewPostEmail and sending the email to users->email and sending the info to newPostEmail
//        Mail::to(auth()->user()->email)->send(new NewPostEmail(['name' => auth()->user()->username, 'title' => $newPost->title]));

        return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');

    }
    public function storeNewPostApi(Request $request){

        $incomingField = $request->validate([
            'title'=>'required',
            'body'=>'required'
        ]);

        $incomingField['title'] = strip_tags($incomingField['title']);
        $incomingField['body'] = strip_tags($incomingField['body']);
        $incomingField['user_id']= auth()->id();

        $newPost = PostModel::create($incomingField);

        dispatch(new SendNewPostEmail(['sendTo' =>auth()->user()->email, 'name' => auth()->user()->username, 'title' => $newPost->title ]));
        //since when we use post its taking time to load, we are letting jobs handle the mail part and its in jobs sendNewPost
        //here we are using Mail from Mail/NewPostEmail and sending the email to users->email and sending the info to newPostEmail
//        Mail::to(auth()->user()->email)->send(new NewPostEmail(['name' => auth()->user()->username, 'title' => $newPost->title]));

        return $newPost->id;
    }
}
