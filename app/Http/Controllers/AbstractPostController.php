<?php

namespace App\Http\Controllers;

use App\Models\AbstractPost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AbstractPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userid = \Auth::user()->id;

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $abstract_posts = AbstractPost::join('users', 'abstract_posts.user_id', '=', 'users.id')
                ->select('abstract_posts.*', 'users.name as user_name', 'users.lastname as user_lastname', 'users.second_lastname as user_second_lastname', 'users.country as user_country')
                ->where('abstract_posts.user_id', $userid)
                ->where('abstract_posts.status', '!=', 'rechazado')
                ->orderBy('id', 'desc')
                ->get();

        return view('pages.abstract_posts.index')->with($data)->with('abstract_posts', $abstract_posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = \Auth::user()->id;

        $data = [
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $user = User::find($id);

        return view('pages.abstract_posts.create')->with($data)->with('user', $user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function show(AbstractPost $abstractPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function edit(AbstractPost $abstractPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AbstractPost $abstractPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AbstractPost  $abstractPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(AbstractPost $abstractPost)
    {
        //
    }
}
