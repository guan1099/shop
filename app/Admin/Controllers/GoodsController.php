<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Show;

use App\Model\GoodsModel;

class GoodsController extends Controller
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
        $grid = new Grid(new GoodsModel());

        $grid->model()->orderBy('goods_id','desc');     //倒序排序

        $grid->goods_id('商品ID');
        $grid->goods_name('商品名称');
        $grid->goods_store('库存');
        $grid->goods_price('价格');
        $grid->created_at('添加时间');
        $grid->content('商品描述');

        return $grid;
    }


    public function edit($id, Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
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
        $show = new Show(GoodsModel::findOrFail($id));

        $show->goods_id('ID');
        $show->goods_name('商品名称');
        $show->goods_store('商品库存');
        $show->goods_price('商品价格');
        $show->created_at('添加时间');
        $show->content('商品描述');

        return $show;
    }
    //创建
    public function create(Content $content)
    {

        return $content
            ->header('商品管理')
            ->description('添加')
            ->body($this->form());
    }


    protected function form()
    {
        $form = new Form(new GoodsModel());

        $form->display('goods_id', '商品ID');
        $form->text('goods_name', '商品名称');
        $form->number('goods_store', '库存');
        $form->currency('goods_price', '价格')->symbol('¥');
        $form->ckeditor('content','文件');
        return $form;
    }
}
