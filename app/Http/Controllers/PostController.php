<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //get all posts
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')->get()
        ],200);
    }

    //get simgle post
    public function show($id)
    {
        return response([
            'posts' => Post::where('id', $id)->withCount('comments', 'likes')->get()
        ],200);
    }

    //create post
    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        $post = Post::create([
           'body' => $attrs['body'],
           'user_id' => auth()->user()->id
        ]);
        return response([
            'message' => 'Post created.',
            'post' => $post
        ], 200);
    }

    //update post
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if(!$post)
        {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }

        if($post->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        $post->update([
           'body' => $attrs['body'],
        ]);
        return response([
            'message' => 'Post update.',
            'post' => $post
        ], 200);
    }

    //delete post

    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Post not found.'
            ], 403);
        }

        if($post->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }
        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted.',
        ], 200);
    }
}
