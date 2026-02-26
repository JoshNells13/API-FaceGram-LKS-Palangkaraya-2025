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

        $following = Follow::where(['following_id' => $user->id, 'follower_id' => $request->user()->id])->first();
        $follower = Follow::where(['following_id' => $request->user()->id, 'follower_id' => $user->id])->first();

        if (!$user->is_private ||  $user->id == $request->user()->id || ($user->is_private && $following != null && $following->is_accepted == true)) {
            $posts = $user->posts;

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
                'post_count' => $user->posts->count(),
                'followers_count' => $user->followings->count(),
                'following_count' => $user->followers->count(),
                'posts' => $posts,
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
            'post_count' => $user->posts,
            'followers_count' => $user->followings,
            'following_count' => $user->followers,
        ], 200);
    }
}
