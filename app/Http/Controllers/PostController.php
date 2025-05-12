<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //
    public function createPost(Request $request)
    {
        // Menentukan Header untuk memasukkan Gambar.
        $request->header('Content-Type', 'multipart/form-data');

        // Melakukan Validasi
        $request->validate([
            'caption' => 'required',
            'attachments' => 'required|array',
            'attachments.*' => 'file|mimes:png,jpg,webp,gif,jpeg'
        ]);

        // Melakukan Pembuatan Data Postingan
        $post = Post::create([
            'caption' => $request->caption,
            'user_id' => $request->user()->id,
            'created_at' => date('Y-m-d, Hi:i:s')
        ]);
        // Setiap Ingin Memasukkan Gambar kita harus melakukan perulangan
        foreach ($request->attachments as $attach) {
            // menentukan direktori tempat dimana gambar disimpan
            $file = $attach->storeAs('posts', $attach->getClientOriginalName(), 'public');
            // melakukan penyimpanan data gambar
            Attachment::create([
                'storage_path' => $file,
                'post_id' => $post->id  
            ]);
        }

        // mengembalikan respon
        return response([
            'message' => 'create post succes',
            'Post' => $post,
        ], 200);
    }

    public function deletePost(Request $request, $postId)
    {
        // mengambil data postingan berdasarkan id
        $post = Post::find($postId);

        // jika tidak ada postingan akan mengembalikan respon
        if (!$post) {
            return response([
                'message' => 'post not found',
            ]);
        }
      
        if ($post->user_id != $request->user()->id) {
            return response([
                'message' => 'forbidden acces'
            ]);
        }
 
        $post->delete();


        // mengembalikan respon
        return response([
            'message' => 'Berhasil Menghapus Postingan'
        ], 204);
    }

    public function getPost(Request $request)
    {
        $request->validate([
            'page' => 'numeric|min:0',
            'size' => 'numeric|min:0|max:10'
        ]);

        $size = $request->input('size', 10);

        $posts = Post::with(['user', 'attachment']) // Load relasi dengan benar
                     ->orderBy('created_at', 'DESC')
                     ->paginate($size)
                     ->appends(request()->query());

        return response()->json([
            'pages' => $posts->currentPage(),
            'size' => $posts->perPage(),
            'data' => $posts
        ]);
    }
}