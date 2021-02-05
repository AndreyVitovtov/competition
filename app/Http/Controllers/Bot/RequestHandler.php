<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Bot\Traits\RequestHandlerTrait;
use App\Models\buttons\InlineButtons;
use App\Models\buttons\Menu;
use App\Models\buttons\RichMedia;
use App\Models\Channels;
use App\Models\Language;

class RequestHandler extends BaseRequestHandler {

    use RequestHandlerTrait;

    public function start() {
        $this->send('{welcome}');
        $res = $this->send('{select_language}:', InlineButtons::languages(), true);
        $this->setInteraction('', [
            'messageId' => $this->getIdSendMessage($res)
        ]);
    }

    public function selectLanguage($id) {
        $this->delMessage();
        $user = $this->getUser();
        $user->languages_id = $id;
        $user->save();
        $language = Language::find($id);
        $this->send('{language_selected}', [], false, [], [
            'lang' => mb_strtolower($language->name)
        ]);
        $this->checkSubscription();
        $this->send('{main_menu}', Menu::main());
    }

    public function changeLanguage() {
        $this->checkSubscription();
        $res = $this->send('{select_language}', InlineButtons::languages(), true);
        $this->setInteraction('', [
            'messageId' => $this->getIdSendMessage($res)
        ]);
    }

    public function methodFromGroupAndChat() {
        $type = $this->getType();
        $type = str_replace(' ', '', ucwords(str_replace('-', ' ', $type)));
        if(method_exists($this, $type)) {
            $this->$type();
        }
    }

    public function activeCompetitions() {
        $this->checkSubscription();
        //TODO: Добавить проверку есть ли конкурсы
        $this->send('{select_competition}', Menu::competitions());
    }

    public function subscribed() {
        $this->checkSubscription();
        $this->send('main_menu', Menu::main());
    }

    private function checkSubscription() {
        $channel = $this->getUser()->language->channel;
        if (!$this->getChatMember($this->getChat(), $channel->channel_id)) {
            $this->send("Чтобы продолжить подпишитесь на канал",
                InlineButtons::checkSubscription($channel->name), true
            );
           die;
        }
    }










    public function newChatParticipant() {
        dd($this->getDataByType());
    }

    public function leftChatParticipant() {
        dd($this->send('Post', InlineButtons::like(), true));
//        dd($this->getDataByType());
    }

    public function callbackQuery() {
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
