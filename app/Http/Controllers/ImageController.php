<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
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
        foreach($images as $image) {
            $result[] = [
                'id' => $image['id'],
                'name' => $image['name'],
                'file' => base64_encode(file_get_contents(public_path().$image['filepath'])),
                'filepath' => $image['filepath']
            ];
        }
        return response($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Image::create($this->uploadFile($request));
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
            'file' => base64_encode(file_get_contents(public_path().$image['filepath']))
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
        if(file_exists(public_path() . $image['filepath'])) {
            File::delete(public_path() . $image['filepath']);
            $newFileRequest = $this->uploadFile($request);
            if($newFileRequest) {
                $image->update($newFileRequest);
            }
            return $image;
        } else {
            return response([
                'message' => 'File doesn\'t exist on the specified path'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $file = Image::find($id);
        $filepath = public_path() . $file['filepath'];
        if(file_exists($filepath)) {
            File::delete($filepath);
            $isDeleted = Image::destroy($id);
            if($isDeleted == 1) {
                return response([
                    'message' => 'Image deleted successfully.'
                ]);
            }
        }
        return response([
            'message' => 'Error occurred while deleting image.'
        ]);
    }

    public function uploadFile(Request $request) {
        $image = $request->file('image');
        if($request->hasFile('image')) {
            $newName = rand() . '.' .$image->getClientOriginalExtension();
            $image->move(public_path('/uploads'), $newName);
            $path = '/uploads/' . $newName;
            $newRequest = [
                'name' => $newName,
                'filepath' => $path
            ];
    
            return $newRequest;
        }
        return null;
    }
}
