<?php


namespace App\Http\Controllers\API;


use App\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Validator;


class NoteController extends Controller
{


    public $successStatus = 200;
    

    public function index()
    {
        $note = Note::where('user_id', Auth::user()->id)->get();

        return response()->json(['success' => $note], $this->successStatus);
    }
    

    public function show($id)
    {
        $note = Note::where('id', $id)->where('user_id', Auth::user()->id)->get();

        if(count($note) > 0){
            return response()->json(['success' => $note], $this->successStatus);
        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $note = new Note;
        $note->user_id = Auth::user()->id;
        $note->note = $request->note;
        $note->save();

        return response()->json(['success' => 'ok'], $this->successStatus);
    }


    public function update($id, Request $request)
    {

        $rules = array(
            'note' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $update = Note::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->update([
            'note' => $request->note
        ]);

        if($update){
            return response()->json(['success' => 'ok'], $this->successStatus);
        } else {
            return response()->json(['success' => 'ko'], $this->successStatus);
        }

    }


    public function destroy($id)
    {

        $note = Note::where('id', $id);
        $note->where('user_id', Auth::user()->id);

        if(count($note->get()) > 0){

            $note->delete();
            return response()->json(['success' => 'ok'], $this->successStatus);
        }

        return response()->json(['success' => 'ko'], $this->successStatus);

    }
}