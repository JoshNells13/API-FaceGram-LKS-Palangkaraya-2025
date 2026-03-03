<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function followUser(Request $request, $username)
    {
        $follow = User::where('username', $username)->first();
        if (!$follow) return response(['message', 'User not found'], 404);
        if ($follow->id == $request->user()->id) return response([
            'message' => 'You Are Not Allowed to follow your self'
        ], 422);

        $checkFollow = Follow::where(['follower_id' => $request->user()->id, 'following_id' => $follow->id])->first();
        if ($checkFollow) return response([
            'message' => 'You are already followed',
            'status' => $checkFollow->is_accepted ? 'following' : 'requested'
        ], 422);

        $following = Follow::create([
            'follower_id' => $request->user()->id,
            'following_id' => $follow->id,
            'is_accepted' => $follow->is_private ? false : true
        ]);

        return response([
            'message' => 'Follow success',
            'status' => $following->is_accepted ? 'following' : 'requested'
        ], 200);
    }

    public function unfollowUser(Request $request, $username)
    {
        $follow = User::where('username', $username)->first();
        if (!$follow) return response(['message', 'User not found'], 404);

        $checkFollow = Follow::where(['follower_id' => $request->user()->id, 'following_id' => $follow->id])->first();
        if (!$checkFollow) return response(['message', 'You are not following this user'], 422);

        $checkFollow->delete();

        return response(['message' => "Unfollow {$follow->username} success"], 200);
    }

    public function getFollowing(Request $request, $username)
    {

        $CheckFollowingUser = User::where('username',$username)->first();

        if(!$CheckFollowingUser){
            return response([
                'message' => 'user not found'
            ],404);
        }

        $following = Follow::where('follower_id', $CheckFollowingUser->id)->get()->pluck('following');

        return response([
            'following' => $following
        ], 200);
    }

    public function getFollower(Request $request, $username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response([
                'message' => 'user not found'
            ], 404);
        }

        $follower = Follow::where('following_id', $user->id)->get()->load('follower');

        return response([
            'followers' => FollowerResource::collection($follower)
        ], 200);
    }

    public function acceptFollow(Request $request, $username)
    {
        $follow = User::where('username', $username)->first();
        if (!$follow) return response(['message', 'User not found'], 404);

        $checkFollow = Follow::where(['follower_id' => $follow->id, 'following_id' => $request->user()->id])->first();
        if (!$checkFollow) return response(['message' => 'The user is not following you'], 422);
        if ($checkFollow->is_accepted) return response(['message' => 'Follow request is already accepted'], 422);

        $checkFollow->update(['is_accepted' => true]);
        return response([
            'message' => 'Follow request accepted'
        ], 200);
    }
}
