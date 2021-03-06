<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        if(count($posts) > 0) {
            foreach($posts as $post) {
                $user = User::where('id', $post['author'])->first();
                $postData[] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'slug' => $post['slug'],
                    'description' => $post['description'],
                    'author' => $user['name'],
                    'blog_image' => $post['blog-image'],
                    'created_at' => $post['created_at'],
                    'updated_at' => $post['updated_at']
                ];
            }
            return response($postData);
        } else {
            return response([]);
        }
    }

    public function showRecentPosts()
    {
        $posts = Post::orderBy('id', 'DESC')->take(10)->get();
        if(count($posts) > 0) {
            foreach($posts as $post) {
                $user = User::where('id', $post['author'])->first();
                $postData[] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'slug' => $post['slug'],
                    'image' => $post['blog-image'],
                    'description' => $post['description'],
                    'author' => $user['name'],
                    'created_at' => $post['created_at'],
                    'updated_at' => $post['updated_at']
                ];
            }
            return response($postData);
        } else {
            return response([]);
        }
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
        ]);

        try {
            $newRequest = [
                'title' => $request['title'],
                'slug' => $request['slug'],
                'author' => $request['author'],
                'description' => $request['description'],
                'blog-image' => $request['blog-image'],
            ];
            return Post::create($newRequest);
        } catch (\Exception $th) {
            throw $th;
        }
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
            'blog-image' => $post['blog-image'],
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
        $newRequest = [
            'title' => $request['title'],
            'slug' => $request['slug'],
            'author' => $request['author'],
            'description' => $request['description'],
            'blog-image' => $request['blog-image'],
        ];
        $post->update($newRequest);
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
