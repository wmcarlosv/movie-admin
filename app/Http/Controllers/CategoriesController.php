<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Session;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Category::all();
        $title = 'Manage Categories';
        return view('admin.categories.index',['title' => $title, 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'New Category';
        $type = 'new';

        return view('admin.categories.form',['title' => $title, 'type' => $type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = new Category();
        $category->name = $request->input('name');

        if($request->input('is_for_channel')){
            $category->is_for_channel = 'Y';
        }else{
            $category->is_for_channel = 'N';
        }

        if($category->save()){
            Session::flash('success','Record Inserted Successfully!!');
        }else{
            Session::flash('error','Error Inserting Record!!');
        }

        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Category::findorfail($id);
        $title = 'Edit Category';
        $type = 'edit';
        return view('admin.categories.form', ['title' => $title, 'data' => $data, 'type' => $type]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = Category::findorfail($id);
        $category->name = $request->input('name');
        if($request->input('is_for_channel')){
            $category->is_for_channel = 'Y';
        }else{
            $category->is_for_channel = 'N';
        }

        if($category->update()){
            Session::flash('success','Record Updated Successfully!!');
        }else{
            Session::flash('error','Error Updating Record!!');
        }

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findorfail($id); 

        if($category->delete()){
            Session::flash('success','Record Deleted Successfully!!');
        }else{
            Session::flash('error', 'Error Deleting Record!!');
        }

        return redirect()->route('categories.index');
    }
}
