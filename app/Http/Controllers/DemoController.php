<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemoController extends Controller
{


    public function aboutPage(){
        return view('single-post');
    }

    public function homepage(){

        $ourName = 'Shudhamsu';
        $animals=['Meowalot', 'barks', 'purrs'];
        return view('homepage',['allAnimals'=>$animals, 'name'=>$ourName]);
    }
}
