<?php

namespace App\Models\API;

class FacebookMessenger {
		protected $token;
		private $request;

		public function __construct(string $token = null) {
            $this->request = json_decode(file_get_contents('php://input'));
            if ($token === null) {
                $this->token = defined(MESSENGER_TOKEN) ? MESSENGER_TOKEN : null;
            } else {
                $this->token = $token;
            }
		}

        function getId(): ? string {
            return $this->request->entry[0]->messaging[0]->sender->id;
        }

		public function getWebhookUpdates(): array {
			$res = json_decode(file_get_contents('php://input'), true);

			return [
				'id' => $res['entry'][0]['id'],
				'time' => $res['entry'][0]['time'],
				'senderId' => $res['entry'][0]['messaging'][0]['sender']['id'],
				'recipientId' => $res['entry'][0]['messaging'][0]['recipient']['id'],
				'timestamp' => $res['entry'][0]['messaging'][0]['timestamp'],
				'payload' => $res['entry'][0]['messaging'][0]['message']['quick_reply']['payload'],
				'mid' => $res['entry'][0]['messaging'][0]['message']['mid'],
				'seq' => $res['entry'][0]['messaging'][0]['message']['seq'],
				'text' => $res['entry'][0]['messaging'][0]['message']['text'],
				'payloadStart' => $res['entry'][0]['messaging'][0]['postback']['payload'],
				'titleStart' => $res['entry'][0]['messaging'][0]['postback']['title']
			];
		}

        public function getRef() {
		    return $this->request->entry[0]->messaging[0]->postback->referral->ref ?? null;
		}

		public function userInfo($chat): string {
            $url = "https://graph.facebook.com/" . $chat . "?fields=first_name,last_name,profile_pic,locale&access_token=" . $this->token;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $json = curl_exec($ch);
            curl_close($ch);

            return $json;
		}

        public function getName($chat) {
            $obj = json_decode($this->userInfo($chat));
            return [
                'chat' => $obj->id,
                'first_name' => $obj->first_name,
                'last_name' => $obj->last_name,
                'username' => $obj->first_name." ".$obj->last_name
            ];
        }

		public function sendMessage(? string $chat, string $message, ? array $params = []): string {
			/*//Быстрые ответы
			$keyboard = [
				[
					"content_type" => "text",
					"title" => "Да",
					"payload" => "yes"
				],
				[
					"content_type" => "text",
					"title" => "Нет",
					"payload" => "no"
				]
			];
			*/

            if (!empty($params['keyboard'])) {
                $keyboard = $params['keyboard'];
            } else {
                $keyboard = null;
            }

			return $this->makeRequest([
                'recipient' => [
                    'id' => $chat
                ],
                'message' => [
                    'text' => $message,
                    'quick_replies' => $keyboard
                ]
            ]);
		}

		public function sendImage($id, $img_url) {
			return $this->makeRequest([
                'recipient' => [
                    'id' => $id
                ],
                'message' => [
                    'attachment' => [
                        'type' => 'image',
                        'payload' => [
                            'url' => $img_url
                        ]
                    ]
                ]
            ]);
		}

		public function sendButton($id, $text, $buttons) {
			/*
			//URL кнопки
			$buttons = [
				array(
				  'type' => 'web_url',
				  'url' => $url,
				  'title' => 'Текст кнопки',
				)
			];

			//Кнопки
			$buttons = [
				array(
					'type' => 'postback',
					'title' => 'Текст кнопки',
					'payload' => 'Это придет на Webhook'
				),
				array(
					'type' => 'postback',
					'title' => 'Текст кнопки',
					'payload' => 'Это придет на Webhook'
				)
			];
			*/

			return $this->makeRequest([
                'recipient' => [
                    'id' => $id
                ],
                'message' => [
                    'attachment' => [
                        'type' => 'template',
                        'payload' => [
                            'template_type' => 'button',
                            'text' => $text,
                            'buttons' => $buttons
                        ]
                    ]
                ]
            ]);
		}

		public function sendTemplateGeneric($id, $title, $img_url, $subtitle, $buttons) {
			return $this->makeRequest([
                'recipient' => [
                    'id' => $id
                ],
                'message' => [
                    'attachment' => [
                        'type' => 'template',
                        'payload' => [
                            'template_type' => 'generic',
                            'image_aspect_ratio' => 'square',
                            'elements' => [
                                [
                                    'title' => $title,
                                    'image_url' => $img_url,
                                    'subtitle' => $subtitle,
                                    'buttons' => $buttons
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
		}

        public function sendFile($chat, $url) {
            return $this->makeRequest([
                'recipient' => [
                    'id' => $chat
                ],
                'message' => [
                    'attachment' => [
                        'type' => 'file',
                        'payload' => [
                            'url' => $url,
                            'is_reusable' => 'true'
                        ]
                    ]
                ]
            ]);
        }

		public function senderAction($id, $action) {
			//$action = 'typing_off';
			return $this->makeRequest([
                'recipient' => [
                    'id' => $id
                ],
                'sender_action' => $action
            ]);
		}

		private function makeRequest($data) {
			$data_string = json_encode($data);

			$ch = curl_init("https://graph.facebook.com/v3.2/me/messages?access_token=".$this->token);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
                ]
			);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}

		public function setWebhook(): ? string {
			$verify_token = "EAAGLEx4zcr4BABGQRuizFmSmxdEZCKcLZBcuuKE";
            if (!empty($_REQUEST['hub_mode']) &&
                $_REQUEST['hub_mode'] == 'subscribe' &&
                $_REQUEST['hub_verify_token'] == $verify_token) {
                return $_REQUEST['hub_challenge'];
            }
            return null;
		}

		public function getStarted() {
			$data_string = json_encode([
                'get_started' => [
                    'payload' => 'start'
                ]
            ]);

			$url = "https://graph.facebook.com/v3.2/me/messenger_profile?access_token=".$this->token;

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}

//addMenu
		public function addMenu() {
				$data = [
				'persistent_menu' => [
					[
						'locale' => 'default',
						'composer_input_disabled' => false,
						'call_to_actions' =>
						[
							[
								'title' => 'Книги 📖',
								'type' => 'nested',
								'call_to_actions' => [
									[
										'title' => '🔍 Поиск книги 📖',
										'type' => 'postback',
										'payload' => 'search_book'
									],
									[
										'title' => '📚 Добавить книгу',
										'type' => 'postback',
										'payload' => 'add_book'
									]
								]
							],
							[
								'title' => '🔐 Доступ',
								'type' => 'nested',
								'call_to_actions' => [
									[
										'title' => '💰 Платный доступ',
										'type' => 'postback',
										'payload' => 'paid_access'
									],
									[
										'title' => '🆓 Бесплатный доступ',
										'type' => 'postback',
										'payload' => 'free_access'
									]
								]
							],
                            [
                                'title' => '💬 Поддержка',
                                'type' => 'nested',
                                'call_to_actions' => [
                                    [
                                        'title' => '📈 Статистика',
                                        'type' => 'postback',
                                        'payload' => 'statistics'
                                    ],
                                    [
                                        'title' => '✉ Контакты',
                                        'type' => 'postback',
                                        'payload' => 'contacts'
                                    ],
                                    [
                                        'title' => '💬 Группа',
                                        'type' => 'web_url',
                                        'url' => 'https://all.vitovtov.info/admin'
                                    ]
                                ]
                            ]
						]
					]
				]
			];

			$url = "https://graph.facebook.com/v3.2/me/messenger_profile?access_token=".$this->token;

			$data_string = json_encode($data);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}

//greeting
		public function greeting($text) {
			$data = [
				'greeting' => [
					[
						'locale' => 'default',
						'text' => $text
					]
				]
			];

			$url = "https://graph.facebook.com/v3.2/me/messenger_profile?access_token=".$this->token;

			$data_string = json_encode($data);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		}

    public function getRequest(): ?string {
        return json_encode($this->request);
    }

    public function getMessage(): ?string {
        $request = $this->request;
        if(isset($request->entry[0]->messaging[0]->message->quick_reply->payload)) {
            return $request->entry[0]->messaging[0]->message->quick_reply->payload;
        }
        else if(isset($request->entry[0]->messaging[0]->message->text)) {
            return $request->entry[0]->messaging[0]->message->text;
        }
        else {
            return null;
        }
    }
}
?>
