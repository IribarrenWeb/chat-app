<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Events\ReadMessages;
use Illuminate\Http\Request;
use App\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get($id){
        
        $userId = Auth()->id();

        Message::where('from',$id)->where('to',$userId)->update(['read' => true]);

        $messages = Message::where(function($q) use ($id,$userId){
            $q->where('from',$id);
            $q->where('to',$userId);
        })->orWhere(function($q) use ($id,$userId){
            $q->where('from',$userId);
            $q->where('to',$id);
        })->get();

        if(count($messages) > 1){
            $lastMessage = $messages->last();
            if($lastMessage->from == $id){
                broadcast(new ReadMessages($id));
            }
        }

        // $messages = $messages->map(function ($message,$key) {
        //     $message->fecha = strtotime($message->created_at);
        //     return $message;
        // });
        
        $messages = $messages->load('fromContact');
        
        return response()->json($messages,200);
    }

    public function save(Request $request){
        // dd($request->date);
        $message = Message::create([
            'from' => Auth()->id(),
            'to'   => $request->contactId,
            'message' => $request->message,
            'created_at' => $request->date
        ]);

        $message = $message->fresh()->load('fromContact');

        broadcast(new NewMessage($message));
        return response()->json($message,200);
    }

    public function read(Request $request){
        $userId = Auth()->id();
        // dd($request->contactId);
        Message::where('from',$request->contactId)->where('to',$userId)->update(['read' => true]);
        
        broadcast(new ReadMessages($request->contactId));
    }
}
