<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Channels extends Controller {
    public function index() {
        return view('admin.channels.index', [
            'channels' => \App\Models\Channels::all(),
            'menuItem' => 'channelsforlanguages'
        ]);
    }

    public function save(Request $request) {
        $request = $request->post();
        foreach($request['channelsId'] as $id => $channel) {
            \App\Models\Channels::where('id', $id)
                ->update([
                    'channel_id' => $channel,
                    'link' => $request['channelsLink'][$id]]
                );
        }

        return redirect()->to(route('channels-for-languages'));
    }
}
