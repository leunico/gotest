<?php

namespace Modules\Operate\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Operate\Entities\Banner;
use Modules\Operate\Transformers\BannerResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        //读取缓存
        $form_data = $request->all();
        ksort($form_data);
        $key = http_build_query($form_data);
        if (Cache::tags('dn_banner')->get($key)) {
            $data = unserialize(Cache::tags('dn_banner')->get($key));
        }else{
            $data = Banner::with('file')
                ->type($request->type)
                ->category($request->category)
                ->belongPage($request->belong_page)
                ->platform($request->platform)
                ->where('status', 1)
                ->orderBy('number', 'Asc')
                ->get();
            foreach ($data as $vo) {
                $vo->image_url = $vo->file->driver_baseurl . $vo->file->filename;
            }
            Cache::tags('dn_banner')->forever($key, serialize($data));
        }
        return $this->response()->collection($data, BannerResource::class);
    }

}
