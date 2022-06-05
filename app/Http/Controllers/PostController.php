<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class PostController extends Controller
{
    public function index()
    {
        //get posts
        $posts = Post::latest()->paginate(5);

        //render view with posts
        return view('posts.index', compact('posts'));
    }
    public function create()
    {
        return view('posts.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            
            'title'     => 'required|min:5',
            'content'   => 'required|min:10',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id'   => 'required',
            'category_id'=> 'required' ,
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        Post::create([
            
            'title'     => $request->title,
            'content'   => $request->content,
            'image'     => $image->hashName(),
            'user_id'   => $request->user_id,
            'category_id'=> $request->category_id
            
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Post $post)
    {
        //validate form
        $this->validate($request, [
            'title'     => 'required|min:5',
            'content'   => 'required|min:10',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id'   => 'required',
            'category_id'=> 'required' ,
        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post with new image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
                'image'     => $image->hashName(),
                'user_id'   => $request->user_id,
                'category_id'=> $request->category_id
            ]);

        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
                'user_id'   => $request->user_id,
                'category_id'=> $request->category_id
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }
    public function destroy(Post $post)
    {
        //delete image
        Storage::delete('public/posts/'. $post->image);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

}
