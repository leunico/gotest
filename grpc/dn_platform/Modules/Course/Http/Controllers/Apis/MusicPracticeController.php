<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Entities\MusicPractice;
use Modules\Course\Transformers\MusicPracticeResource;
use Illuminate\Http\JsonResponse;

class MusicPracticeController extends Controller
{
    /**
     * music practices list
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, MusicPractice $practice): JsonResponse
    {
        $tag = $request->input('tag', null);

        $data = $practice->where('status', MusicPractice::STATUS_ON)
            ->with([
                'tags',
                'book'
            ])
            ->orderBy('sort')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($value) use ($tag) {
                return ! empty($tag) ? $value->tags->contains('id', $tag) : $value;
            });

        return $this->response()->collection($data, MusicPracticeResource::class);
    }

    /**
     * Show the music practice
     *
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(MusicPractice $practice): JsonResponse
    {
        $practice->load([
            'book'
        ]);

        return $this->response()->item($practice, MusicPracticeResource::class);
    }
}
