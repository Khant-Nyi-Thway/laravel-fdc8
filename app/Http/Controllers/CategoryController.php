<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::get();

        return view ('admin.pos.categories.index')->with('data',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pos.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_name' => 'required|unique:categories'
            ]
        );

        //dd($validator);

        if($validator->fails()){
            return redirect ('/admin/categories/create')
                ->withErrors($validator)
                ->withInput();
        }



        $inputs = [];
        $inputs['category_name'] = $request['category_name'];
        $inputs['description'] = $request['description'];
        $inputs['created_by'] = auth()->user()->email;
        $inputs['created_at'] = Carbon::now();
        
        Category::insert($inputs);

        session()->flash('message','You have successfully saved the record!');
        return redirect ('admin/categories');


    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $data = Category::where('id',$category->id)->first();

        return view ('admin.pos.categories.show')->with('data',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category = Category::where('id', $category->id)->first();

        return view ('admin.pos.categories.edit')->with('category',$category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_name' => 'required|unique:categories,category_name,' . $category->id
            ]
        );

        if($validator->fails()){
            return redirect ('/admin/categories/' . $category->id .'/edit')
                ->withErrors($validator)
                ->withInputs();
        }

        $inputs = [];
        $inputs['category_name'] = $request['category_name'];
        $inputs['description'] = $request['description'];
        $inputs['created_at'] = Carbon::now();
        $inputs['updated_by'] = auth()->user()->email;
        
        Category::where('id',$category->id)->update($inputs);

        session()->flash('message','You have successfully updated the record!');
        return redirect ('admin/categories');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Category::where('id',$category->id)->delete();
    }
}
