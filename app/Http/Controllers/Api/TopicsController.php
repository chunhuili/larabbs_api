<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TopicsController extends Controller
{
    public function index(Request $request, Topic $topic)
    {
        $topics = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category','replies')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ])
            ->paginate();

        return TopicResource::collection($topics);
    }

    public function store(TopicRequest $request, Topic $topic)
    {
//        return $this->errorResponse(403, '您还没有通过认证', 1003);  测试代码

        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();

        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return response(null, 204);
    }

    public function show($topicId)
    {
//        $topic->load('user', 'category');// load加载model层定义好的一对多、多对多的方法

        // 不使用路由模型绑定
        $topic = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category')
            ->findOrFail($topicId);

        // 重写路由模型绑定


        return new TopicResource($topic);
    }
}
