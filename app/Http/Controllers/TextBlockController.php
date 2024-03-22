<?php

namespace App\Http\Controllers;

use App\Http\Resources\TextBlockResource;
use App\Models\TextBlock;
use Illuminate\Http\Request;

class TextBlockController extends Controller
{
    public function getAll(Request $request)
    {
        $query = TextBlock::query()->orderBy("text", "ASC");
        return TextBlockResource::collection($query->paginate($request->pagination["per_page"], ['*'], 'page', $request->pagination["current_page"]));
    }
}
