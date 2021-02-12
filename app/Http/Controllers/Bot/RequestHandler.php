<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Bot\Traits\RequestHandlerTrait;
use App\Models\AddToGroup;
use App\Models\BestVideo;
use App\Models\buttons\InlineButtons;
use App\Models\buttons\Menu;
use App\Models\buttons\RichMedia;
use App\Models\Channels;
use App\Models\Groups;
use App\Models\Invited;
use App\Models\Language;
use App\Models\LikesVideo;
use App\Models\PostVideo;

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
        if($bestVideo = $this->getCompetitionBestVideo()) {
            if($this->getType() == 'video') {
                if(($video = $this->saveFile()) !== null) {
                    $this->setInteraction('bestVideoSendText', [
                        'video' => $video,
                        'best_videos_id' => $bestVideo->id
                    ]);
                    $this->send('{send_text}', Menu::back());
                }
                else {
                    $this->send('{error}');
                }
            }
        }
        else {
            $this->send('{no_competition}');
        }
    }

    public function bestVideoSendText($params) {
        if($this->getType() == 'text') {
            $postVideo = new PostVideo();
            $postVideo->best_videos_id = $params['best_videos_id'];
            $postVideo->users_id = $this->getUserId();
            $postVideo->video = $params['video'];
            $postVideo->text = $this->getMessage();
            $postVideo->save();

            $this->send('{video_sent_for_moderation}', Menu::main());
        }
        else {
            $this->send('{send_text}');
        }
    }

    public function getCompetitionBestVideo() {
        return BestVideo::where('active', '1')
            ->first();
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
        $group = Groups::where('group_id', $data['chat']['id'])->first();
        if($group->addToGroup->active == 1) {
            if(Invited::where('referrer', $data['from']['id'])
                ->where('referral', $data['whom']['id'])
                ->where('groups_id', $group->id)
                ->exists()
            ) return;
            $invited = new Invited;
            $invited->groups_id = $group->id;
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
        if(substr($this->getMessage(), 0, 13) == 'likeBestVideo') {
            $arr = explode('__', $this->getMessage());
            $bestVideoId = explode('_', $arr[1])[0];
            $postId = explode('_', $arr[1])[1];

            if(BestVideo::where('id', $bestVideoId)->where('active', '0')->exists()) {
                $this->answerCallbackQuery('{competition_is_over}');
            }
            else {
                $userChat = json_decode($this->getRequest())->callback_query->from->id;
                if(LikesVideo::where('post_videos_id', $postId)->where('user_chat', $userChat)->exists()) {
                    $this->answerCallbackQuery('{you_have_already_rated_this_post}');
                }
                else {
                    $likeVideo = new LikesVideo();
                    $likeVideo->post_videos_id = $postId;
                    $likeVideo->user_chat = $userChat;
                    $likeVideo->save();
                    $this->answerCallbackQuery('{thank_you_for_rating}');
                }
            }
        }
    }
}
