<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\API\Telegram;
use App\Models\BotUsers;
use App\Models\buttons\Menu;
use App\Models\PostPhoto;
use App\Models\PostVideo;
use App\Models\Text;
use App\Models\User;
use Illuminate\Http\Request;

class ModerationOfCompetitions extends Controller {

    public function video() {
        return view('admin.moderation-of-competitions.video', [
            'videos' => PostVideo::with('user')
                ->where('post_id', NULL)
                ->get(),
            'menuItem' => 'moderationofcompetitionsvideo'
        ]);
    }

    public function videoPlay($id) {
        $postVideo = PostVideo::find($id);
        return view('admin.moderation-of-competitions.play', [
            'menuItem' => 'moderationofcompetitionsvideo',
            'video' => url('video/'.$postVideo->video),
            'text' => $postVideo->text
        ]);
    }

    public function videoDelete(Request $request) {
        $postVideo = PostVideo::find($request->post('id'));
        unlink(public_path('video/'.$postVideo->video));
        PostVideo::where('id', $postVideo->id)->delete();
        return redirect()->to(route('moderation-of-competitions-video'));
    }

    public function videoActive(Request $request) {
        $postVideo = PostVideo::find($request->post('id'));
        $telegram  = new Telegram(TELEGRAM_TOKEN);
        $res = $telegram->sendDocument(
            $postVideo->bestVideo->channel_id,
            url('video/'.$postVideo->video),
            $postVideo->text,
            [
                'inlineButtons' => [[[
                    'text' => '👍',
                    'callback_data' => 'likeBestVideo__'.$postVideo->bestVideo->id.'_'.$postVideo->id
                ]]]
            ]
        );
        $postId = json_decode($res)->result->message_id;
        PostVideo::where('id', $request->post('id'))->update(['post_id' => $postId]);
        unlink(public_path('video/'.$postVideo->video));

        $text = new Text();
        $user = BotUsers::find($postVideo->users_id);
        $message = $text->valueSubstitution($user, '{video_published}', 'pages');
        $telegram->sendMessage($user->chat, $message);

        return redirect()->to(route('moderation-of-competitions-video'));
    }




    public function photo() {
        return view('admin.moderation-of-competitions.photo', [
            'photos' => PostPhoto::with('user')
                ->where('post_id', NULL)
                ->get(),
            'menuItem' => 'moderationofcompetitionsphoto'
        ]);
    }

    public function photoDelete(Request $request) {
        $postPhoto = PostPhoto::find($request->post('id'));
        unlink(public_path('video/'.$postPhoto->photo));
        PostPhoto::where('id', $postPhoto->id)->delete();
        return redirect()->to(route('moderation-of-competitions-photo'));
    }

    public function photoActive(Request $request) {
        $postPhoto = PostPhoto::find($request->post('id'));
        $telegram  = new Telegram(TELEGRAM_TOKEN);
        $res = $telegram->sendPhoto(
            $postPhoto->bestPhoto->channel_id,
            url('photo/'.$postPhoto->photo),
            $postPhoto->text,
            [
                'inlineButtons' => [[[
                    'text' => '👍',
                    'callback_data' => 'likeBestPhoto__'.$postPhoto->bestPhoto->id.'_'.$postPhoto->id
                ]]]
            ]
        );
        $postId = json_decode($res)->result->message_id;
        PostPhoto::where('id', $request->post('id'))->update(['post_id' => $postId]);
        unlink(public_path('photo/'.$postPhoto->photo));

        $text = new Text();
        $user = BotUsers::find($postPhoto->users_id);
        $message = $text->valueSubstitution($user, '{photo_published}', 'pages');
        $telegram->sendMessage($user->chat, $message);
        return redirect()->to(route('moderation-of-competitions-photo'));
    }

    public function photoRead($id) {
        return view('admin.moderation-of-competitions.photo-read', [
            'photo' => PostPhoto::find($id),
            'menuItem' => 'moderationofcompetitionsphoto'
        ]);
    }
}
