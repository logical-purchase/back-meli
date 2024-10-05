<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use Exception;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Handle JSON responses.
     */
    private function jsonResponse($status, $data, $code)
    {
        return response()->json(array_merge(['status' => $status], $data), $code);
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonResponse(400, ['errors' => $validator->errors()], 400);
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $wishlist = Wishlist::all();
            return $this->jsonResponse(200, ['wishlists' => $wishlist], 200);
        } catch (Exception $e) {
            return $this->jsonResponse(500, ['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationResponse = $this->validateRequest($request, [
            'id' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $wishlist = wishlist::create($request->all());
            return $this->jsonResponse(201, ['message' => 'wishlist created successfully', 'wishlist' => $wishlist], 201);
        } catch (Exception $e) {
            return $this->jsonResponse(500, ['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $wishlist = wishlist::find($id);

            if (!$wishlist) {
                return $this->jsonResponse(404, ['error' => 'wishlist not found'], 404);
            }

            return $this->jsonResponse(200, ['wishlist' => $wishlist], 200);
        } catch (Exception $e) {
            return $this->jsonResponse(500, ['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validationResponse = $this->validateRequest($request, [
            'id' => 'required|integer',
        ]);

        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $wishlist = wishlist::find($id);

            if (!$wishlist) {
                return $this->jsonResponse(404, ['error' => 'wishlist not found'], 404);
            }

            $wishlist->update($request->all());
            return $this->jsonResponse(200, ['message' => 'wishlist updated successfully', 'wishlist' => $wishlist], 200);
        } catch (Exception $e) {
            return $this->jsonResponse(500, ['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $wishlist = wishlist::find($id);

            if (!$wishlist) {
                return $this->jsonResponse(404, ['error' => 'wishlist not found'], 404);
            }

            $wishlist->delete();
            return $this->jsonResponse(200, ['message' => 'wishlist deleted successfully'], 200);
        } catch (Exception $e) {
            return $this->jsonResponse(500, ['error' => 'Internal Server Error'], 500);
        }
    }
}
