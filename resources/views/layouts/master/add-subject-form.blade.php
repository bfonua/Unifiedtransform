<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#addSubjectModal">+  @lang(' Add New Subject')</button>

<!-- Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-labelledby="addSubjectModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">@lang('Add New Subject')</h4>
        </div>
        <div class="modal-body">
        <form class="form-horizontal" action="{{url('subject/add-class')}}" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label for="name" class="col-sm-4 control-label">@lang('Subject Name')</label>
                <div class="col-sm-8">
                <input type="text" name="name" class="form-control" id="name" placeholder="@lang('Subject Name')" required>
                </div>
            </div>
            <div class="form-group">
                <label for="subjectOptionClasses" class="col-sm-4 control-label">@lang('Options for')</label>
                <div class="col-sm-8">
                    @foreach ($classes as $class)
                        <label class="checkbox-inline">
                            <input type="checkbox" id="inlineCheckbox{{$loop->iteration}}"" name="options[]" value="{{$class->id}}">{{$class->class_number}}
                        </label>
                    @endforeach
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
