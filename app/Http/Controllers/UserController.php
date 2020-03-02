<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('login');
    }

    /**
     * Returns all de contacts.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $contacts = User::where('id','!=', Auth()->id())->get();
        $unread = Message::select(\DB::raw('`from` as sender_id, count(`from`) as unread'))
            ->where('to',Auth::id())
            ->where('read',false)
            ->groupBy('from')
            ->get();
        
        $contacts = $contacts->map(function ($item) use ($unread){
            $verifyUnread = $unread->where('sender_id',$item->id)->first();

            $item->unread = $verifyUnread ? $verifyUnread->unread : 0;

            return $item;
        });

        // dd($contacts);
        return response()->json($contacts,200);
    }
    
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validate->fails()){
            $response = [
                'status' => false,
                'errors' => $validate->errors()                
            ];

            return response()->json($response);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $response = [
                'status' => true,
                'msg' => 'Estas loggeado'
            ];
        }else{
            $response = [
                'status' => false,
                'msg' => 'Datos invalidos'
            ];
        }

        return response()->json($response);
    }

    public function upload(Request $request){
        
        $validate = Validator::make($request->all(),[
            'image' => 'required|image'
        ]);

        if($validate->fails()){
            $data = [
                'status' => false,
                'errors' => $validate->errors()
            ];
            return response()->json($data);
        }

        $path = 'storage/' . $request->file('image')->store('avatar/'.Auth::id(),'public');
        
        
        if(!empty($request->option)){
            $deletingPath = str_replace('storage/', '', Auth::user()->profile_img);
            \Storage::disk('public')->delete($deletingPath);
        }
        
        Auth::user()->update(['profile_img' => $path]);
        
        $data = [
            'status' => true,
            'path' =>  $path
        ];
        
        return response()->json($data,201);
    }

    public function update(Request $request){

        $validate = Validator::make($request->all(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'. Auth::id()],
            'phone' => 'required|digits_between:10,20|unique:users,phone_number,'. Auth::id()
        ]); 

        if($validate->fails()){
            $data = [
                'status' => false,
                'errors' => $validate->errors()
            ];

            return response()->json($data);
        }

        $user = Auth::user();

        $user->update([
        'name' => $request->name, 
        'email' => $request->email, 
        'phone_number' => $request->phone, 
        ]);

        $user = $user->fresh();

        $data = [
            'status' => true,
            'user' => $user
        ];

        return response()->json($data,201);
    }
}
