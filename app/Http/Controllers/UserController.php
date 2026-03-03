<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $followers = Follow::select('following_id')->where('follower_id', $request->user()->id)->pluck('following_id')->toArray();
        array_push($followers, $request->user()->id);

        $notFollowers = User::whereNotIn('id', $followers)->get();

        return response([
            'users' => $notFollowers
        ], 200);
    }

    public function show(Request $request, $username)
    {
        $user = User::where('username', $username)
            ->with(['posts.Attachment'])
            ->withCount(['posts', 'followers', 'followings'])
            ->first();
        if (!$user) return response(['message' => 'User not found'], 404);

        $following = Follow::where([
            'follower_id' => auth()->id(),
            'following_id' => $user->id
        ])->first();

        $follower = Follow::where([
            'follower_id' => $user->id,
            'following_id' => auth()->id()
        ])->first();

        $isOwner = $user->id === auth()->id();
        $isPublic = !$user->is_private;
        $isApprovedFollower = $following?->is_accepted == 1;

        if ($isPublic || $isOwner || $isApprovedFollower) {
            return response([
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'bio' => $user->bio,
                'is_private' => $user->is_private,
                'created_at' => $user->created_at,
                'is_your_account' => $isOwner,
                'following_status' => !$following
                    ? 'not-following'
                    : ($following->is_accepted ? 'following' : 'requested'),
                'follower_status' => !$follower
                    ? 'not-follower'
                    : ($follower->is_accepted ? 'follower' : 'requested'),
                'post_count' => $user->posts_count,
                'followers_count' => $user->followers_count,
                'following_count' => $user->followings_count,
                'posts' => $user->posts,
            ], 200);
        }

        return response([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'username' => $user->username,
            'bio' => $user->bio,
            'is_private' => $user->is_private,
            'created_at' => $user->created_at,
            'is_your_account' => $user->id == $request->user()->id,
            'following_status' => !$following ? 'not-following' : ($following->is_accepted ? 'following' : 'requested'),
            'follower_status' => !$follower ? 'not-follower' : ($follower->is_accepted ? 'follower' : 'requested'),
            'post_count' => $user->posts_count,
            'followers_count' => $user->followers_count,
            'following_count' => $user->followings_count,
        ], 200);
    }
}
