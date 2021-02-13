<?php

namespace App\Models\buttons;

use App\Models\buttons\extend\AbstractButtonsTelegram;

class ButtonsTelegram extends AbstractButtonsTelegram {

    public function competitions($comp) {
        if(is_array($comp[0] ?? null)) {
            $buttons = [];
            foreach($comp[0] as $key => $c) {
                if($c) {
                    $buttons[] = ['{'.$key.'}'];
                }
            }
            $buttons[] = ['{back}'];
            return $buttons;
        }
        else {
            return [];
        }
    }
}
