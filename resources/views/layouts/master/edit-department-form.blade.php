<a role="button" class="btn btn-primary btn-xs" href="" data-toggle="modal" data-target="#editDepartment"><i class="material-icons">edit</i> </a>
<!-- MODAL -->
<div class="modal fade" id="editDepartment" tabindex="-1" role="dialog" aria-labelledby="editDepartmentLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myDepartmentLabel">@lang('Edit Department')</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="{{url('school/edit-tct-department/'.$department->id)}}" method="post">
                {{csrf_field()}}
                {{ method_field('PUT') }}
                <div class="form-group">
                    <label for="departmentName" class="col-sm-4 control-label"><h5>@lang('Department Name')</h5></label>
                    <div class="col-sm-8">
                        <input type="text" name="departmentName" class="form-control" id="departmentName" value="{{$department->department_name}}" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger btn-sm">@lang('Submit')</button>
            </div>   
        </form> 
        </div>
    </div>
</div>
