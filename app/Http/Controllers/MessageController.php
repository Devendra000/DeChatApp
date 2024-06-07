<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(){
        return view('messages.index');
    }

    public function broadcast(Request $request){
        broadcast(new MessageEvent($request->message))->toOthers();
        // broadcast(new MessageEvent($request->message));

        return view('messages.sent',['message' => $request->message ?? '']);
    }

    public function receive(Request $request){
        $receivedMessage = $request->message ?? '';
        return view('messages.receive')->with(['message' => $receivedMessage]);
    }
}
