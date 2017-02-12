<?php
    
    namespace App\Http\Controllers;
    
    use App\CommitteeRole;
    use App\Http\Requests\CommitteeRequest;
    use Illuminate\Http\Request;
    use Szykra\Notifications\Flash;
    
    class CommitteeController extends Controller
    {
        /**
         * View the committee.
         * @return Response
         */
        public function view()
        {
            $roles = CommitteeRole::orderBy('order', 'ASC')->get();
            $order = ['1' => 'At the beginning'];
            foreach($roles as $role) {
                $order[$role->order + 1] = "After '{$role->name}'";
            }
            
            return view('committee.view')->with([
                'roles' => $roles,
                'order' => $order,
            ]);
        }
        
        /**
         * Add a new committee position.
         * @param \App\Http\Requests\CommitteeRequest $request
         * @return \Illuminate\Http\Response
         */
        public function store(CommitteeRequest $request)
        {
            // Require ajax
            $this->requireAjax();
            
            // Check the order
            $order = $this->verifyRoleOrder($request->get('order'));
            
            // Create the new role
            CommitteeRole::create(clean($request->only('name', 'email', 'description', 'user_id')) + ['order' => $order]);
            
            // Flash message
            Flash::success('Committee role added');
            
            return $this->ajaxResponse(true);
        }
        
        /**
         * Update a committee role.
         * @param \App\Http\Requests\CommitteeRequest $request
         * @return \Illuminate\Http\Response
         */
        public function update(CommitteeRequest $request)
        {
            // Require ajax
            $this->requireAjax();
            
            // Get the role
            $role = CommitteeRole::find($request->get('id'));
            if(!$role) {
                return $this->ajaxError('', 422, 'Could not find the committee role.');
            }
            
            // Check the order
            $order = $this->verifyRoleOrder($request->get('order'));
            
            // Update the role
            $role->update(clean($request->only('name', 'email', 'description', 'user_id')) + ['order' => $order]);
            Flash::success("Role '{$role->name}' updated");
            
            return $this->ajaxResponse(true);
        }
        
        /**
         * Delete a committee role.
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function destroy(Request $request)
        {
            // Require ajax
            $this->requireAjax();
            
            // Check that a role exists for the given ID
            $role = CommitteeRole::find($request->get('id'));
            if(!$role) {
                return $this->ajaxError('', 422, 'Could not find the committee role.');
            }
            
            // Delete the role
            $role->delete();
            Flash::success('Committee role deleted');
            
            return $this->ajaxResponse(true);
        }
        
        /**
         * @param int $order
         * @return int
         */
        private function verifyRoleOrder($order)
        {
            if($order < 1 || ($order > CommitteeRole::count() && $order != 1)) {
                $order = CommitteeRole::count() + 1;
                
                return $order;
            }
            
            return $order;
        }
    }
