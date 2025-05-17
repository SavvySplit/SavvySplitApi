<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    // GET /api/groups
    /**
     * @authenticated 
     */
    public function index()
    {
        return response()->json(Group::all());
    }

    // POST /api/groups
    /**
     * @authenticated 
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'members' => 'array',
            'description' => 'nullable|string',
            'color' => 'nullable|integer',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $group = Group::create($validated->validated());

        return response()->json($group, 201);
    }

    // GET /api/groups/{id}
    /**
     * @authenticated 
     */
    public function show($id)
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return response()->json($group);
    }

    // PUT /api/groups/{id}
    /**
     * @authenticated 
     */
    public function update(Request $request, $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'members' => 'sometimes|required|array',
            'description' => 'nullable|string',
            'color' => 'nullable|integer',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $group->update($validated->validated());

        return response()->json($group);
    }

    // DELETE /api/groups/{id}
    /**
     * @authenticated 
     */
    public function destroy($id)
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted']);
    }
}
