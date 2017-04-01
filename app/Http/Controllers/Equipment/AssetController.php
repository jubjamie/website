<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;

class AssetController extends Controller
{
    /**
     * View the asset register.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view()
    {
        return view('equipment.assets.view');
    }
}
