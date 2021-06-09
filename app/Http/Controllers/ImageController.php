<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = Image::all();
        if(count($images)) {
            foreach($images as $image) {
                $user = User::where('id', $image['author'])->first();
                $result[] = [
                    'id' => $image['id'],
                    'name' => $image['name'],
                    'file' => $image['data'] . ',' . $image['base_string'],
                    'author' => $user['name'],
                    'created_at' => $image['created_at']
                ];
            }
            return response($result);
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
            'name' => 'required|string',
            'data' => 'required|string',
            'base_string' => 'required|string',
            'author' => 'required|string'
        ]);
        $images = $request->json()->all();
        foreach ($images as $image) {
            Image::create($image);
        }
        return response(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $image = Image::where('id', $id)->first();
        return response([
            'id' => $image['id'],
            'name' => $image['name'],
            'file' => $image['data'] . ',' . $image['base_string'],
            'author' => $image['author'],
            'created_at' => $image['created_at']
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
        $image = Image::find($id);
        $image->update($request);
        return $image;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isDeleted = Image::destroy($id);
        if($isDeleted == 1) {
            return response([
                'message' => 'Image deleted successfully.'
            ]);
        }
        return response([
            'message' => 'Error occurred while deleting image.'
        ]);
    }
}
