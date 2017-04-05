<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class AssetController extends Controller
{
    /**
     * View the asset register.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view()
    {
        if(!Gate::allows('members.strict')) {
            throw new AuthorizationException();
        }
        
        return view('equipment.assets.view');
    }
}
