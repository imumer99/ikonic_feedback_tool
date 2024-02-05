<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\FeedbackRepositoryInterface;

class FeedbackController extends Controller
{
    private $feedbackRepository;

    public function __construct(FeedbackRepositoryInterface $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    public function index()
    {
        try {

            $feedback = $this->feedbackRepository->all();
            return response()->json($feedback);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function store(FeedbackRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = auth()->id();

            $feedback = $this->feedbackRepository->create($validatedData);
            return response()->json($feedback);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()],422);
        }
    }

    public function addComment(Request $request, $id)
    {
        try {
            $data['message'] = $request->content;
            $data['id'] = $id;
            $data['user_id'] = auth()->id();

            $comment = $this->feedbackRepository->addComment($data);
            return response()->json(['status' => true, 'comment' => $comment]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()],422);
        }
    }

    public function findUsersLinkedToFeedback($id)
    {
        try {
            $user_names =  $this->feedbackRepository->findUsersLinkedToFeedback($id);
            return response()->json(['user_names' => $user_names]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()],422);
        }
    }
}
