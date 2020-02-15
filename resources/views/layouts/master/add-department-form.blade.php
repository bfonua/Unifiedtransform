{{-- BUTTON --}}
<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addDepartment">+Add New Department</button>
<!-- Modal -->
<div class="modal fade" id="addDepartment" tabindex="-1" role="dialog" aria-labelledby="addDepartmentLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">@lang('Add New Department')</h4>
      </div>
      <div class="modal-body">
    {{-- FORM --}}
        <form class="form-horizontal" action="{{url('school/add-tct-department')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label for="department_name" class="col-sm-4 control-label">@lang('Department Name')</label>
                <div class="col-sm-8">
                    <input type="text" name="department_name" class="form-control" id="department_name" placeholder="@lang('Department Name')" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger btn-sm">@lang('Submit')</button>
            </div>
        </form>
    </div>
    </div>
  </div>
</div>
