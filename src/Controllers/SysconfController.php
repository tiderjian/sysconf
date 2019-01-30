<?php
namespace Encore\Admin\Sysconf\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Sysconf\Models\Group;
use Encore\Admin\Sysconf\Models\Sysconf;
use Encore\Admin\Sysconf\Models\SysconfGroup;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Arr;
use Encore\Admin\Layout\Column;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;

class SysconfController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('System config'))
            ->row(function(Row $row){
                $row->column(8, $this->form());
                $row->column(4, function(Column $column){
                    $column->append((new Box("group config", $this->groupGrid()->render()))->style('success'));
                    $column->append($this->groupForm()->render());
                });
            });
    }

    public function store(){
        $data = Input::all();
        $sysconfs = Sysconf::get();

        foreach($data as $k => $v){
            $conf = $sysconfs->where('slug', $k)->first();
            if(is_null($conf)){
                continue;
            }
            $this->form()->update($conf->id, [$k => $v]);
        }

        return redirect(url("/admin/sysconf"));
    }

    /**
     * @return Form
     */
    protected function form(){
        $form = new Form(new Sysconf());
        $form->setAction(url('/admin/sysconf'));
        $form->disableReset();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->builder()->getTools()->disableList();

        $configUri = url('/admin/sysconf/config');
        $sysConfButton = <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$configUri}" class="btn btn-sm btn-success" title="config manage">
        <i class="fa fa-eye"></i><span class="hidden-xs"> config manage</span>
    </a>
</div>
HTML;

        $form->builder()->getTools()->prepend($sysConfButton);

        $group_conf = SysconfGroup::ordered()->get();

        $group_conf->each(function($item, $value) use($form){

            $showTab = $item->permission ? Admin::user()->can($item['permission']) : true;
            $sysconfs = Sysconf::get();

            if($showTab){
                $form->tab($item->title, function($form) use ($sysconfs, $item){
                    $sysconfs->where('group', $item['id'])->each(function($conf, $key) use($form){
                        \Encore\Admin\Sysconf\Sysconf::buildFormItem($form, $conf);
                    });
                });
            }

        });

        return $form;
    }

    public function config(Content $content)
    {
        return $content
            ->header(trans('config list'))
            ->body($this->configGrid());
    }
    
    public function configShow($id, Content $content){
        return $content->header('show config')
            ->body(Admin::show(Sysconf::findOrFail($id), function(Show $show){
                $show->getModel()->extra = json_encode($show->getModel()->extra);
                $show->title('title');
                $show->slug('slug');
                $show->type('type');
                $show->group('group')->as(function($group){
                    return SysconfGroup::find($group)->title;
                });
                $show->extra('extra')->json();
                $show->tips('tips');
                $show->status()->using(['1' => '启用', '0' => '禁用']);
                $show->sort('sort');
                $show->created_at('created_at');
                $show->updated_at('updated_at');
            }));
    }

    public function configCreate(Content $content){
        return $content
            ->header('create config')
            ->body($this->configForm()->disableEditingCheck());
    }

    public function configEdit($id, Content $content){
        return $content
            ->header('edit config')
            ->body($this->configForm()->disableCreatingCheck()->edit($id));
    }

    public function configStore(){
        $form = $this->configForm();
        $form ->builder()->field('title')->rules("unique:sysconfs");
        $form ->builder()->field('slug')->rules("unique:sysconfs");
        return $form->store();
    }

    public function configUpdate($id){
        $form = $this->configForm();
        $form ->builder()->field('title')->rules("unique:sysconfs,title,{$id}");
        $form ->builder()->field('slug')->rules("unique:sysconfs,slug,{$id}");
        return $form->update($id);
    }

    public function configDestroy($id){
        if ($this->configForm()->destroy($id)) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }

    protected function configForm(){
        return Sysconf::form(function($form){

            $form->disableReset();

            $form->saving(function($form){
                $form->input("extra", json_decode($form->input("extra")));
            });

            $form->text('title')->rules('required');
            $form->text('slug')->rules('required|alpha_dash|max:50');
            $form->sysconfSelect('type')->options(\Encore\Admin\Sysconf\Sysconf::getType())
                ->rules('required|' . (string)Rule::in(array_keys(\Encore\Admin\Sysconf\Sysconf::getType())))
            ->fillJsonEditor('extra', url("/admin/sysconf/config/create"));
            $form->select('group')->options(SysconfGroup::pluck('title', 'id'))->rules('required');

            $json = \Encore\Admin\Sysconf\Sysconf::getJsonTemplate(app('request')->query("sysconfType", null));
            $form->sysconfJson('extra')->default($json);
            $form->textarea('tips');
            $form->switch('status');
            $form->number('sort');
        });
    }

    /**
     * @return Grid
     */
    protected function configGrid(){
        $grid = new Grid(new Sysconf(), function(Grid $grid){
            $grid->disableExport();
        });

        $grid->filter(function(Grid\Filter $filter){
            $filter->disableIdFilter();

            $filter->like('title', 'title');
            $filter->like('slug', 'slug');
            $filter->equal('group')->select(SysconfGroup::pluck('title', 'id'));
        });

        $grid->title();
        $grid->slug();
        $grid->type();
        $grid->sysconfGroup()->title("Group");

        return $grid;
    }

    /**
     * @return Grid
     */
    protected function groupGrid(){
        $grid = new Grid(new SysconfGroup(), function(Grid $grid){
            $grid->setResource(url('/admin/sysconf/group'));
            $grid->disablePagination();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->disableTools();
            $grid->disableCreateButton();
            $grid->disableExport();

            $grid->actions(function($action){
                $action->disableView();
                $action->disableEdit();
            });

            $grid->model()->ordered();
        });

        $permissionModel = config('admin.database.permissions_model');

        $grid->title("group title")->editable();
        $grid->permission("permission")->editable('select', $permissionModel::pluck('name', 'slug')->prepend('', ''));
        $grid->sort('order')->orderable();

        return $grid;
    }

    public function groupUpdate($group){
        return $this->groupForm()->update($group);
    }

    protected function groupForm(){
        return SysconfGroup::form(function($form){
            $form->disableReset();
            $form->builder()->setAction(url('/admin/sysconf/group'));
            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
            $form->builder()->getTools()->disableList();
            $form->builder()->setTitle('add new group');

            $form->text('title')->rules('required');

            $permissionModel = config('admin.database.permissions_model');
            $form->select('permission')->options($permissionModel::pluck('name', 'slug'));
        });
    }

    public function groupStore(){
        return $this->groupForm()->store();
    }

    public function groupDestroy($group){
        if ($this->groupForm()->destroy($group)) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }


}