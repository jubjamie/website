<?php

namespace App\Http\Controllers\Equipment;

use App\EquipmentBreakage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\RepairRequest;
use App\Mail\Equipment\Breakage;
use bnjns\FlashNotifications\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Szykra\Notifications\Flash;

class RepairsController extends Controller
{
    /**
     * View the list of outstanding repairs.
     * @return $this
     */
    public function index()
    {
        $this->authorize('index', EquipmentBreakage::class);
        
        $breakages = EquipmentBreakage::where('status', '<>', EquipmentBreakage::STATUS_RESOLVED)
                                      ->where('closed', false)
                                      ->orderBy('created_at', 'DESC')
                                      ->paginate(20);
        $this->checkPagination($breakages);
        
        return view('equipment.repairs.index')->with('breakages', $breakages);
    }
    
    /**
     * View the form to create a new repair.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', EquipmentBreakage::class);
        return view('equipment.repairs.create');
    }
    
    /**
     * Process the form and create a new breakage.
     * @param \App\Http\Requests\Equipment\RepairRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RepairRequest $request)
    {
        // Create the breakage
        $breakage = EquipmentBreakage::create([
            'name'        => clean($request->get('name')),
            'label'       => clean($request->get('label')),
            'location'    => clean($request->get('location')),
            'description' => clean($request->get('description')),
            'status'      => EquipmentBreakage::STATUS_REPORTED,
            'user_id'     => $request->user()->id,
            'closed'      => false,
        ]);
        
        // Send the email
        Mail::to('equip@bts-crew.com')
            ->queue(new Breakage($breakage->toArray() + [
                    'user_email'    => $breakage->user->email,
                    'user_name'     => $breakage->user->name,
                    'user_username' => $breakage->user->username,
                ]));
        
        Notifications::success('Breakage reported');
        return redirect()->route('equipment.repairs.index');
    }
    
    /**
     * View the details of a breakage.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($id)
    {
        $breakage = EquipmentBreakage::findOrFail($id);
        $this->authorize('view', $breakage);
        return view('equipment.repairs.view')->with('breakage', $breakage);
    }
    
    public function update($id, Request $request)
    {
        // Get the equipment breakage and authorise
        $breakage = EquipmentBreakage::findOrFail($id);
        $this->authorize('update', $breakage);
        
        if($request->get('action') == 'update') {
            $this->updateBreakageStatus($breakage, $request);
        } else if($request->get('action') == 'close') {
            $breakage->update([
                'closed' => true,
            ]);
            Notifications::success('Breakage closed');
        } else if($request->get('action') == 'reopen') {
            $breakage->update([
                'closed' => false,
            ]);
            Notifications::success('Breakage re-opened');
        }
        
        return redirect()->route('equipment.repairs.view', ['id' => $id]);
    }
    
    /**
     * Update the status of a breakage.
     * @param \App\EquipmentBreakage   $breakage
     * @param \Illuminate\Http\Request $request
     */
    private function updateBreakageStatus(EquipmentBreakage $breakage, Request $request)
    {
        // Validate
        $this->validate($request, [
            'status' => 'required|in:' . implode(',', array_keys(EquipmentBreakage::$Status)),
        ], [
            'status.required' => 'Please choose a status for the breakage',
            'status.in'       => 'Please choose a valid status',
        ]);
        
        // Update, message and redirect
        $breakage->update([
            'comment' => clean($request->get('comment')),
            'status'  => clean($request->get('status')),
            'closed'  => (int) $request->get('status') === EquipmentBreakage::STATUS_RESOLVED,
        ]);
        Notifications::success('Breakage updated');
    }
}