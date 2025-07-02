<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    /**
     * Get all modules.
     */
    public function index()
    {
        $modules = Module::where('is_active', true)->get();
        return response()->json($modules);
    }

    /**
     * Get all module instances.
     */
    public function getInstances()
    {
        $instances = ModuleInstance::with('module')
            ->where('is_active', true)
            ->get();

        return response()->json($instances);
    }

    /**
     * Create a new module instance.
     */
    public function createInstance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:255',
            'config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $instance = ModuleInstance::create([
            'module_id' => $request->module_id,
            'name' => $request->name,
            'is_active' => true,
            'config' => $request->config,
        ]);

        return response()->json($instance, 201);
    }

    /**
     * Update a module instance.
     */
    public function updateInstance(Request $request, $id)
    {
        $instance = ModuleInstance::where('id', $id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|required|boolean',
            'config' => 'sometimes|nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $instance->update($request->only(['name', 'is_active', 'config']));

        return response()->json($instance);
    }

    /**
     * Delete a module instance.
     */
    public function deleteInstance($id)
    {
        $instance = ModuleInstance::where('id', $id)
            ->firstOrFail();

        $instance->delete();

        return response()->json(null, 204);
    }

    /**
     * Update the display order of module instances.
     */
    public function updateDisplayOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instances' => 'required|array',
            'instances.*.id' => 'required|exists:module_instances,id',
            'instances.*.display_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->instances as $instance) {
            ModuleInstance::where('id', $instance['id'])->update([
                'display_order' => $instance['display_order']
            ]);
        }

        return response()->json(['message' => 'Display order updated successfully']);
    }
}
