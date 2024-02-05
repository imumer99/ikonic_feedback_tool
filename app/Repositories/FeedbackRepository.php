<?php

namespace App\Repositories;

use App\Repositories\Interfaces\FeedbackRepositoryInterface;
use App\Models\Feedback;
use App\Models\Comment;

class FeedbackRepository implements FeedbackRepositoryInterface{
      
    public function all(){

        return Feedback::with('user', 'category' , 'comments.user')->latest()->paginate(8);
    }

    public function create(array $data)
    {
        return Feedback::create($data);
    }

    public function addComment($data)
    {
        $feedback = Feedback::find($data['id']);

        if (!$feedback) {
            throw new \Exception('Feedback not found');
        }

        $comment = Comment::create([
            'feedback_id' => $data['id'],
            'user_id' => $data['user_id'],
            'message' => $data['message'],
        ]); 
        
        return $comment;
    }

    public function findUsersLinkedToFeedback($id)
    {
        $feedback = Feedback::find($id);
        $comments = $feedback->comments;
        $users = [];

        foreach ($comments as $comment) {
            $user = $comment->user;
            $users[] = [
              'id' => $user->id,
              'name' => $user->name,
            ];
        }

        $uniqueUsers = array_map("unserialize", array_unique(array_map("serialize", $users)));
        array_unshift($uniqueUsers, ['id' => 0, 'name' => 'all']);

        return $uniqueUsers;
    }
}