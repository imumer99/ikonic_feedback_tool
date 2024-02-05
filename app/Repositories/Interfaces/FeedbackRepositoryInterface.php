<?php

namespace App\Repositories\Interfaces;

Interface FeedbackRepositoryInterface{

    public function all();

    public function create(array $data);

    public function addComment(array $data);

    public function findUsersLinkedToFeedback($id);
}