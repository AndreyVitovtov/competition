<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsButtonsSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('settings_buttons_ru')->insert([
            ["id" => "1","name" => "start","text" => "U3RhcnQg8J+agA==","menu" => "Старт","menu_us" => "Start"],
            ["id" => "2","name" => "back","text" => "8J+UmSDQndCw0LfQsNC0","menu" => "back","menu_us" => "back"],
            ["id" => "3","name" => "contacts","text" => "4pyJINCf0L7QtNC00LXRgNC20LrQsA==","menu" => "Главное меню","menu_us" => "Main menu"],
            ["id" => "4","name" => "contacts_general","text" => "0J7QsdGJ0LjQtSDQstC+0L/RgNC+0YHRiw==","menu" => "Поддержка","menu_us" => "Support"],
            ["id" => "5","name" => "contacts_advertising","text" => "0KDQtdC60LvQsNC80LA=","menu" => "Поддержка","menu_us" => "Support"],
            ["id" => "6","name" => "contacts_offers","text" => "0J/RgNC10LTQu9C+0LbQtdC90LjRjw==","menu" => "Поддержка","menu_us" => "Support"],
            ["id" => "7","name" => "languages","text" => "8J+RhSDQr9C30YvQutC4","menu" => "Главное меню","menu_us" => "Main menu"],
            ["id" => "8","name" => "activeCompetitions","text" => "8J+OryDQkNC60YLQuNCy0L3Ri9C1INC60L7QvdC60YPRgNGB0Ys=","menu" => "main","menu_us" => "main"],
            ["id" => "9","name" => "changeLanguage","text" => "0K/Qt9GL0Lo=","menu" => "main","menu_us" => "main"],
            ["id" => "10","name" => "groupInvitations","text" => "8J+ZiyDQn9GA0LjQs9C70LDRiNC10L3QuNGPINCyINCz0YDRg9C/0L/Rgw==","menu" => "competitions","menu_us" => "competitions"],
            ["id" => "11","name" => "bestPhoto","text" => "8J+TtyDQm9GD0YfRiNC10LUg0YTQvtGC0L4=","menu" => "competitions","menu_us" => "competitions"],
            ["id" => "12","name" => "bestVideo","text" => "8J+OrCDQm9GD0YfRiNC10LUg0LLQuNC00LXQvg==","menu" => "competitions","menu_us" => "competitions"],
            ["id" => "13","name" => "otherCompetitions","text" => "8J+OryDQlNGA0YPQs9C40LUg0LrQvtC90LrRg9GA0YHRiw==","menu" => "competitions","menu_us" => "competitions"],
            ["id" => "14","name" => "subscribed","text" => "0J/QvtC00L/QuNGB0LDQu9GB0Y8=","menu" => "","menu_us" => ""],
            ["id" => "15","name" => "group","text" => "0JPRgNGD0L/Qv9Cw","menu" => "","menu_us" => ""],
            ["id" => "16","name" => "like","text" => "8J+RjQ==","menu" => "","menu_us" => ""],
        ]);
    }
}
