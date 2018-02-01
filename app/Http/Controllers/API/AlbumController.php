<?php


namespace App\Http\Controllers\API;

use File;
use App\Album;
use App\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;


class AlbumController extends Controller
{


    public $successStatus = 200;


    public function index()
    {
        $album = Album::where('user_id', Auth::user()->id)->with('photos')->get();

        return response()->json(['success' => $album], $this->successStatus);
    }


    public function show($id)
    {
        $album = Album::where('id', $id)->where('user_id', Auth::user()->id)->with('photos')->get();

        if(count($album) > 0){
            return response()->json(['success' => $album], $this->successStatus);
        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $album = new Album;
        $album->user_id = Auth::user()->id;
        $album->name = $request->name;
        $album->save();

        return response()->json(['success' => 'ok'], $this->successStatus);
    }


    public function update($id, Request $request)
    {

        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $update = Album::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->update([
                'name' => $request->name
            ]);

        if($update){
            return response()->json(['success' => 'ok'], $this->successStatus);
        } else {
            return response()->json(['success' => 'ko'], $this->successStatus);
        }

    }


    public function destroy($id)
    {

        $album = Album::where('id', $id);
        $album->where('user_id', Auth::user()->id);

        if(count($album->get()) > 0){

            $album_photos = Photo::where('album_id', $id)->get();
            foreach ($album_photos as $album_photo) {

                if(File::delete(public_path('images/' . $album_photo->photo))){}

            }
            Photo::where('album_id', $id)->delete();

            $album->delete();
            return response()->json(['success' => 'ok'], $this->successStatus);
        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }
}