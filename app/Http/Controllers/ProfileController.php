<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Services\PublicImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly PublicImageStorage $images) {}

    public function edit(Request $request): View
    {
        return view('profile.edit', ['profile' => $request->user()->profile()->firstOrCreate()]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $profile = $request->user()->profile()->firstOrCreate();
        $oldAvatar = $profile->avatar;
        $removeAvatar = $request->boolean('remove_avatar');
        $newAvatar = $this->images->storeSafely($request->file('avatar'), 'avatars', function (?string $avatar) use ($profile, $validated, $removeAvatar): ?string {
            if ($avatar) {
                $profile->avatar = $avatar;
            } elseif ($removeAvatar) {
                $profile->avatar = null;
            }
            $profile->bio = $validated['bio'] ?? null;
            DB::transaction(fn () => $profile->save());

            return $avatar;
        });
        if ($newAvatar || $removeAvatar) {
            $this->images->delete($oldAvatar);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
