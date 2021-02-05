<?php

namespace App\Models\buttons;

use App\Models\Language;

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

    public static function like() {
        return [[[
                "text" => "{like}",
                "callback_data" => "like__50"
            ]]];
    }

    public static function checkSubscription($name) {
        return [
            [[
                "text" => 'channel '.$name,
                "callback_data" => "123"
            ]],
            [[
                "text" => "{subscribed}",
                "callback_data" => "subscribed"
            ]]
        ];
    }


}
