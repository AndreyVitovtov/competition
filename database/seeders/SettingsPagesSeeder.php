<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsPagesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        DB::table('settings_pages_ru')->insert([
            ["id" => "1","name" => "greeting","text" => "0J3QsNC20LzQuNGC0LUgU3RhcnQg8J+agCDQtNC70Y8g0L/RgNC+0LTQvtC70LbQtdC90LjRjw==","description" => "Приветствие","description_us" => "Greeting"],
            ["id" => "2","name" => "welcome","text" => "0J/RgNC40LLQtdGC0YHRgtCy0LjQtSDQsdC+0YLQsA==","description" => "Приветствие","description_us" => "Greeting"],
            ["id" => "3","name" => "main_menu","text" => "0JPQu9Cw0LLQvdC+0LUg0LzQtdC90Y4=","description" => "Главное меню","description_us" => "Main menu"],
            ["id" => "4","name" => "unknown_team","text" => "0J3QtdC40LfQstC10YHRgtC90LDRjyDQutC+0LzQsNC90LTQsCDwn5qn","description" => "Неизвестная команда","description_us" => "Unknown team"],
            ["id" => "5","name" => "error","text" => "0KfRgtC+INGC0L4g0L/QvtGI0LvQviDQvdC1INGC0LDQuigoINCf0L7Qv9GA0L7QsdGD0LnRgtC1INC/0L7Qt9C20LVcblxue3tlcnJvcn19","description" => "Ошибка","description_us" => "Error"],
            ["id" => "6","name" => "send_support_message","text" => "0J7RgtC/0YDQsNCy0LjRgtGMINGB0L7QvtCx0YnQtdC90LjQtSDQsiDQv9C+0LTQtNC10YDQttC60YM=","description" => "Отправить сообщение в поддержку","description_us" => "Send a message to support"],
            ["id" => "7","name" => "select_topic","text" => "0JLRi9Cx0LXRgNC40YLQtSDRgtC10LzRgzo=","description" => "Выберите тему","description_us" => "Choose a topic"],
            ["id" => "8","name" => "send_message","text" => "0J7RgtC/0YDQsNCy0YzRgtC1INGB0L7QvtCx0YnQtdC90LjQtQ==","description" => "Отправьте сообщение","description_us" => "Send a message"],
            ["id" => "9","name" => "message_sending","text" => "0KHQvtC+0LHRidC10L3QuNC1INC+0YLQv9GA0LDQstC70LXQvdC+","description" => "Сообщение отправлено","description_us" => "Message sent"],
            ["id" => "10","name" => "choose_language","text" => "0JLRi9Cx0LXRgNC40YLQtSDRj9C30YvQug==","description" => "Выберите язык","description_us" => "Choose language"],
            ["id" => "11","name" => "language_saved","text" => "0K/Qt9GL0Log0YHQvtGF0YDQsNC90LXQvQ==","description" => "Язык сохранен","description_us" => "Language saved"],
            ["id" => "12","name" => "select_language","text" => "0JLRi9Cx0LXRgNC40YLQtSDRj9C30YvQug==","description" => "Выберите язык","description_us" => "Choose language"],
            ["id" => "13","name" => "language_selected","text" => "0JLRi9Cx0YDQsNC9IHt7bGFuZ319INGP0LfRi9C6","description" => "Выбран язык","description_us" => "Language selected"],
            ["id" => "14","name" => "select_competition","text" => "0JLRi9Cx0LXRgNC40YLQtSDQutC+0L3QutGD0YDRgQ==","description" => "Выберите конкурс","description_us" => "Select competition"],
            ["id" => "15","name" => "no_competition","text" => "0JrQvtC90LrRg9GA0YEg0L7RgtGB0YPRgtGB0YLQstGD0LXRgg==","description" => "Конкурс отсутствует","description_us" => "No competition"],
            ["id" => "16","name" => "send_photo","text" => "0J7RgtC/0YDQsNCy0YzRgtC1INGC0LXQutGB0YI=","description" => "Отправьте фото","description_us" => "Send a photo"],
            ["id" => "17","name" => "send_video","text" => "0J7RgtC/0YDQsNCy0YzRgtC1INCy0LjQtNC10L4=","description" => "Отправьте видео","description_us" => "Submit your video"],
            ["id" => "18","name" => "send_text","text" => "0J7RgtC/0YDQsNCy0YzRgtC1INGC0LXQutGB0YI=","description" => "Отправьте текст","description_us" => "Send text"],
            ["id" => "19","name" => "check_subscription","text" => "0KfRgtC+0LHRiyDQv9GA0L7QtNC+0LvQttC40YLRjCDQv9C+0LTQv9C40YjQuNGC0LXRgdGMINC90LAg0LrQsNC90LDQuw==","description" => "Чтобы продолжить подпишитесь на канал","description_us" => "To continue subscribe to the channel"],
            ["id" => "20","name" => "select_competition","text" => "0JLRi9Cx0LXRgNC40YLQtSDQutC+0L3QutGD0YDRgQ==","description" => "Выберите конкурс","description_us" => "Select competition"],
            ["id" => "21","name" => "video_sent_for_moderation","text" => "0JLQuNC00LXQviDQvtGC0L/RgNCw0LLQu9C10L3QviDQvdCwINC80L7QtNC10YDQsNGG0LjRjg==","description" => "Видео отправлено на модерацию","description_us" => "Video sent for moderation"],
            ["id" => "22","name" => "photo_sent_for_moderation","text" => "0KTQvtGC0L4g0L7RgtC/0YDQsNCy0LvQtdC90L4g0L3QsCDQvNC+0LTQtdGA0LDRhtC40Y4=","description" => "Фото отправлено на модерацию","description_us" => "Photo has been sent for moderation"],
            ["id" => "23","name" => "competition_is_over","text" => "0JrQvtC90LrRg9GA0YEg0LfQsNC60L7QvdGH0LjQu9GB0Y8=","description" => "Конкурс закончился","description_us" => "The competition is over"],
            ["id" => "24","name" => "you_have_already_rated_this_post","text" => "0JLRiyDRg9C20LUg0L/RgNC+0LPQvtC70L7RgdC+0LLQsNC70Lgg0LfQsCDQtNCw0L3QvdGL0Lkg0L/QvtGB0YI=","description" => "Вы уже проголосовали за данный пост","description_us" => "You have already voted for this post."],
            ["id" => "25","name" => "thank_you_for_rating","text" => "0KHQv9Cw0YHQuNCx0L4g0LfQsCDQvtGG0LXQvdC60YM=","description" => "Спасибо за оценку","description_us" => "thank you for rating"],
        ]);
    }
}
