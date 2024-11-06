<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Dokumentasi API DataTech",
 *      description="🚀 Welcome to DataTech API Documentation! 📚 Your comprehensive guide to our powerful data management solutions. Explore our endpoints, features, and seamless integration capabilities to supercharge your applications. Let's build something amazing together! 💡", *      
 * @OA\Contact(
 *          email="rezihandianto98@gmail.com"
 *      ),
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 * 
 */ class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
