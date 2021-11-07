<?php

namespace App\Http\Controllers;

use App\Models\CMSPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CMSPageController extends Controller
{
    //
    public function index(Request $request)
    {
        $cms = CMSPage::OrderBy('id','DESC')->paginate(15);
        return Inertia::render('pages/list', ['cms' => $cms]);
    }

    public function edit($id){
        $cms = CMSPage::find($id);
        return Inertia::render('pages/edit', ['cms' => $cms]);
    }

    public function update(Request $request){

        $validate = $request->validate([
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keywords' => 'required'
        ]);

        $cms = CMSPage::find($request->id);
        $cms->meta_title = $request->meta_title;
        $cms->meta_description = $request->meta_description;
        $cms->meta_keywords = $request->meta_keywords;
        $cms->save();

        return redirect('pages/list')->with('success', 'CMS Page Has Been Updated Updated Successfully.');
    }
}
