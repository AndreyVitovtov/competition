<?php

use App\Models\SettingsButtons;

$settingsButtons = SettingsButtons::all();
$data = [];
foreach($settingsButtons as $sb) {
    $data[$sb->name] = $sb->menu_us;
}

return $data;
