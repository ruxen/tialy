<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetTokenRequest;
use App\Http\Requests\StoreUrlRequest;
use App\Http\Requests\UpdateUrlRequest;
use App\Models\Url;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    /**
     * Redirect to the original URL
     *
     * @param string $slug
     */
    public function redirect($slug): RedirectResponse
    {
        $url = Cache::remember('url_' . $slug, now()->addMinutes(30), function () use ($slug) {
            return Url::where('slug', $slug)->firstOrFail();
        });

        $url->logVisit();
        return redirect()->to($url->original_url, 301);
    }

    /**
     * Get token for the API
     *
     * @param GetTokenRequest $request
     */
    public function getToken(GetTokenRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect password'
            ], 401);
        }

        $token = $user->createToken('tialytoken')->accessToken;

        return response()->json([
            'status' => true,
            'token' => $token
        ], 200);
    }

    /**
     * Get list shortened URLs
     *
     * @param Request $request
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $urls = Url::paginate($perPage);

        return response()->json($urls, 200);
    }

    /**
     * Show a specific shortened URL
     *
     * @param int $id
     */
    public function show($id): JsonResponse
    {
        $url = Url::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $url
        ], 200);
    }

    /**
     * Create a new shortened URL
     *
     * @param StoreUrlRequest $request
     */
    public function store(StoreUrlRequest $request): JsonResponse
    {
        do {
            $slug = $request->slug ?? Str::random(6);
        } while (Url::where('slug', $slug)->exists());

        Url::create([
            'slug' => $slug,
            'original_url' => $request->original_url
        ]);

        $shortenedUrl = env('APP_URL') . '/' . $slug;

        return response()->json([
            'status' => true,
            'message' => 'Shortened URL created successfully',
            'shortened_url' => $shortenedUrl
        ], 201);
    }


    /**
     * Update shortened URL
     *
     * @param UpdateUrlRequest $request
     * @param int $id
     */
    public function update(UpdateUrlRequest $request, $id): JsonResponse
    {
        $url = Url::findOrFail($id);
        Cache::forget('url_' . $url->slug);
        $url->update($request->all());
        $shortenedUrl = env('APP_URL') . '/' . $url->slug;

        return response()->json([
            'status' => true,
            'message' => 'Shortened URL updated successfully',
            'shortened_url' => $shortenedUrl
        ], 201);
    }

    /**
     * Delete a specific shortened URL
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $url = Url::findOrFail($id);
        Cache::forget('url_' . $url->slug);
        $url->delete();

        return response()->json([
            'status' => true,
            'message' => 'Shortened URL deleted successfully'
        ], 200);
    }
}
