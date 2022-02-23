<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\BookController;

Route::post('login', [AuthController::class, 'authenticate']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    //Logout and User Profile
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'get_user']);

    //Books CRUD
    Route::get('books', [BookController::class, 'allBooks']);
    Route::get('book/{id}', [BookController::class, 'getBook']);
    Route::post('book', [BookController::class, 'addBook']);
    Route::put('book/{book}',  [BookController::class, 'updateBook']);
    Route::delete('book/{book}',  [BookController::class, 'destroyBook']);

    //Authors CRUD
    Route::get('authors', [AuthorsController::class, 'allAuthors']);
    Route::get('author/{id}', [AuthorsController::class, 'getAuthor']);
    Route::post('author', [AuthorsController::class, 'addAuthor']);
    Route::put('author/{author}',  [AuthorsController::class, 'updateAuthor']);
    Route::delete('author/{author}',  [AuthorsController::class, 'destroyBook']);
});