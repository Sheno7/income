<?php

namespace App\Admin\Controllers;

use App\Models\Account;
use App\Models\Balance;
use App\Http\Controllers\Controller;
use App\Models\Source;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BalanceController extends Controller
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
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
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
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
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
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
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
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Balance);

        $grid->id('ID');
        $grid->date('date');
        $grid->quantity('quantity');
        $grid->value_in_pound('value_in_pound');
        $grid->value_in_dollar('value_in_dollar');
        $grid->column('account_id',"Account")->display(function (){
            return @Account::find($this->account_id)->name;
        });
//        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Balance::findOrFail($id));

        $show->id('ID');
        $show->date('date');
        $show->quantity('quantity');
        $show->value_in_pound('value_in_pound');
        $show->value_in_dollar('value_in_dollar');
        $show->account_id('account_id');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Balance);

        $form->display('ID');
        $form->date('date', 'date');
        $form->number('quantity', 'quantity');
        $form->number('value_in_pound', 'value_in_pound');
        $form->number('value_in_dollar', 'value_in_dollar');
        $form->select('type', 'type')->options(["IN"=>"In","OUT"=>"Out"]);
        $form->select('account_id', 'account_id')->options(function () {
            $options = [];
            $source = Account::query()->select(["id", "name"])->get();
            foreach ($source as $row) {
                $options[$row->id] = $row->name;
            }
            return $options;
        });
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
