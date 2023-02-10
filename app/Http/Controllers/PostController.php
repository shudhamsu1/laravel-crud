<?php

namespace App\Http\Controllers;

use App\Models\PostModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PostController extends Controller
{
    public function delete(PostModel $post){
        if(auth()->user()->cannot('delete', $post)){
            return 'You cannot do that';
        }
        $post->delete();
        return redirect('/profile/'. auth()->user()->username)->with('success', 'Post successfully deleted');
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
        return redirect("/post/{$newPost->id}")->with('success', 'New post successfully created');

    }
}