<?php

namespace App\Admin\Controllers;

use App\Model\WeixinUser;
use App\Model\WeixinMedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WeixinController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }
    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinUser);

        $grid->id('Id');
        $openid=$grid->openid('Openid')->display(function($openid){
            return '<a href="/admin/chat?openid='.$openid.'">'.$openid.'</a>';
        });
        $grid->add_time('Add time')->display(function($time){
            return date("Y-m-d H:i:s",$time);
        });
        $grid->nickname('Nickname');
        $grid->sex('Sex');
        $grid->headimgurl('Headimgurl')->display(function($url){
            return '<img src="'.$url.'">';
        });
        $grid->subscribe_time('Subscribe time');
        $grid->actions(function ($actions) {
            $actions->append('<a href="/"><i>发送信息</i></a>');
        });
        return $grid;
    }
    public function chat(Content $content)
    {
        $openid=$_GET['openid'];
        $arr=WeixinUser::where(['openid'=>$openid])->first()->toArray();
        $arr1=WeixinMedia::where(['openid'=>$openid])->OrderBy('add_time','des')->first();
        $data=[
            'list'=>$arr,
            'list1'=>$arr1
        ];
        return $content
            ->header('私聊')
            ->description('description')
            ->body(view('kefu.keliao',$data));
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinUser::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinUser);

        $form->number('openid', 'Openid');
        $form->datetime('add_time', 'Add time')->default(date('Y-m-d H:i:s'));
        $form->text('nickname', 'Nickname');
        $form->text('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->datetime('subscribe_time', 'Subscribe time')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
