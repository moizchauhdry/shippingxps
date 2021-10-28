<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $settings= SiteSetting::all();

        return Inertia::render('Settings/SettingList',[
            'settings' => $settings
        ]);    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings');

        foreach($settings as $setting){
            $site_setting = SiteSetting::find($setting['id']);
            $site_setting->value = $setting['value'];
            $site_setting->update();
        }
       
        return redirect('settings')->with('success', 'Settings Updated!');
        
    }

}
