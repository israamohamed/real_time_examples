<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NewMessage;
use App\Models\User;
use App\Events\GreetingSent;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showChat()
    {
        return view('chat.show');
    }

    public function sendMessage(Request $request)
    {
        $rules = [
            'message' => 'required',
        ];

        $request->validate($rules);

        broadcast(new NewMessage($request->user() , $request->message));

        return response()->json("Message Sent");
    }

    public function greetRecieved(Request $request , User $user)
    {
        $sender = $request->user();
        $receiver = $user;
        //notify receiver 
        broadcast(new GreetingSent($receiver ,  "{$request->user()->name} greeted you" ));
        //notify sender 
        broadcast(new GreetingSent($sender   ,  "You greeted {$receiver->name}" ));

        return "Greeting {$receiver->name} from {$sender->name}" ;
    }
}
