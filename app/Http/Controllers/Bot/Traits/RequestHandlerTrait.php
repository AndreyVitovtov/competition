<?php


namespace App\Http\Controllers\Bot\Traits;


use App\Models\BotUsers;
use App\Models\buttons\ButtonsFacebook;
use App\Models\buttons\ButtonsTelegram;
use App\Models\buttons\ButtonsViber;
use App\Models\buttons\InlineButtons;
use App\Models\buttons\Menu;
use App\Models\buttons\RichMedia;
use App\Models\ContactsModel;
use App\Models\ContactsType;
use App\Models\Language;
use App\Services\Contracts\BotService;

trait RequestHandlerTrait {
    private $messenger;
    private $botService;
    private $richMedia;

    public function __construct(BotService $botService) {
        $this->botService = $botService;

        $headers = getallheaders();
        if(isset($_SERVER['HTTP_X_VIBER_CONTENT_SIGNATURE']) || isset($headers['Viber'])) {
            $this->messenger = "Viber";
        }
        elseif(isset($headers['Facebook-Api-Version'])) {
            $this->messenger = "Facebook";
        }
        else {
            $this->messenger = "Telegram";
        }

        define("MESSENGER", $this->messenger);
        parent::__construct();

        if($this->messenger == "Facebook") {
            $this->mark_seen();
            $this->typing_on();
            sleep(rand(1, 2));
        }
    }

    public function getMessenger() {
        return $this->messenger;
    }

    public function buttons() {
        if($this->messenger == "Viber") {
            return new ButtonsViber();
        }
        elseif($this->messenger == "Telegram") {
            return new ButtonsTelegram();
        }
        else {
            return new ButtonsFacebook();
        }
    }

    public function index() {
        file_put_contents(public_path("json/request.json"), $this->getRequest());

        if($this->getType() == "started") {
            $this->setUserId();

            $context = $this->getBot()->getContext();
            if($context) {
                $context = str_replace(" ", "+", $context);
                if($this->messenger == "Viber" && substr($context, -2) != "==") {
                    $context .= "==";
                }

                $this->startRef($context);
            }

            $this->send("{greeting}", Menu::start());
        }
        else {
            $this->callMethodIfExists();
        }

        return response('OK', 200)->header('Content-Type', 'text/plain');


//TODO: ДОБАВИТЬ WEBHOOK FACEBOOK MESSENGER
//        $verify_token = "31ad48b8b8b266e8f653de34252e44a0"; //Маркер подтверждения
//        if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verify_token) {
//            echo $_REQUEST['hub_challenge'];
//        }
    }

    public function start($params = null) {

        $this->delInteraction();

        $this->setUserStart();

        //FACEBOOK REFERRALS
        if(MESSENGER == "Facebook") {
            $chat = $this->getBot()->getRef();
            if($chat != null) {
                $this->startRef($chat);
            }
        }

       //TODO: execute start method

//        $this->send("{welcome}", [
//            'buttons' => $this->buttons()->main_menu($this->getUserId())
//        ]);
    }

    public function languages() {
        if(MESSENGER == "Viber") {
            $languages = Language::all();
            $count = $languages->count()+1;
            $this->send("{choose_language}", Menu::main());
            $this->sendCarousel(
                RichMedia::languages(),
                ['rows' => $count < 7 ? $count : 7],
                Menu::back()
            );
        }
        elseif(MESSENGER == "Telegram") {
            $this->send("{choose_language}", InlineButtons::languages(), true);
        }
    }

    public function lang($code) {
        $user = BotUsers::find($this->getUserId());
        $user->language = $code;
        $user->save();
        $this->send('{language_saved}', Menu::main());
    }

    public function contacts() {
        $this->setInteraction('contacts_select_topic');

        $this->send("{send_support_message}", Menu::back());

        if(MESSENGER == "Facebook") {
            $this->send("{select_topic}", ButtonsFacebook::contacts());
        }
        elseif(MESSENGER == "Telegram") {
            $this->send("{select_topic}", InlineButtons::contacts(), true);
        }
        else {
            $this->send("{select_topic}", Menu::back());
            $this->sendCarousel(
                RichMedia::contacts(),
                ['rows' => 4],
                Menu::back()
            );
        }
    }

    public function contacts_select_topic() {
        $topic = $this->getBot()->getMessage();
        if($topic == "general" ||
            $topic == "access" ||
            $topic == "advertising" ||
            $topic == "offers") {
            $this->send("{send_message}", Menu::back(), true);
            $this->delInteraction();
            $this->setInteraction('contacts_send_message', [
                'topic' => $topic
            ]);
        }
        else {
            $this->contacts();
        }
    }

    public function contacts_send_message($params) {
        $contactsType = ContactsType::where('type', $params['topic'])->first();
        $contacts = new ContactsModel();
        $contacts->contacts_type_id = $contactsType->id;
        $contacts->users_id = $this->getUserId();
        $contacts->text = $this->getBot()->getMessage();
        $contacts->date = date("Y-m-d");
        $contacts->time = date("H:i:s");
        $contacts->save();

        $this->send("{message_sending}", Menu::main());
        $this->delInteraction();
    }

    public function main() {
        $this->delInteraction();
        $this->send("{main_menu}", Menu::main());
    }

    public function back() {
//        $this->delMessage();
        $this->delInteraction();

        $this->send("{main_menu}", Menu::main());
        exit;
    }

//    public function group() {
//        $this->send("{group}", [
//            'inlineButtons' => InlineButtons::group()
//        ]);
//    }





    public function performAnActionRef($referrerId) {
        $this->userAccess($referrerId);
//      $this->send("REF SYSTEM ".$chat);
    }

//    public function userAccess($id) {
//        $count = RefSystem::where('referrer', $id)->count();
//
//        if($count == COUNT_INVITES_ACCESS) {
//            $user = BotUsers::find($id);
//            $user->access = '1';
//            $user->access_free = '1';
//            $user->save();
//
//            $this->sendTo($user->chat, "{got_free_access}", Menu::main(), false, [], [
//                'count' => COUNT_INVITES_ACCESS
//            ]);
//        }
//    }
}
