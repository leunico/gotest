<?php

namespace Modules\Operate\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Operate\Http\Requests\BannerRequest;
use Modules\Operate\Entities\Banner;
use function App\responseFailed;
use function App\responseSuccess;
use Illuminate\Support\Facades\DB;
use function App\removeNullElement;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $perPage ?? 10;
        $data = Banner::with('file')->type($request->type)->paginate($perPage);
        return responseSuccess($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(BannerRequest $request)
    {
        $form_data = $request->only(['number', 'type', 'category', 'file_id', 'link', 'platform', 'status', 'belong_page']);
        $form_data = removeNullElement($form_data);
        //banner排序
        $this->bannerSort($form_data);

        $data = Banner::create($form_data);
        Cache::tags('dn_banner')->flush();
        return responseSuccess($data);
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function show(Banner $banner)
    {
        $banner->load(['file']);
        $banner->file_url = $banner->file->driver_baseurl . $banner->file->filename;
        return responseSuccess($banner);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Banner $banner, BannerRequest $request)
    {
        $form_data = $request->only(['number', 'type', 'category', 'file_id', 'link', 'platform', 'status', 'belong_page']);
        $form_data = removeNullElement($form_data);
        //banner排序
        $this->bannerSort($form_data,$banner->id);

        $banner->update($form_data);
        Cache::tags('dn_banner')->flush();
        return responseSuccess($banner);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        Cache::tags('dn_banner')->flush();
        return responseSuccess('删除成功');
    }

    //banner排序
    private function bannerSort($form_data,$id = '')
    {
        $banner = Banner::where('belong_page', $form_data['belong_page'])
            ->orderBy('number', 'Asc')->get();
        $banner_number = array_column($banner->toarray(), 'number');
        $key = array_search($form_data['number'], $banner_number);
        if (!($key === false)) {
            $i = $form_data['number'];
            foreach ($banner as $k => $vo) {
                if($id == $vo->id){
                    break;
                }
                if ($k >= $key && $i == $vo->number) {
                    $i = $vo->number + 1;
                    $vo->number = $i;
                    $vo->save();
                }
            }
        }
        return true;
    }
}
