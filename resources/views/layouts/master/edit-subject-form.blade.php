<a role="button" class="btn {{($sub->active == 1)? 'btn-warning': 'btn-danger' }} btn-xs" href="" data-toggle="modal" data-target="#editSubjectModal{{$sub->id}}"><i class="material-icons">edit</i> @lang('Edit')</a>

<div class="modal fade" id="editSubjectModal{{$sub->id}}" tabindex="-1" role="dialog" aria-labelledby="editSubjectModal{{$sub->id}}Label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Edit {{($sub->active == 0)? "Inactive" : "" }} Subject</h4>
                </div>
            <div class="modal-body">
            @if($sub->active)
                <form class="form-horizontal" action="{{ url('subject/update_subject/'.$sub->id) }}" method="post">
            @else
                <form class="form-horizontal" action="{{ url('subject/update_inactive_subject/'.$sub->id) }}" method="post"> 
            @endif
                {{csrf_field()}}
                {{ method_field('PUT') }}
                <div class="form-group">
                    <label for="subject{{$sub->id}}" class="col-sm-4 control-label">@lang('Name')</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="name" id="subject{{$sub->id}}" value="{{$sub->name}}">
                    </div>
                </div>
                @if($sub->active)
                    <div class="form-group">
                        <label for="subjectOptionClasses" class="col-sm-4 control-label">@lang('Options for')</label>
                        <div class="col-sm-8 text-left">
                            @foreach ($classes as $class)
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="form-check-input" id="inlineCheckbox{{$loop->iteration}}"" name="options[]" value="{{$class->id}}"
                                    @php
                                        $subClass = \App\SubjectClass::where([
                                            'subject_id' => $sub->id,
                                            'class_id' => $class->id,
                                            'active' => 1,
                                        ])->get();
                                    @endphp
                                    @if($subClass->first())
                                        checked
                                    @endif
                                    >
                                    {{$class->class_number}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="active{{$sub->id}}" class="col-sm-4 control-label">@lang('Make Inactive')</label>
                    <div class="col-sm-4">
                        <select id="sub_active" class="form-control" name="sub_active">
                            <option value="1" {{($sub->active == 1)? 'selected="selected"' : ''}}>Active</option>
                            <option value="0" {{($sub->active == 0)? 'selected="selected"' : ''}}>Inactive</option>
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
