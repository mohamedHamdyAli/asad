<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\services\User\UserHelperFunctionService;

class UserController extends Controller
{
    private UserHelperFunctionService $userService;

    public function __construct(UserHelperFunctionService $userService)
    {
        $this->userService = $userService;
    }


    public function index()
    {
        $data = $this->userService->getUserData();
        return response()->json([
            'status' => 'success',
            'message' => 'Users Data Fetched successfully',
            'data' => $data
        ], 200);
    }


    public function store(UserRequest $request)
    {
       $data = $this->userService->createUser($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $data

        ]);
    }

    public function show($id)
    {
        $data = $this->userService->getUserData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User data fetched successfully',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        $data = $this->userService->getUserData($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User data for editing fetched successfully',
            'data' => $data
        ], 200);
    }


    public function update(UserRequest $request, $id)
    {
        $data =  $this->userService->updateUserData($request->validated(), $id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $data
        ], 200);
    }


    public function destroy($id)
    {
       $data = $this->userService->deleteUser($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ], 200);
    }
}
