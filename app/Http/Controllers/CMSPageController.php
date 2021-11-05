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
}
