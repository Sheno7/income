<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Source;
use App\Models\Transaction;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    public function index(Content $content)
    {

        $query = Transaction::query();
        $result=$query->selectRaw("sum(CASE
                 WHEN type =  'IN' THEN value_in_pound
                 ELSE 0
              END) as income,
              sum(CASE
                 WHEN type =  'OUT' THEN value_in_pound
                 ELSE 0
              END) as output,
              sum(CASE
                 WHEN type =  'IN' THEN value_in_dollar
                 ELSE 0
              END) as income_dollar,
              sum(CASE
                 WHEN type =  'OUT' THEN value_in_dollar
                 ELSE 0
              END) as output_dollar")
            ->when(request()->input("start_date"), function ($q) {
                return $q->where("date", ">=", request()->input("start_date"));
            })->when(request()->input("end_date"), function ($q) {
                return $q->where("date", "<=", request()->input("end_date"));
            })->when(request()->input("note"), function ($q) {
                return $q->where('note', 'like', "%" . request()->input("note") . "%");
            })->when(request()->input("year"), function ($q) {
                return $q->where('date', 'like', "%" . request()->input("year") . "%");
            })->when(request()->input("source_id"), function ($q) {
                return $q->where("source_id", request()->input("source_id"));
            })->first();

        return $content
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->row(function (Row $row) {
                $row->column(12, $this->form());
            })->row(function ($row) use ($result){
                $row->column(4, new InfoBox('In', 'money', 'aqua', null, $result->income.' LE'));
                $row->column(4, new InfoBox('Out', 'money', 'green', null, $result->output.' LE'));
                $row->column(4, new InfoBox('Total incomes', 'money', 'yellow', null, $result->income-$result->output.' LE'));

                $row->column(4, new InfoBox('In dollar', 'money', 'aqua', null, $result->income_dollar.' USD'));
                $row->column(4, new InfoBox('Out dollar', 'money', 'green', null, $result->output_dollar.' USD'));
                $row->column(4, new InfoBox('Total incomes dollar', 'money', 'yellow', null, $result->income_dollar-$result->output_dollar.' USD'));
            });
    }


    public function form()
    {
        $form = new Form();
        $form->number('year', 'Year');
        $form->text("note", "Note");
        $form->dateRange("start_date", "end_date", "Date");
        $form->select('source_id', 'Source')->options(function () {
            $options = [];
            $source = Source::query()->select(["id", "name"])->get();
            foreach ($source as $row) {
                $options[$row->id] = $row->name;
            }
            return $options;
        });
        $form->method('get');
        $form->fill(request()->all());
        $form->disableReset();

        return new Box('Filter', $form);
    }
}
