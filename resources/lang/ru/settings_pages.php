<?php

use App\Models\SettingsPages;

$settingsPages = SettingsPages::all();
$data = [];
foreach($settingsPages as $sp) {
    $data[$sp->name] = $sp->description;
}

return $data;
