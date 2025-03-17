<?php

namespace App\Admin\Controllers;

use App\Models\Source;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TransactionController extends Controller
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
        $grid = new Grid(new Transaction);
        $options = [];
        $source = Source::query()->select(["id", "name"])->get();
        foreach ($source as $row) {
            $options[$row->id] = $row->name;
        }


        $grid->filter(function($filter) use ($options){
            $filter->disableIdFilter();
            $filter->where(function ($query) {
                $query->where('date', 'like', "%{$this->input}%");
            }, 'Year');
            $filter->where(function ($query) {
                $query->where('note', 'like', "%{$this->input}%");
            }, 'note');
            $filter->between('date', "Date")->datetime();
            $filter->in('source_id',"Source")->multipleSelect($options);
            $filter->equal('type')->select(["In"=>"In","OUT"=>"Out"]);

        });

        $grid->id('ID');
        $grid->column('source_id',"Source")->display(function (){
            return @Source::find($this->source_id)->name;
        });

        $grid->value_in_pound('value_in_pound')->totalRow();
        $grid->value_in_dollar('value_in_dollar')->totalRow();
        $grid->column('dollar_value',"Dollar value")->display(function (){
            return round($this->value_in_pound/$this->value_in_dollar,2);
        });
        $grid->type('type');
        $grid->date('date');
        $grid->note('note');
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
        $show = new Show(Transaction::findOrFail($id));

        $show->id('ID');
        $show->source_id('source_id');
        $show->value_in_pound('value_in_pound');
        $show->value_in_dollar('value_in_dollar');
        $show->type('type');
        $show->date('date');
        $show->note('note');
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
        $form = new Form(new Transaction);

//        $form->display('ID');
        $form->select('source_id', 'source_id')->options(function () {
            $options = [];
            $source = Source::query()->select(["id", "name"])->get();
            foreach ($source as $row) {
                $options[$row->id] = $row->name;
            }
            return $options;
        });
        $form->number('value_in_pound', 'value_in_pound');
        $form->number('value_in_dollar', 'value_in_dollar');
        $form->select('type', 'type')->options(["IN" => "In", "OUT" => "Out"]);
        $form->date('date', 'date');
        $form->text('note', 'note');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
