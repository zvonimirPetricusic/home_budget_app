<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Helpers\APIHelpers;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $response = APIHelpers::createAPIResponse(false, 200, '', $categories);
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params = $request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        $category = Category::create([
            'name' => $params['name'],
            'color' => $params['color']
        ]);

        if($category){
            $response = APIHelpers::createAPIResponse(false, 201, 'Category added!', $category);
            return response()->json($response, 200);
        } else{
            $response = APIHelpers::createAPIResponse(true, 400, 'Category could not be added!', null);
            return response()->json($response, 400);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        $response = APIHelpers::createAPIResponse(false, 200, '', $category);
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if($category){
            $update = $category->update($request->all());

            if($update){
                $response = APIHelpers::createAPIResponse(false, 200, 'Category updated!', $update);
                return response()->json($response, 200);
            } else{
                $response = APIHelpers::createAPIResponse(true, 400, 'Category could not be updated!', null);
                return response()->json($response, 400);
            }
        }else{
            $response = APIHelpers::createAPIResponse(true, 404, 'Wrong Id!', null);
            return response()->json($response, 404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if($category){
            $delete = Category::destroy($id);
            if($delete){
                $response = APIHelpers::createAPIResponse(false, 200, 'Category deleted!', $delete);
                return response()->json($response, 200);
            } else{
                $response = APIHelpers::createAPIResponse(true, 400, 'Category could not be deleted!', null);
                return response()->json($response, 400);
            }
        }else{
            $response = APIHelpers::createAPIResponse(true, 404, 'Wrong Id!', null);
            return response()->json($response, 404);
        }

    }

    /**
     * Filter by name
     * 
     * @param str $name
     * @return \Illuminate\Http\Response
     */

     public function filter($name)
     {
        $category = Category::where([['name', 'like', '%' . $name . '%']])->get();
        $response = APIHelpers::createAPIResponse(false, 200, '', $category);
        return response()->json($response, 200);
     }
}
