<?php

namespace App\Http\Controllers\Bot\Traits;

use App\Models\API\FacebookMessenger;
use App\Models\API\Telegram;
use App\Models\API\Viber;
use App\Models\BotUsers;
use App\Models\buttons\Buttons;
use App\Models\buttons\ButtonsFacebook;
use App\Models\buttons\ButtonsTelegram;
use App\Models\buttons\ButtonsViber;
use App\Models\buttons\Menu;
use App\Models\Visit;

trait Universal {
    public function __construct() {

        $this->messenger = $this->getMessenger();

        if($this->messenger == "Viber") {
            $this->bot = new Viber(VIBER_TOKEN);
        }
        elseif($this->messenger == "Facebook") {
            $this->bot = new FacebookMessenger(FACEBOOK_TOKEN);
        }
        elseif($this->messenger == "Telegram") {
            $this->bot = new Telegram(TELEGRAM_TOKEN);
        }

        $this->chat = $this->bot->getId();

        if(! $this->chat) {
            return response ('OK', 200)
                ->header('Content-Type', 'text/plain');
        }

        $this->setType();

        /* COUNT VISITS */
        $visit = new Visit();
        $visit->add(date("Y-m-d"), $this->getUserId());
    }

    public function setUserId(): void {
        if($this->messenger == "Viber") {
            $botUsers = new BotUsers();
            $res = $botUsers->where('chat', $this->chat)->first();
            $this->user = $res;
            if(empty($res)) {
                $name = $this->bot->getName();
                if (empty($name['first_name'])) {
                    $name['first_name'] = "test";
                }
                if (empty($name['last_name'])) {
                    $name['last_name'] = "test";
                }
                if (empty($name['username'])) {
                    $name['username'] = "test";
                }

                $botUsers->chat = $this->chat;
                $botUsers->first_name = $name['first_name'];
                $botUsers->last_name = $name['last_name'];
                $botUsers->username = $name['username'];
                $botUsers->country = $this->bot->getCountry();
                $botUsers->messenger = "Viber";
                $botUsers->date = date("Y-m-d");
                $botUsers->time = date("H:i:s");
                $botUsers->save();
                $userId = $botUsers->id;
            } else {
                $userId = $res['id'];
            }

            $this->userId = $userId;
        }
        elseif($this->messenger == "Facebook") {
            $botUsers = new BotUsers();
            $res = $botUsers->where('chat', $this->chat)->first();
            if(empty($res)) {
                $name = $this->bot->getName($this->chat);
                if(empty($name['first_name'])) {
                    $name['first_name'] = "test";
                }
                if (empty($name['last_name'])) {
                    $name['last_name'] = "test";
                }
                if (empty($name['username'])) {
                    $name['username'] = "test";
                }

                $botUsers->chat = $this->chat;
                $botUsers->first_name = $name['first_name'];
                $botUsers->last_name = $name['last_name'];
                $botUsers->username = $name['username'];
                $botUsers->messenger = "Facebook";
                $botUsers->date = date("Y-m-d");
                $botUsers->time = date("H:i:s");
                $botUsers->save();
                $userId = $botUsers->id;
            }
            else {
                $userId = $res['id'];
            }

            $this->userId = $userId;
        }
        elseif($this->messenger == "Telegram") {
            $botUsers = new BotUsers();
            $res = $botUsers->where('chat', $this->chat)->first();
            if(empty($res)) {
                $name = $this->bot->getName();
                if(empty($name['first_name'])) {
                    $name['first_name'] = "test";
                }
                if(empty($name['last_name'])) {
                    $name['last_name'] = "test";
                }
                if(empty($name['username'])) {
                    $name['username'] = "test";
                }

                $botUsers->chat = $this->chat;
                $botUsers->first_name = $name['first_name'];
                $botUsers->last_name = $name['last_name'];
                $botUsers->username = $name['username'];
                $botUsers->messenger = "Telegram";
                $botUsers->date = date("Y-m-d");
                $botUsers->time = date("H:i:s");
                $botUsers->save();
                $userId = $botUsers->id;
            }
            else {
                $userId = $res['id'];
            }

            $this->userId = $userId;
        }
    }

    private function setType() {
        if($this->messenger == "Viber") {
            $this->type = $this->getTypeReq();
        }
        elseif($this->messenger == "Facebook") {
            $obj = json_decode($this->getRequest());
            $arrProperties = $this->getProperties($obj);
            $this->type = $this->getTypeReq($arrProperties);
        }
        elseif($this->messenger == "Telegram") {
            $obj = json_decode($this->getRequest());
            $arrProperties = $this->getProperties($obj);
            $this->type = $this->getTypeReq($arrProperties);
        }
    }

    public function getTypeChat(): ? string {
        $request = json_decode($this->getRequest());
        return $request->message->chat->type ?? $request->channel_post->chat->type ?? null;
    }

    private function getTypeReq($arrProperties = null):? string {
        if($this->messenger == "Viber") {
            $req = json_decode($this->getRequest());
            if(isset($req->message->type)) {
                return $req->message->type; //text, picture
            }
            if($req->event == "conversation_started") {
                return "started";
            }
            return null;
        }
        elseif($this->messenger == "Facebook") {
            $rules = [
                'postback' => 'postback',
                'quick_reply' => 'quick_reply',
                'file' => 'url',
                'message' => 'message'
            ];
            foreach($rules as $type => $rule) {
                if(array_key_exists($rule, $arrProperties)) return $type;
            }
            return 'other';
        }
        elseif($this->messenger == "Telegram") {
            $rules = [
                'callback_query' => 'callback_query',
                'channel_post' => 'channel_post',
                'location' => 'location',
                'contact' => 'contact',
                'reply_to_message' => 'reply_to_message',
                'text' => 'text',
                'document' => 'document',
                'photo' => 'photo',
                'video' => 'video',
                'bot_command' => 'entities',
                'new_chat_participant' => 'new_chat_participant',
                'left_chat_participant' => 'left_chat_participant'
            ];

            foreach($rules as $type => $rule) {
                if(array_key_exists($rule, $arrProperties)) return $type;
            }
            return 'other';
        }
    }

    private function getProperties($obj, $names = []): array {
        if(is_object($obj) || is_array($obj)) foreach ($obj as $name => $el) {
            $names[$name] = $name;
            if (is_object($el) || is_array($el)) {
                $names = $this->getProperties($el, $names);
            }
        }
        return $names;
    }

    public function getDataByType() {
        $request = json_decode($this->getRequest());
        if($this->messenger == "Viber") {
            if (empty($request)) return null;
            if ($this->type == "text") {
                $data = [
                    'message_id' => $request->message_token,
                    'text' => $request->message->text
                ];
            } elseif ($this->type == "picture") {
                $data = [
                    'message_id' => $request->message_token,
                    'picture' => [
                        [
                            'media' => $request->message->media,
                            'thumbnail' => $request->message->thumbnail,
                            'file_name' => $request->message->file_name
                        ]
                    ]
                ];
            } elseif($this->type == "file") {
                $data = [
                    'message_id' => $request->message_token,
                    'data' => [
                        'type' => $request->message->type,
                        'media' => $request->message->media,
                        'file_name' => $request->message->file_name,
                        'size' => $request->message->size
                    ]
                ];
            } elseif($this->type == "location") {
                $data = [
                    'lat' => $request->message->location->lat,
                    'lon' => $request->message->location->lon,
                    'address' => $request->message->location->address
                ];
            }
            elseif($this->type == "contact") {
                $data = [
                    'phone' => $request->message->contact->phone_number
                ];
            }
            else {
                $data = [
                    'message_id' => $request->message_token ?? null,
                    'data' => null
                ];
            }
            return $data;
        }
        elseif($this->messenger == "Facebook") {
            if($this->type == "quick_reply") {
                $data = [
                    'message_id' => $request->entry[0]->id,
                    'text' => $request->entry[0]->messaging[0]->message->quick_reply->payload
                ];
            }
            elseif($this->type == "payload") {
                $data = [
                    'message_id' => $request->entry[0]->id,
                    'text' => $request->entry[0]->messaging[0]->message->text
                ];
            }
            elseif($this->type == "message") {
                $data = [
                    'message_id' => $request->entry[0]->id,
                    'text' => $request->entry[0]->messaging[0]->message->text
                ];
            }
            elseif($this->type == "postback") {
                $data = [
                    'message_id' => $request->entry[0]->id,
                    'text' => $request->entry[0]->messaging[0]->postback->payload
                ];
            }
            elseif($this->type == "file") {
                $data = [
                    'message_id' => $request->entry[0]->id,
                    'type' => $request->entry[0]->messaging[0]->message->attachments[0]->type,
                    'url' => $request->entry[0]->messaging[0]->message->attachments[0]->payload->url
                ];
            }
            else {
                $data = [
                    'message_id' => $request->entry[0]->id,
                    'data' => null
                ];
            }

            return $data;
        }
        elseif($this->messenger == "Telegram") {
            if(empty($request)) return null;
            if($this->type == "text") {
                $data = [
                    'message_id' => $request->message->message_id,
                    'text' => $request->message->text
                ];
            }
            elseif($this->type == "document") {
                $data = [
                    'message_id' => $request->message->message_id,
                    'file_name' => $request->message->document->file_name,
                    'mime_type' => $request->message->document->mime_type,
                    'file_id' => $request->message->document->file_id,
                    'file_unique_id' => $request->message->document->file_unique_id,
                    'file_size' => $request->message->document->file_size
                ];
                if(isset($request->message->document->thumb)) {
                    $data['thumb'] = [
                        'file_id' => $request->message->document->thumb->file_id,
                        'file_unique_id' => $request->message->document->thumb->file_unique_id,
                        'file_size' => $request->message->document->thumb->file_size,
                        'width' => $request->message->document->thumb->width,
                        'height' => $request->message->document->thumb->height
                    ];
                }
            }
            elseif($this->type == "photo") {
                $data = [
                    'message_id' => $request->message->message_id,
                    'photo' => [
                        [
                            'file_id' => $request->message->photo[0]->file_id,
                            'file_unique_id' => $request->message->photo[0]->file_unique_id,
                            'file_size' => $request->message->photo[0]->file_size,
                            'width' => $request->message->photo[0]->width,
                            'height' => $request->message->photo[0]->height
                        ],
//                        [
//                            'file_id' => $request->message->photo[1]->file_id,
//                            'file_unique_id' => $request->message->photo[1]->file_unique_id,
//                            'file_size' => $request->message->photo[1]->file_size,
//                            'width' => $request->message->photo[1]->width,
//                            'height' => $request->message->photo[1]->height
//                        ]
                    ]
                ];
            }
            elseif($this->type == "video") {
                $data = [
                    'message_id' => $request->message->message_id ?? null,
                    'video' => [
                        'duration' => $request->message->video->duration ?? null,
                        'width' => $request->message->video->width ?? null,
                        'height' => $request->message->video->height ?? null,
                        'file_name' => $request->message->video->file_name ?? null,
                        'mime_type' => $request->message->video->mime_type ?? null,
                        'file_id' => $request->message->video->file_id ?? null,
                        'file_unique_id' => $request->message->video->file_unique_id ?? null,
                        'file_size' => $request->message->video->file_size ?? null
                    ],
                    'thumb' => [
                        'file_id' => $request->message->video->thumb->file_id ?? null,
                        'file_unique_id' => $request->message->video->thumb->file_unique_id ?? null,
                        'file_size' => $request->message->video->thumb->file_size ?? null,
                        'width' => $request->message->video->thumb->width ?? null,
                        'height' => $request->message->video->thumb->height ?? null
                    ]
                ];
            }
            elseif($this->type == "callback_query") {
                $data = [
                    'message_id' => $request->callback_query->message->message_id,
                    'data' => $request->callback_query->data,
                    'from' => [
                        'id' => $request->callback_query->from->id,
                        'first_name' => $request->callback_query->from->first_name,
                        'last_name' => $request->callback_query->from->last_name,
                        'username' => $request->callback_query->from->username
                    ],
                    'chat' => [
                        'id' => $request->callback_query->message->chat->id,
                        'title' => $request->callback_query->message->chat->title ?? null,
                        'type' => $request->callback_query->message->chat->type
                    ]
                ];
            }
            elseif($this->type == "bot_command") {
                $data = [
                    'message_id' => $request->callback_query->message->message_id,
                    'text' => $request->callback_query->message->text
                ];
            }
            elseif($this->type == "channel_post") {
                $data = [
                    'message_id' => $request->channel_post->message_id,
                    'text' => $request->channel_post->text
                ];
            }
            elseif($this->type == "location") {
                $data = [
                    'message_id' => $request->message->message_id,
                    'lat' => $request->message->location->latitude,
                    'lon' => $request->message->location->longitude
                ];
            }
            elseif($this->type == "contact") {
                $data = [
                    'phone' => $request->message->contact->phone_number
                ];
            }
            elseif($this->type == "new_chat_participant") {
                $data = [
                    'message_id' => $request->message->message_id ?? null,
                    'from' => [
                        'id' => $request->message->from->id ?? null,
                        'first_name' => $request->message->from->first_name ?? null,
                        'last_name' => $request->message->from->last_name ?? null,
                        'username' => $request->message->from->username ?? null,
                    ],
                    'whom' => [
                        'id' => $request->message->new_chat_participant->id,
                        'first_name' => $request->message->new_chat_participant->first_name ?? null,
                        'last_name' => $request->message->new_chat_participant->last_name ?? null,
                        'username' => $request->message->new_chat_participant->username ?? null,
                    ],
                    'chat' => [
                        'id' => $request->message->chat->id ?? null,
                        'title' => $request->message->chat->title ?? null,
                        'type' => $request->message->chat->type ?? null,
                    ],
                    'date' => $request->message->date ?? null
                ];
            }
            elseif($this->type == 'left_chat_participant') {
                $data = [
                    'message_id' => $request->message->message_id ?? null,
                    'from' => [
                        'id' => $request->message->from->id ?? null,
                        'first_name' => $request->message->from->first_name ?? null,
                        'last_name' => $request->message->from->last_name ?? null,
                        'username' => $request->message->from->username ?? null,
                    ],
                    'whom' => [
                        'id' => $request->message->left_chat_participant->id,
                        'first_name' => $request->message->left_chat_participant->first_name ?? null,
                        'last_name' => $request->message->left_chat_participant->last_name ?? null,
                        'username' => $request->message->left_chat_participant->username ?? null,
                    ],
                    'chat' => [
                        'id' => $request->message->chat->id ?? null,
                        'title' => $request->message->chat->title ?? null,
                        'type' => $request->message->chat->type ?? null,
                    ],
                    'date' => $request->message->date ?? null
                ];
            }
            elseif($this->type == 'reply_to_message') {
                $data = [
                    'message_id' => $request->message->message_id ?? null,
                    'from' => [
                        'id' => $request->message->from->id ?? null,
                        'first_name' => $request->message->from->first_name ?? null,
                        'last_name' => $request->message->from->last_name ?? null,
                        'username' => $request->message->from->username ?? null
                    ],
                    'chat' => [
                        'id' => $request->message->chat->id ?? null,
                        'first_name' => $request->message->chat->first_name ?? null,
                        'last_name' => $request->message->chat->last_name ?? null,
                        'username' => $request->message->chat->username ?? null
                    ],
                    'reply_to_message' => [
                        'message_id' => $request->message->reply_to_message->message_id ?? null,
                        'from' => [
                            'id' => $request->message->reply_to_message->from->id ?? null,
                            'first_name' => $request->message->reply_to_message->from->first_name ?? null,
                            'last_name' => $request->message->reply_to_message->from->last_name ?? null,
                            'username' => $request->message->reply_to_message->from->username ?? null
                        ],
                        'chat' => [
                            'id' => $request->message->reply_to_message->chat->id ?? null,
                            'first_name' => $request->message->reply_to_message->chat->first_name ?? null,
                            'last_name' => $request->message->reply_to_message->chat->last_name ?? null,
                            'username' => $request->message->reply_to_message->chat->username ?? null,
                        ],
                        'forward_from' => [
                            'id' => $request->message->reply_to_message->forward_from->id ?? null,
                            'first_name' => $request->message->reply_to_message->forward_from->first_name ?? null,
                            'last_name' => $request->message->reply_to_message->forward_from->last_name ?? null,
                            'username' => $request->message->reply_to_message->forward_from->username ?? null,
                        ],
                        'text' => $request->message->reply_to_message->text ?? null,
                    ],
                    'text' => $request->message->text
                ];
            }
            else {
                $data = [
                    'message_id' => $request->message->message_id ?? null,
                    'data' => null
                ];
            }
            return $data;
        }
    }

    public function saveFile($path = null, $name = null): ? string {
        $filePath = $this->getFilePath();
        if($this->messenger == "Telegram") {
            $ext = explode(".", $filePath);
        }
        else {
            $ext = explode("?", $filePath);
            $ext = explode(".", $ext[0]);
        }

        if($name == null) {
            $name = time().".".end($ext);
        }

        if($path == null) {
            if(copy($filePath, public_path()."/img/".$name)) return $name;
        }
        else {
            if(copy($filePath, $path.$name)) return $name;
        }

        return null;
    }

    public function getMethodName(): ?string {
        $data = $this->getDataByType();
        if($this->messenger == "Viber") {
            if ($this->type == "text") {
                return trim($data['text']);
            }
            elseif ($this->type == "picture") {
                return "photo_sent";
            }
            elseif ($this->type == "file") {
                return "file";
            }
            elseif ($this->type == "location") {
                return "location";
            }
            elseif ($this->getType() == "contact") {
                return "contact";
            }
            else {
                return null;
            }
        }
        elseif($this->messenger == "Facebook") {
            $name = "";
            if($this->type == "message" || $this->type == "postback" || $this->type == "quick_reply" || $this->type == "payload") {
                $name = trim(trim($data['text'], "/"));
            }
            elseif($this->type == "file") {
                return "file_send";
            }

            if($name == "Начать" || $name == "начать") {
                $name = "start";
            }

            if(isset($name)) {
                $rname = $this->getCommandFromMessage($name);
                if($rname['command']) {
                    return $rname['command'];
                }
                else {
                    if(!empty($name)) {
                        return $name;
                    }
                    return null;
                }
            }
            else {
                return null;
            }
        }
        elseif($this->messenger == "Telegram") {
            if($this->type == "text" || $this->type == "bot_command") {
                if(strpos($data['text'], "@")) {
                    return trim(explode('@', $data['text'])[0], "/");
                }
                return trim($data['text'], "/");
            }
            elseif($this->type == "callback_query") {
                return trim($data['data'], "/");
            }
            elseif($this->type == "channel_post") {
                return trim($data['text'], "/");
            }
            elseif($this->type == "photo") {
                return "photo_sent";
            }
            elseif($this->type == "document") {
                return "document_sent";
            }
            elseif($this->type == "location") {
                return "location";
            }
            elseif($this->type == "contact") {
                return "contact";
            }
            else {
                return null;
            }
        }
    }

    public function getLocation(): ? array {
        if($this->type == "location") {
            return $this->getDataByType();
        }
        else {
            return null;
        }
    }

    public function getParams($assoc = false) {
        return json_decode($this->getInteraction()['params'], $assoc === true ? true : false);
    }

    public function getFilePath($thumb = false) {
        $data = $this->getDataByType();
        if($this->messenger == "Telegram") {
            if(isset($data['photo'][0]['file_id'])) {
                return $this->getBot()->getFilePath($data['photo'][0]['file_id']);
            }
            else {
                if($thumb) {
                    if(isset($data['thumb']['file_id'])) {
                        return $this->getBot()->getFilePath($data['thumb']['file_id']);
                    }
                }
                else {
                    if(isset($data['video']['file_id'])) {
                        dd($this->getBot()->getFilePath($data['video']['file_id']));
                        return $this->getBot()->getFilePath($data['video']['file_id']);
                    }
                }

                return $this->getBot()->getFilePath($data['file_id']);
            }
        }
        elseif($this->messenger == "Viber") {
            if(isset($data['picture'][0]['media'])) {
                return $data['picture'][0]['media'];
            }
            else {
                return $data['data']['media'];
            }
        }
        elseif($this->messenger == "Facebook") {
            return $data['url'];
        }
    }

    public function unknownTeam() {
        if($this->messenger == "Viber") {
            if(substr($this->getBot()->getMessage(), 0, 4) == "http") return;

            $this->send("{unknown_team}", Menu::main());
        }
        elseif($this->messenger == "Facebook") {
            $this->send("{unknown_team}");
        }
        elseif($this->messenger == "Telegram") {
            $this->send("{unknown_team}", Menu::main());
        }
    }

}
