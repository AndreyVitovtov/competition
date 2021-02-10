<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Bot\Traits\RequestHandlerTrait;
use App\Models\AddToGroup;
use App\Models\buttons\InlineButtons;
use App\Models\buttons\Menu;
use App\Models\buttons\RichMedia;
use App\Models\Channels;
use App\Models\Invited;
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
        $res = $this->send('{select_language}', InlineButtons::languages(), true);
        $this->setInteraction('', [
            'messageId' => $this->getIdSendMessage($res)
        ]);
    }

    public function activeCompetitions() {
        $this->checkSubscription();
        //TODO: Добавить проверку есть ли конкурсы
        $this->send('{select_competition}', Menu::competitions());
    }

    public function subscribed() {

        $this->send('main_menu', Menu::main());
    }

    private function checkSubscription() {
        $channel = $this->getUser()->language->channel;

        if (!$this->getChatMember($this->getChat(), $channel->channel_id)) {
            $this->send("Чтобы продолжить подпишитесь на канал",
                InlineButtons::checkSubscription($channel->link), true
            );
           die;
        }
    }

    public function mainMenu() {
        $this->checkSubscription();
        $this->send('{main_menu}', Menu::main());
    }

    public function groupInvitations() {
        $addToGroup = AddToGroup::where('languages_id', $this->getUser()->languages_id)
            ->where('active', '1')
            ->first();
        if(empty($addToGroup)) {
            $this->send('{no_competition}', Menu::competitions());
        }
        else {
            $this->send($addToGroup->description, InlineButtons::addToGroup($addToGroup->group_link), true);
        }

    }

    public function bestVideo() {
        $this->setInteraction('sendVideo');
        $this->send('{send_video}', Menu::back());
    }

    public function sendVideo() {
        $this->forwardMessage('303688172');
        dd($this->getFilePath(true));
    }








    public function methodFromGroupAndChat() {

        $type = $this->getType();
        $type = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $type))));
        if(method_exists($this, $type)) {
            $this->$type();
        }
    }

    public function newChatParticipant() {
        $data = $this->getDataByType();
        $addToGroup = AddToGroup::where('group_id', $data['chat']['id'])
            ->where('active', '1')
            ->first();
        if(!empty($addToGroup)) {
            if(Invited::where('referrer', $data['from']['id'])
                ->where('referral', $data['whom']['id'])
                ->where('add_to_group_id', $addToGroup->id)
                ->exists()
            ) return;
            $invited = new Invited;
            $invited->add_to_group_id = $addToGroup->id;
            $invited->referrer = $data['from']['id'];
            $invited->referral = $data['whom']['id'];
            $invited->save();
        }
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
