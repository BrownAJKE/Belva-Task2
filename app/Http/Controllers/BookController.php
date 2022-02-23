<?php
namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Notifications\BookAddedSuccessfully;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allBooks()
    {
        //Laravel API resource with Pagination
        $books =  $this->user->books()->paginate(10);

        //API resource to eager load the relationship and avoid n+1 problem
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addBook(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'isbn', 'author');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'isbn' => 'required',
            'author' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Check if author exists in the database
        $author = $request->input('author');

        if(!Author::where('id', $author)->exists()){
            return response([
                'message' => 'No author found with that id',
                'status' => 404
            ]);
        }

        //Request is valid, create new product
        $book = $this->user->books()->create([
            'name' => $request->name,
            'isbn' => $request->isbn,
            'author_id' => $author
        ]);

        //Send email notification to user
        $book->user->notify(new BookAddedSuccessfully($book));

        //Book created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Book created successfully',
            'data' => $book
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function getBook($id)
    {
        $book = $this->user->books()->find($id);
    
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, book not found.'
            ], 400);
        }
    
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function updateBook(Request $request, Book $book)
    {
        //Validate data
        $data = $request->only('name', 'isbn', 'author');

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'isbn' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }


        //Check if author exists in the database
        $author = $request->input('author');

        if(!Author::where('id', $author)->exists()){
            return response([
                'message' => 'Author with that id not found',
                'status' => 404
            ]);
        }

        
        //Request is valid, update product
        $book = $book->update([
            'name' => $request->name,
            'isbn' => $request->isbn,
            'author_id' => $author
        ]);

        //Book updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Book updated successfully',
            'data' => $book
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $boook
     * @return \Illuminate\Http\Response
     */
    public function destroyBook(Book $book)
    {
        $book->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully'
        ], Response::HTTP_OK);
    }
}