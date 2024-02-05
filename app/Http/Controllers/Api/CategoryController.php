<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        try {

            $categories = $this->categoryRepository->all();
            return response()->json($categories);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
