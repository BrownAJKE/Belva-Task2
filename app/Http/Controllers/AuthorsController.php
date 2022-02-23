<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class AuthorsController extends Controller
{
    public function allAuthors()
    {
        //Get all the books as a collection using API resource
        return Author::all();
    }

    public function addAuthor(AuthorRequest $request)
    {
        //Validation using AuthorRequest Class
        $author = Author::create([
            'name' => $request->input('name')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Author created successfully',
            'data' => $author
        ], Response::HTTP_OK);
    }

    public function getAuthor($id)
    {
        //find the author details in the database with passed id
        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Author not found.'
            ], 400);
        }
    
        return $author;
    }

    public function updateAuthor(AuthorRequest $request, Author $author)
    {
        //Validation with request class then update 
        $author->update([
            'name' => $request->input('name')
        ]);

        //Author updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Author updated successfully',
            'data' => $author
        ], Response::HTTP_OK);
    }

    public function deleteAuthor(Author $author)
    {
        //Route-model binding to get the author
        $author->delete();

        return response([
            'message' => 'Author deleted successfully',
            'status' => 200
        ]);
    }

}
