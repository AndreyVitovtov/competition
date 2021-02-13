<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Bot\Traits\RequestHandlerTrait;
use App\Models\AddToGroup;
use App\Models\BestPhoto;
use App\Models\BestVideo;
use App\Models\buttons\InlineButtons;
use App\Models\buttons\Menu;
use App\Models\buttons\RichMedia;
use App\Models\Channels;
use App\Models\Groups;
use App\Models\Invited;
use App\Models\Language;
use App\Models\LikesPhoto;
use App\Models\LikesVideo;
use App\Models\Others;
use App\Models\PostPhoto;
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
        if(($competitions = $this->checkCompetitions()) != null) {
            $this->send('{select_competition}', Menu::competitions($competitions));
        }
        else {
            $this->send('{no_competition}');
        }
    }

    private function checkCompetitions(): ? array {
        $language = $this->getUser()->languages_id;
        $addToGroup = AddToGroup::where('languages_id', $language)
            ->where('active', '1')
            ->exists();
        $bestPhoto = BestPhoto::where('languages_id', $language)
            ->where('active', '1')
            ->exists();
        $bestVideo = BestVideo::where('languages_id', $language)
            ->where('active', '1')
            ->exists();
        $other = Others::where('languages_id', $language)
            ->exists();
        if($addToGroup || $bestPhoto || $bestVideo || $other) {
            return [
                'groupInvitations' => $addToGroup,
                'bestPhoto' => $bestPhoto,
                'bestVideo' => $bestVideo,
                'otherCompetitions' => $other
            ];
        }
        else {
            return null;
        }
    }

    public function subscribed() {
        $this->send('main_menu', Menu::main());
    }

    private function checkSubscription() {
        $channel = $this->getUser()->language->channel ?? null;

        if ($channel != null && $channel->channel_id != '0' && !$this->getChatMember($this->getChat(), $channel->channel_id)) {
            $this->send('{check_subscription}',
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
            $this->send('{no_competition}', Menu::competitions($this->checkCompetitions()));
        }
        else {
            $this->send($addToGroup->description, InlineButtons::addToGroup($addToGroup->groups), true);
        }

    }

    public function bestVideo() {
        $bestVideo = BestVideo::where('languages_id', $this->getUser()->languages_id)
            ->where('active', '1')
            ->first();
        if(empty($bestVideo)) {
            $this->send('{no_competition}', Menu::competitions($this->checkCompetitions()));
        }
        else {
            $this->setInteraction('sendVideo');
            $this->send('{send_video}', Menu::back());
        }
    }

    public function sendVideo() {
        if($bestVideo = $this->getCompetitionBestVideo()) {
            if($this->getType() == 'video') {
                if(($video = $this->saveFile(null, null, 'video')) !== null) {
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

    public function bestPhoto() {
        $bestPhoto = BestPhoto::where('languages_id', $this->getUser()->languages_id)
            ->where('active', '1')
            ->first();
        if(empty($bestPhoto)) {
            $this->send('{no_competition}', Menu::competitions($this->checkCompetitions()));
        }
        else {
            $this->setInteraction('competitionSendPhoto');
            $this->send('{send_photo}', Menu::back());
        }
    }

    public function competitionSendPhoto() {
        if($bestPhoto = $this->getCompetitionBestPhoto()) {
            if($this->getType() == 'photo') {
                if(($photo = $this->saveFile(null, null, 'photo')) !== null) {
                    $this->setInteraction('bestPhotoSendText', [
                        'photo' => $photo,
                        'best_photos_id' => $bestPhoto->id
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

    public function bestPhotoSendText($params) {
        if($this->getType() == 'text') {
            $postPhoto = new PostPhoto();
            $postPhoto->best_photos_id = $params['best_photos_id'];
            $postPhoto->users_id = $this->getUserId();
            $postPhoto->photo = $params['photo'];
            $postPhoto->text = $this->getMessage();
            $postPhoto->save();

            $this->send('{photo_sent_for_moderation}', Menu::main());
        }
        else {
            $this->send('{send_text}');
        }
    }

    public function getCompetitionBestPhoto() {
        return BestPhoto::where('active', '1')
            ->first();
    }

    public function otherCompetitions() {
        $user = $this->getUser();
        $other = Others::where('languages_id', $user->languages_id)->first();
        if($other) {
            $this->send($other->description, Menu::main());
        }
        else {
            $this->send('{no_competition}');
        }
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
        if($group->addToGroup->active == '1') {
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
                    $postVideo = PostVideo::find($postId);
                    $channelId = $postVideo->bestVideo->channel_id;
                    $postId = $postVideo->post_id;
                    $countLikes = $postVideo->likes->count();
                    $this->editMessageReplyMarkup($channelId, $postId, InlineButtons::like(
                        'video',
                        $postVideo->bestVideo->id,
                        $postVideo->id,
                        $countLikes)
                    );
                }
            }
        }

        if(substr($this->getMessage(), 0, 13) == 'likeBestPhoto') {
            $arr = explode('__', $this->getMessage());
            $bestPhotoId = explode('_', $arr[1])[0];
            $postId = explode('_', $arr[1])[1];

            if(BestPhoto::where('id', $bestPhotoId)->where('active', '0')->exists()) {
                $this->answerCallbackQuery('{competition_is_over}');
            }
            else {
                $userChat = json_decode($this->getRequest())->callback_query->from->id;
                if(LikesPhoto::where('posts_photos_id', $postId)->where('user_chat', $userChat)->exists()) {
                    $this->answerCallbackQuery('{you_have_already_rated_this_post}');
                }
                else {
                    $likeVideo = new LikesPhoto();
                    $likeVideo->posts_photos_id = $postId;
                    $likeVideo->user_chat = $userChat;
                    $likeVideo->save();
                    $this->answerCallbackQuery('{thank_you_for_rating}');

                    $postPhoto = PostPhoto::find($postId);
                    $channelId = $postPhoto->bestPhoto->channel_id;
                    $postId = $postPhoto->post_id;
                    $countLikes = $postPhoto->likes->count();
                    $this->editMessageReplyMarkup($channelId, $postId, InlineButtons::like(
                        'photo',
                        $postPhoto->bestVideo->id,
                        $postPhoto->id,
                        $countLikes)
                    );
                }
            }
        }
    }
}
