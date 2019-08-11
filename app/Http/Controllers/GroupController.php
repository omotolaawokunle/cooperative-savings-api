<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Group;
use App\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $user; //Authenticated User
    public function __construct()
    {
        //Check if user is logged in.
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::all()->where('is_searchable', true);
        return response()->json(['status' => 'Success', 'groups' => $groups]);
    }

    /**
     * Store a newly created Group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate group creation request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'periodic_amount' => 'required',
            'max_capacity' => 'required',
            'is_searchable' => 'required',
        ]);
        // Store group parameters in database
        $this->user->group()->create([
            'name' => $request->name,
            'description' => $request->description,
            'periodic_amount' => $request->periodic_amount,
            'max_capacity' => $request->max_capacity,
            'is_searchable' => $request->is_searchable,
        ]);
        //Add Group admin to the created group
        $this->user->groups()->attach($this->user->group->id, ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        //Return a response to end user
        return response()->json(['status' => 'Success', 'message' => 'Group created successfully'], 200);
    }

    /**
     * Add user to group
     *
     * @param \App\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addUserToGroup(Request $request, Group $group)
    {
        // Check if Group is exists
        if (is_null($group)) {
            // Return error if group doesn't exist
            return response()->json(['status' => 'Error', 'message' => 'Group not found'], 404);
        }
        if ($this->user->group && $this->user->group->id == $group->id) { // Check if logged in user is the group admin
            if (is_array($request->users)) { // Check if users to add is multiple or single
                // Add User to group
                foreach ($request->users as $user) {
                    $user = User::find($user);
                    if (!is_null($user)) { // Check if user exists
                        if (!$user->inGroup($group->id)) { // Check if user is already in group
                            if ($group->max_capacity >= $group->userCount($group->id)) { // Check if Group has reached maximum capacity
                                // Return Error
                                return response()->json(['status' => 'Error', 'message' => 'This group is already at maximum capacity']);
                            } else {
                                // Add User to group
                                $user->groups()->attach($group->id, ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                                /* Add User to Collection List */
                                $collection = $group->collection()->list;
                                $collection = json_decode($collection);
                                array_push($collection, $user->id);
                                $collection = new \App\Collection(['list' => json_encode($collection)]);
                                $group->collection()->save($collection);
                                // Return success
                                return response()->json(['status' => 'Success', 'message' => 'Group joined successfully'], 200);
                            }
                        } else {
                            return response()->json(['status' => 'Warning', 'message' => $user->name . ' is already a member of this group!']);
                        }
                    } else {
                        return response()->json(['status' => 'Error', 'message' => 'User not found!']);
                    }
                }
            } else {
                $user = $request->users;
                $user = User::find($user);
                if (!is_null($user)) { // Check if user exists
                    if (!$user->inGroup($group->id)) { // Check if user is already in group
                        if ($group->max_capacity >= $group->userCount($group->id)) { // Check if Group has reached maximum capacity
                            return response()->json(['status' => 'Error', 'message' => 'This group is already at maximum capacity']);
                        } else {
                            // Add User to group
                            $user->groups()->attach($group->id, ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                            /* Add User to Collection List */
                            $collection = $group->collection()->list;
                            $collection = json_decode($collection);
                            array_push($collection, $user->id);
                            $collection = new \App\Collection(['list' => json_encode($collection)]);
                            $group->collection()->save($collection);
                            // Return success
                            return response()->json(['status' => 'Success', 'message' => 'Group joined successfully'], 200);
                        }
                    } else {
                        return response()->json(['status' => 'Warning', 'message' => $user->name . ' is already a member of this group!']);
                    }
                } else {
                    return response()->json(['status' => 'Error', 'message' => 'User not found!']);
                }
            }
        } else {
            // Return unauthorized request if user is not group admin
            return response()->json(['status' => 'Error', 'message' => 'Unauthorized Request'], 401);
        }
    }

    /**
     * Join the specified group
     *
     *  @param \App\Group $group
     *  @return \Illuminate\Http\Response
     */
    public function join(Group $group)
    {
        // Check if group exists
        if (is_null($group)) {
            return response()->json(['status' => 'Error', 'message' => 'Group not found'], 404);
        }
        // Check if user is already a member of the group
        if (!$this->user->inGroup($group->id)) {
            if ($group->userscount >= $group->max_capacity) { // CHeck if group is already at maximum capacity
                return response()->json(['status' => 'Error', 'message' => 'This group is already at maximum capacity']);
            } else {
                // Add User to group
                $this->user->groups()->attach($group->id, ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                /* Add User to Collection List */
                $collection = $group->collection->list;
                if (is_null($collection)) {
                    $collection = json_encode([]);
                }
                $collection = json_decode($collection);
                array_push($collection, $this->user->id);
                $collection = new \App\Collection(['list' => json_encode($collection)]);
                $group->collection()->save($collection);
                // Return success
                return response()->json(['status' => 'Success', 'message' => 'Group joined successfully'], 200);
            }
        } else {
            return response()->json(['status' => 'Warning', 'message' => 'You are already a member of this group!']);
        }
    }
    /**
     * Return the list of group members and how much they have saved
     *
     *  @param \App\Group $group
     *  @return \Illuminate\Http\Response
     */
    public function list(Group $group)
    {
        // Check if logged in user is group admin
        if ($this->user->group && $this->user->group->id == $group->id) {
            $list = [];
            foreach ($group->users as $user) { // Get Users in the group
                $saved_amount = $user->savingsWithGroup($group->id); // Retrieve amount saved by each user in the group
                $index = ['id' => $user->id, 'name' => $user->name, 'saved_amount' => $saved_amount];
                array_push($list, $index);
            }
            // Return the list of users with their savings
            return response()->json(['status' => 'Success', 'list_of_users' => $list], 200);
        } else {
            return response()->json(['status' => 'Error', 'message' => 'Unauthorized Request'], 401);
        }
    }
}
