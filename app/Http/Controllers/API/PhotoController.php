<?php


namespace App\Http\Controllers\API;

use Image;
use App\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use File;
use Validator;


class PhotoController extends Controller
{


    public $successStatus = 200;


    public function index($album=null)
    {
        if(empty($album)){
//            $photo = Photo::whereHas('albums', function($q){
//                    $q->where('user_id', '=', Auth::user()->id);
//                })
            $photo = Photo::where('user_id', Auth::user()->id)
                ->with('albums')
                ->get();
        } else {
            $photo = Photo::where('user_id', Auth::user()->id)
            ->where('album_id', $album)
            ->with('albums')
            ->get();
        }

        return response()->json(['success' => $photo, 'path_image' => url('/images')], $this->successStatus);
    }


    public function show($id)
    {
        $photo = Photo::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->with('albums')
            ->get();

        if(count($photo) > 0){
            return response()->json(['success' => $photo, 'path_image' => url('/images')], $this->successStatus);
        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_id' => 'required|numeric',
            'photo' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $my_photo = $this->savePhoto($request);

        if($my_photo){

            $photo = new Photo;
            $photo->album_id = $request->album_id;
            $photo->user_id = Auth::user()->id;
            $photo->photo = $my_photo;
            $photo->save();

            return response()->json(['success' => 'ok'], $this->successStatus);

        }

        return response()->json(['success' => 'ko'], $this->successStatus);
    }


    public function update(Request $request)
    {

        $rules = array(
            'id' => 'required|numeric',
            'album_id' => 'required|numeric',
            'photo' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $my_photo = $this->savePhoto($request);

        if($my_photo){

            $update = Photo::where('id', $request->id)
                ->where('user_id', Auth::user()->id)
                ->where('album_id', $request->album_id);

            if(count($update->get())){
                if(File::delete(public_path('images/' . $update->get()[0]['photo']))){}

                $update->update([
                    'photo' => $my_photo,
                ]);

                if($update){
                    return response()->json(['success' => 'ok'], $this->successStatus);
                }
            }

        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }


    public function destroy($id)
    {

        $photo = Photo::where('id', $id)->where('user_id', Auth::user()->id);

        if(count($photo->get()) > 0){

            if(File::delete(public_path('images/' . $photo->get()[0]['photo']))){}
            $photo->delete();

            return response()->json(['success' => 'ok'], $this->successStatus);
        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }


    public function savePhoto($request)
    {

        if($request->photo){

            $filename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = public_path('images/' . $filename);
            Image::make($request->photo)->save($path);

            return  $filename;
        }

        return false;
    }

}