<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Bot\Traits\RequestHandlerTrait;
use App\Models\buttons\InlineButtons;
use App\Models\buttons\Menu;
use App\Models\buttons\RichMedia;

class RequestHandler extends BaseRequestHandler {

    use RequestHandlerTrait;
    // TODO: bot commands

    public function methodFromGroupAndChat() {
        $type = $this->getType();

        if(method_exists($this, $type)) {
            $this->$type();
        }
    }

    public function new_chat_participant() {
        dd($this->getDataByType());
    }

    public function left_chat_participant() {
        dd($this->send('Post', InlineButtons::like(), true));
//        dd($this->getDataByType());
    }

    public function callback_query() {
//        dd($this->getChatMember('709935151', '-1001461794802'));
        $this->answerCallbackQuery('text');
        dd($this->sendTo($this->getChat(), 'Post', InlineButtons::like(), true));
        if(substr($this->getMessage(), 0, 4) == 'like') {
            $postId = explode('__', $this->getMessage());
            $type = $this->getDataByType();
            dd($type['chat']['type']);
        }
    }
}
