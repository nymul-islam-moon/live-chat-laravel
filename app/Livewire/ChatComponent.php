<?php

namespace App\Livewire;

use App\Events\MessageSendEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatComponent extends Component
{

    public $user;
    public $sender_id;
    public $receiver_id;
    public $message = '';
    public $messages = [];

    public function render()
    {
        return view('livewire.chat-component');
    }

    public function mount($user_id)
    {
        $this->sender_id = Auth::user()->id;
        $this->receiver_id = $user_id;

        $messages = Message::where(function ($query) {
            $query->where('sender_id', $this->sender_id)
                ->Where('receiver_id', $this->receiver_id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->receiver_id)
                ->Where('receiver_id', $this->sender_id);
        })
            ->with('rel_to_sender:id,name', 'rel_to_receiver:id,name')
            ->get();

        foreach ($messages as $message) {
            $this->appendChatMessage($message);
        }

        // dd($this->messages);

        $this->user = User::find($user_id);
    }

    public function sendMessage()
    {
        $chatMessage = new Message;

        $chatMessage->sender_id = $this->sender_id;
        $chatMessage->receiver_id = $this->receiver_id;
        $chatMessage->message = $this->message;
        $chatMessage->save();

        $this->appendChatMessage($chatMessage);
        try {
            \Log::info('start');

            broadcast(new MessageSendEvent($chatMessage))->toOthers();
        } catch (\Exception $e) {
            \Log::info('fail to fire');
        }

        $this->message = '';
    }

    #[On('echo-private:chat-channel.{sender_id},MessageSendEvent')]
    public function listenForMessage($event)
    {
        // dd(Message::where($event['message']['id'])->first());
        $chatMessage = Message::where('id', $event['message']['id'])
            ->with('rel_to_sender:id,name', 'rel_to_receiver:id,name')
            ->first();

        $this->appendChatMessage($chatMessage);
    }

    public function appendChatMessage($chatMessage)
    {
        $this->messages[] = [
            'id' => $chatMessage->id,
            'message' => $chatMessage->message,
            'sender' => [
                'id' => $chatMessage->rel_to_sender->id,
                'name' => $chatMessage->rel_to_sender->name,
            ],
            'receiver' => [
                'id' => $chatMessage->rel_to_receiver->id,
                'name' => $chatMessage->rel_to_receiver->name,
            ]
        ];
    }
}
