<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with('commentable')->latest();

        if ($request->get('status') === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->get('status') === 'approved') {
            $query->where('is_approved', true);
        }

        $comments = $query->paginate(15)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        AuditLogService::log('comment_approved', "Comment #{$comment->id} approved", $comment);

        return back()->with('success', 'Comment approved.');
    }

    public function destroy(Comment $comment)
    {
        $id = $comment->id;
        $comment->delete();

        AuditLogService::log('comment_deleted', "Comment #{$id} deleted");

        return back()->with('success', 'Comment deleted.');
    }
}
