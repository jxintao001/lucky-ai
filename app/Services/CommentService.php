<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Comment;
use Auth;

class CommentService
{

    public function add($list)
    {
        foreach ($list as $item) {
            $comment = new Comment();
            $comment->user_id = Auth::user()->id;
            $comment->order_id = $item->order_id;
            $comment->product_id = $item->product_id;
            $comment->rating = $item->rating;
            $comment->review = $item->review;
            $comment->review_at = now();
            $comment->save();
        }
    }


}
