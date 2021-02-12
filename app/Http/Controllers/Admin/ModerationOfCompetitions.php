<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\API\Telegram;
use App\Models\PostVideo;
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
        return redirect()->to(route('moderation-of-competitions-video'));
    }









    public function photo() {
        return view('admin.moderation-of-competitions.video', [
            'menuItem' => 'moderationofcompetitionsphoto'
        ]);
    }


}
