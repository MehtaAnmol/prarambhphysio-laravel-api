<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'DESC')->get();
        foreach($posts as $post) {
            $user = User::where('id', $post['author'])->first();
            $postData[] = [
                'id' => $post['id'],
                'title' => $post['title'],
                'slug' => $post['slug'],
                'description' => $post['description'],
                'author' => $user['name'],
            ];
        }
        return response($postData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'description' => 'required',
            'blog-image' => 'required',
        ]);

        $image = $request->file('blog-image');
        if($request->hasFile('blog-image')) {
            $newName = rand() . '.' .$image->getClientOriginalExtension();
            $image->move(public_path('/uploads'), $newName);
            $path = '/uploads/' . $newName;
            $newRequest = [
                'title' => $request['title'],
                'slug' => $request['slug'],
                'author' => $request['author'],
                'description' => $request['description'],
                'blog-image' => $path,
            ];
        }

        return Post::create($newRequest);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        $user = User::where('id', $post['author'])->first();
        return response([
            'id' => $post['id'],
            'title' => $post['title'],
            'slug' => $post['slug'],
            'description' => $post['description'],
            'author' => $user['name'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->update($request->all());
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $filepath = public_path() . $post['blog-image'];
        if(file_exists($filepath)) {
            File::delete($filepath);
        }
        return Post::destroy($id);
    }
}
