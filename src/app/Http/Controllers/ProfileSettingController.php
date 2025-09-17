<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileSettingController extends Controller
{
    public function profileSetting()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        return view('profile-setting', compact('user', 'profile'));
    }

    public function profileUpdate(ProfileRequest $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        // profilesテーブルの更新
        $profile = Profile::firstOrNew(['user_id' => $user->id]);
        if ($request->hasFile('profile_image')) {
            $imageFile = $request->file('profile_image');
            $imageName = uniqid() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/profiles', $imageName);
            $profile->profile_image = 'storage/profiles/' . $imageName;
        }
        $profile->postal_code = $request->input('postal_code');
        $profile->address = $request->input('address');
        $profile->building_name = $request->input('building_name');
        $profile->save();

        return redirect('/');
    }
}
