<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;

use App\Model\UserModel;

class UsersController extends Controller
{
    use HasResourceActions;
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new UserModel());

        $grid->uid('UID');
        $grid->username('昵称');
        $grid->age('年龄');
        $grid->email('邮箱');
        $grid->created_at('注册时间');

        return $grid;
    }

    //编辑
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }


    //创建
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }


    //详情展示
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }
    protected function detail($id)
    {
        $show = new Show(UserModel::findOrFail($id));

        $show->uid('ID');
        $show->username('用户名称');
        $show->email('邮箱');
        $show->age('年龄');
        $show->score('积分');

        return $show;
    }




    //新增
    protected function form()
    {
        $form = new Form(new UserModel());

        $form->text('username', '昵称');
        $form->text('age', '年龄');
        $form->email('email', 'Email');
        $form->password('pwd', '密码');
        $form->ckeditor('content','文件');

        return $form;
    }
}
