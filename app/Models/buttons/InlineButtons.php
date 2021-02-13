<?php

namespace App\Models\buttons;

use App\Models\Language;
use App\Models\Text;

class InlineButtons {

    public static function contacts() {
        return [
            [
                [
                    "text" => "{contacts_general}",
                    "callback_data" => "general"
                ], [
                    "text" => "{contacts_access}",
                    "callback_data" => "access"
                ]
            ], [
                [
                    "text" => "{contacts_advertising}",
                    "callback_data" => "advertising"
                ], [
                    "text" => "{contacts_offers}",
                    "callback_data" => "offers"
                ]
            ]
        ];
    }

    public static function languages() {
        $languages = [];
        $lang = Language::all()->toArray();
        foreach ($lang as $l) {
            $languages[] = [[
                'text' => base64_decode($l['emoji'])." ".$l['name'],
                'callback_data' => 'selectLanguage__' . $l['id']
            ]];
        }
        return $languages;
    }

    public static function like($type, $bestId, $postId,  $count = 0) {
        if($type == 'video') {
            $callbackQuery = 'likeBestVideo';
        }
        else {
            $callbackQuery = 'likeBestPhoto';
        }
        $callbackQuery .= "__".$bestId."_".$postId;
        $text = (($count) ? "👍 $count" : "👍");
        return [[[
                "text" => $text,
                "callback_data" => $callbackQuery
            ]]];
    }

    public static function checkSubscription($link) {
        return [
            [[
                "text" => "Channel",
                "url" => $link

            ]],
            [[
                "text" => '{subscribed}',
                "callback_data" => "mainMenu"
            ]]
        ];
    }

    public static function addToGroup($groups) {
        $buttons = [];
        foreach($groups as $group) {
            $buttons[] = [[
                'text' => "{group}",
                'url' => $group->group_link
            ]];
        }
        return $buttons;
    }


}
