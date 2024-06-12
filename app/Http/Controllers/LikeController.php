<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    //
    public function likeOrunlike($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }
        $like = $post->likes()->where('user_id',auth()->user()->id)->first();

        if(!$like)
        {
            Like::create([
                'post_id' => $id,
                'user_id'=>auth()->user()->id
            ]);

            return response([
                'message' => 'Liked'
            ],200);
        }

        //if not liked then like

        if(!$like)
        {
            $like->delete();

            return response([
                'message' => 'Disliked'
            ],200);
        }
    }

}
