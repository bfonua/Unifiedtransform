<a role="button" class="btn btn-primary btn-xs" href="" data-toggle="modal" data-target="#editFormModal{{$class->id}}"><i class="material-icons">edit</i></a>
<!-- MODAL -->
<div class="modal fade" id="editFormModal{{$class->id}}" tabindex="-1" role="dialog" aria-labelledby="editFormModal{{$class->id}}Label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myClassLabel">@lang('Edit Class')</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="{{url('school/edit-tct-class/'.$class->id)}}" method="post">
                    {{csrf_field()}}
                    {{ method_field('PUT') }}
                    <div class="form-group">
                        <label for="class{{$class->class_number}}" class="col-sm-4 control-label"><h5>@lang('Class Number/Name')</h5></label>
                        <div class="col-sm-8">
                            <input type="text" name="class_number" class="form-control" id="class{{$class->class_number}}" placeholder="@lang('Class Number/Name')" value="{{$class->class_number}}" required>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label for="session" class="col-sm-4 control-label"><h5>@lang('Session')</h5></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="session" id="session">
                        </div>
                    </div> --}}
                    <div class="form-group">
                        <label for="classgroup{{$school->id}}" class="col-sm-4 control-label"><h5>@lang('Class Group (If Any)')</h5></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="group" id="classgroup{{$school->id}}" value="{{$class->group}}">
                            {{-- <span id="helpBlock" class="help-block">@lang('Leave Empty if this Class belongs to no Group')</span> --}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="option{{$class->class_number}}" class="col-sm-4 control-label"><h5>@lang('Optional Classes')</h5></label>
                        <div class="col-sm-3">
                            <select id="options" id="option{{$class->class_number}}" class="form-control" name="options">
                                <option value="1" {{($class->options == 1)? 'selected="selected"' : ''}}>Yes</option>
                                <option value="0" {{($class->options == 0)? 'selected="selected"' : ''}}>No</option>                            
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="optionCount" class="col-sm-4 control-label"><h5>@lang('Number of Options')</h5></label>
                        <div class="col-sm-3">
                            <select id="optionCount" id="optionCount{{$class->class_number}}" class="form-control" name="optionCount">
                                {{-- <option value="1" {{($class->options == 1)? 'selected="selected"' : ''}}>Yes</option>
                                <option value="0" {{($class->options == 0)? 'selected="selected"' : ''}}>No</option>                             --}}

                                @foreach(range(1,10) as $i)
                                    <option value="{{$i}}" {{($class->optionCount == $i)? 'selected="selected"' : ''}}>{{$i}}</option> 
                                @endforeach
                            </select>

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
