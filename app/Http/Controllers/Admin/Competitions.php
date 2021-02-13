<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddToGroup;
use App\Models\BestPhoto;
use App\Models\BestVideo;
use App\Models\Groups;
use App\Models\Invited;
use App\Models\Language;
use App\Models\Others;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Competitions extends Controller{
    public function groupInvitations(Request $request) {
        $group = $request->get('group') ?? null;
        $request = $request->post();
        $addToGroup = AddToGroup::with('groups')->where('languages_id', ($request['language'] ?? null))
            ->where('active', '1')->get()->first();

        if($request['language'] ?? null) {
            $groups = $addToGroup->groups ?? null;
            if($group) {
                $groupId = $request['group'];
            }

            if($addToGroup != null) {
                $res = $this->countReferrals($groupId ?? $addToGroup->groups->first()->id) ?? [];
            }

        }

        return view('admin.competitions.group-invitations', [
            'languages' => Language::all(),
            'lang' => $request['language'] ?? null,
            'competition' => $addToGroup,
            'res' => $res ?? [],
            'groups' => $groups ?? [],
            'groupId' => $groupId ?? null,
            'menuItem' => 'groupinvitations'
        ]);
    }

    public function groupInvitationsSave(Request $request) {
        $request = $request->post();

        if(empty($request['description']) || empty($request['group_id']) || empty($request['group_link'])) {
            return redirect()->to(route('group-invitations', $request['language']));
        }

        DB::beginTransaction();
        try {
            if(isset($request['id']) && $request['id'] != null) {
                $addToGroup = AddToGroup::find($request['id']);
            }
            else {
                $addToGroup = new AddToGroup();
                $addToGroup->languages_id = $request['language'];
                $addToGroup->date = date('Y-m-d');
                $addToGroup->time = date('H:i:s');
            }
            $addToGroup->description = $request['description'];
            $addToGroup->save();

            foreach($request['group_id'] as $key => $id) {
                $group = new Groups();
                $group->group_id = $id;
                $group->group_link = $request['group_link'][$key];
                $group->add_to_group_id = $addToGroup->id;
                $group->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return redirect()->to(route('group-invitations').'?language='.$addToGroup->languages_id);
    }

    public function groupInvitationsComplete(Request $request) {
        $addToGroup = AddToGroup::find($request->post('id'));
        $addToGroup->active = 0;
        $addToGroup->save();

        return redirect()->to(route('group-invitations').'?language='.$addToGroup->languages_id);
    }

    public function groupInvitationsArchive(Request $request) {
        return view('admin.competitions.group-invitations-archive', [
            'competitions' => AddToGroup::with('groups')->where('active', 0)
                ->where('languages_id', $request->post('language'))
                ->orderBy('id', 'DESC')
                ->get(),
            'language' => Language::find($request->post('language')),
            'menuItem' => 'groupinvitations'
        ]);
    }

    public function groupInvitationsArchiveDetails(Request $request) {
        $addToGroup = AddToGroup::find($request->post('id'));
        if($request->post('group')) {
            $groupId = $request->post('group');
            $group_id = Groups::find($groupId)->group_id;
        }
        else {
            $group = $addToGroup->groups->first();
            $groupId = $group->id;
            $group_id = $group->group_id;
        }
        return view('admin.competitions.group-invitations-archive-details', [
            'menuItem' => 'groupinvitations',
            'competition' => $addToGroup,
            'language' => $addToGroup->language,
            'groupId' => $groupId,
            'group_id' => $group_id,
            'res' => $this->countReferrals($groupId)
        ]);
    }

    public function groupInvitationsArchiveDelete(Request $request) {
        $AddToGroup = AddToGroup::find($request->post('id'));
        $AddToGroup->delete();
        $lang = $AddToGroup->languages_id;
        return redirect()->to(route('group-invitations-archive', ['language' => $lang]));
    }

    private function countReferrals($groupId, $count = 10) {
        return DB::select("
            SELECT
                u.id AS id,
                u.username,
                u.chat,
                COUNT(i.referral) AS countRef1,
                SUM(ref2.count) AS countRef2
            FROM invited i
            LEFT JOIN (
                SELECT
                    i2.referrer AS referral,
                    COUNT(i2.referral) AS count
                FROM invited i1
                LEFT JOIN invited i2 ON i1.referral = i2.referrer
                WHERE i1.groups_id = '".$groupId."'
                AND i2.groups_id = '".$groupId."'
                GROUP BY i2.referrer
            ) ref2 ON i.referral = ref2.referral
            JOIN users u on i.referrer = u.chat
            WHERE i.groups_id = '".$groupId."'
            GROUP BY i.referrer
            ORDER BY countRef1 DESC, countRef2 DESC
            LIMIT ".$count."
        ");
    }

    public function bestVideos(Request $request, $language = null) {
        $request = $request->post();

        $competition = BestVideo::where('languages_id', ($request['language'] ?? $language))
            ->where('active', '1')
            ->first();

        if($competition) {
            $res = $this->countLikesVideos($competition->id ?? null);
        }

        return view('admin.competitions.best-videos', [
            'lang' => ($request['language'] ?? $language),
            'competition' => $competition,
            'languages' => Language::all(),
            'res' => $res ?? null,
            'menuItem' => 'bestvideos'
        ]);
    }

    public function bestVideosSave(Request $request) {
        if(empty($request->post('description')) || empty($request->post('channel_id'))) {
            return redirect()->to(route('best-videos')."?language=".$request->post('languages_id'));
        }


        $bestVideo = new BestVideo();
        $bestVideo->description = $request->post('description');
        $bestVideo->languages_id = $request->post('languages_id');
        $bestVideo->channel_id = $request->post('channel_id');
        $bestVideo->channel_name = $request->post('channel_name');
        $bestVideo->save();

        return redirect()->to(route('best-videos')."?language=".$request->post('languages_id'));
    }

    public function bestVideosComplete(Request $request) {
        BestVideo::where('id', $request->post('best_videos_id'))->update(['active' => '0']);
        return redirect()->to(route('best-videos')."?language=".$request->post('languages_id'));
    }

    public function bestVideosArchive(Request $request) {
        return view('admin.competitions.best-videos-archive', [
            'competitions' => BestVideo::where('active', '0')
                ->where('languages_id', $request->post('language'))
                ->orderBy('id', 'DESC')
                ->get(),
            'language' => Language::find($request->post('language')),
            'menuItem' => 'bestvideos'
        ]);
    }

    public function bestVideosArchiveDelete(Request $request) {
        BestVideo::where('id', $request->post('id'))
            ->where('active', '0')
            ->delete();
        return redirect()->to(route('best-videos-archive')."?language=".$request->post('language'));
    }

    public function bestVideosArchiveDetails(Request $request) {
        $competition = BestVideo::find($request->post('id'));
        return view('admin.competitions.best-videos-archive-details', [
            'menuItem' => 'bestvideos',
            'competition' => $competition,
            'language' => Language::find($competition->languages_id),
            'res' => $this->countLikesVideos($competition->id)
        ]);
    }

    public function countLikesVideos($bestVideosId) {
        return DB::select("
            SELECT pv.users_id AS userId,
                   u.username,
                   CONCAT('https://t.me/', bv.channel_name,'/', pv.post_id) AS post,
                   COUNT(lv.user_chat) AS count
            FROM posts_videos pv
            JOIN likes_videos lv on pv.id = lv.post_videos_id
            JOIN users u on lv.user_chat = u.chat
            JOIN best_videos bv on pv.best_videos_id = bv.id
            WHERE pv.best_videos_id = '".$bestVideosId."'
            GROUP BY pv.id
            ORDER BY count DESC"
        );
    }

    public function bestPhotos(Request $request, $language = null) {
        $request = $request->post();

        $competition = BestPhoto::where('languages_id', ($request['language'] ?? $language))
            ->where('active', '1')
            ->first();

        if($competition) {
            $res = $this->countLikesPhotos($competition->id ?? null);
        }

        return view('admin.competitions.best-photos', [
            'lang' => ($request['language'] ?? $language),
            'competition' => $competition,
            'languages' => Language::all(),
            'res' => $res ?? null,
            'menuItem' => 'bestphotos'
        ]);
    }

    public function bestPhotosSave(Request $request) {
        if(empty($request->post('description')) || empty($request->post('channel_id'))) {
            return redirect()->to(route('best-photos')."?language=".$request->post('languages_id'));
        }


        $bestPhoto = new BestPhoto();
        $bestPhoto->description = $request->post('description');
        $bestPhoto->languages_id = $request->post('languages_id');
        $bestPhoto->channel_id = $request->post('channel_id');
        $bestPhoto->channel_name = $request->post('channel_name');
        $bestPhoto->save();

        return redirect()->to(route('best-photos')."?language=".$request->post('languages_id'));
    }

    public function bestPhotosComplete(Request $request) {
        BestPhoto::where('id', $request->post('best_photos_id'))->update(['active' => '0']);
        return redirect()->to(route('best-photos')."?language=".$request->post('languages_id'));
    }

    public function bestPhotosArchive(Request $request) {
        return view('admin.competitions.best-photos-archive', [
            'competitions' => BestPhoto::where('active', '0')
                ->where('languages_id', $request->post('language'))
                ->orderBy('id', 'DESC')
                ->get(),
            'language' => Language::find($request->post('language')),
            'menuItem' => 'bestphotos'
        ]);
    }

    public function bestPhotosArchiveDelete(Request $request) {
        BestPhoto::where('id', $request->post('id'))
            ->where('active', '0')
            ->delete();
        return redirect()->to(route('best-photos-archive')."?language=".$request->post('language'));
    }

    public function bestPhotosArchiveDetails(Request $request) {
        $competition = BestPhoto::find($request->post('id'));
        return view('admin.competitions.best-photos-archive-details', [
            'menuItem' => 'bestphotos',
            'competition' => $competition,
            'language' => Language::find($competition->languages_id),
            'res' => $this->countLikesPhotos($competition->id)
        ]);
    }

    public function countLikesPhotos($bestPhotosId) {
        return DB::select("
            SELECT pp.users_id AS userId,
                   u.username,
                   CONCAT('https://t.me/', bp.channel_name,'/', pp.post_id) AS post,
                   COUNT(lp.user_chat) AS count
            FROM posts_photos pp
            JOIN likes_photos lp on pp.id = lp.posts_photos_id
            JOIN users u on lp.user_chat = u.chat
            JOIN best_photos bp on pp.best_photos_id = bp.id
            WHERE pp.best_photos_id = '".$bestPhotosId."'
            GROUP BY pp.id
            ORDER BY count DESC"
        );
    }

    public function others(Request $request) {


        return view('admin.competitions.others', [
            'menuItem' => 'others',
            'competition' => Others::where('languages_id', ($request->post('language') ?? null))->first(),
            'languages' => Language::all(),
            'lang' => $request->post('language') ?? null
        ]);
    }

    public function othersSave(Request $request) {
        if(empty($request->post('description'))) {
            return redirect()->to(route('others')."?language=".$request->post('language'));
        }

        if($request->post('id') != null) {
            $others = Others::find($request->post('id'));
        }
        else {
            $others = new Others();
            $others->languages_id = $request->post('language');
        }

        $others->description = $request->post('description');
        $others->date = date('d.m.Y');
        $others->time = date('H:i:s');
        $others->save();
        return redirect()->to(route('others')."?language=".$request->post('language'));
    }

    public function othersDelete(Request $request) {
        Others::where('id', $request->post('id'))->delete();
        return redirect()->to(route('others')."?language=".$request->post('language'));
    }

}
