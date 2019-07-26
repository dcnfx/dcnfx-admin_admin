<?php
/**
 * 视频管理
 *
 * @author      dcnfx
 * @Time: 2019/07/14 15:57
 * @version     1.0 版本号
 */
namespace App\Http\Controllers\Admin;
use App\Http\Requests\StoreRequest;
use App\Models\Admin;
use App\Models\Log;
use App\Models\Stream;
use App\Service\DataService;
use Illuminate\Http\Request;
class StreamController extends BaseController
{
    /**
     * 视频流列表
     */
    public function index(Request $request)
    {
        $admin = new Admin;
        $directories = $admin -> getProjectFolder();
        $stream = Stream::query();
        if($request->filled('folder')) {
            $stream->where('folder',$request->input('folder'));
        }
        if($request->filled('type')) {
            $stream->where('type',$request->input('type'));
        }
        if($request->filled('title')) {
            $stream->where('filename', 'LIKE', '%'.trim($request->input('title')).'%');
        }
        if($request->filled('begin')) {
            $stream->where('created_at', '>=', trim($request->input('begin')));
        }
        $data =  $stream->latest('id')->paginate(50);
        return view('stream.list',['list'=>$data,'input'=>$request->all(),'project_list'=>$directories]);
    }
    /**
     * 监控增加保存
     */
    public function store(StoreRequest $request){
        $model = new Stream();
        $stream = DataService::handleDate($model,$request->all(),'streams-add_or_update');
        if( $stream['status']==1)Log::addLogs(trans('fzs.menus.handle_menu').trans('fzs.common.success'),'/stream/store');
        else Log::addLogs(trans('fzs.menus.handle_menu').trans('fzs.common.fail'),'/stream/store');
        return $stream;
    }
    /**
     * 监控编辑页面
     */
    public function edit($id=0)
    {
        $admin = new Admin;
        $directories = $admin -> getProjectFolder();
        $info = ($id > 0) ? Stream::find($id) : [];
        return view('stream.edit', ['id'=>$id,'info'=>$info,'project_list'=>$directories]);

    }

    public function update($id,Request $request){
        $model = new Stream();
        $input = $request->all();
        $input['id'] = $id;
        $stream = DataService::handleDate($model,$input,'streams-update_status');
        if( $stream['status']==1)Log::addLogs(trans('fzs.menus.handle_menu').trans('fzs.common.success'),'/stream/update');
        else Log::addLogs(trans('fzs.menus.handle_menu').trans('fzs.common.fail'),'/stream/update');
        return $stream;
    }
    /**
     * 监控删除
     */
    public function destroy($id)
    {
        $model = new Stream();
        $stream = DataService::handleDate($model,['id'=>$id],'stream-delete');
        if($stream['status']==1)Log::addLogs(trans('fzs.menus.del_menu').trans('fzs.common.success'),'/stream/destroy/'.$id);
        else Log::addLogs(trans('fzs.menus.del_menu').trans('fzs.common.fail'),'/stream/destroy/'.$id);
        return $stream;
    }

}
