<table>
    <thead>
        <tr>
            <th>TCTID</th>
            <th>Last Name</th>
            <th>Given Name</th>
            <th>Full Name</th>
            <th>DOB</th>
            <th>Category</th>
            <th>Status</th>
            <th>Form</th>
            <th>Form #</th>
            <th>House</th>
            <th>Church</th>
            <th>Village</th>
            <th>Nationality</th>
            <th>Phone</th>
            <th>Health Conditions</th>
            <th>Reg Date</th>
            <th>Reg Notes</th>
            <th>Previous Form</th>
            <th>Previous School</th>
            <th>Father's Name</th>
            <th>Mother's Name</th>
            <th>Parent's Phone</th>
            {{-- <th>Assigned</th> --}}
            <th>Active</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $stu)
            <tr>
                <td>{{$stu->tct_id}}</td>
                <td>{{$stu->student->lst_name}}</td>
                <td>{{$stu->student->given_name}}</td>
                <td>{{$stu->student->given_name}} {{$stu->student->lst_name}}</td>
                <td>{{$stu->birthday}}</td>
                <td>{{$stu->category_id}}</td>
                <td>{{ucfirst($stu->group)}}</td>
                <td>{{$stu->section->class->class_number}}{{$stu->section->section_number}}</td>
                <td>{{$stu->form_num}}</td>
                <td>{{$stu->house->house_abbrv}}</td>
                <td>{{$stu->religion}}</td>
                <td>{{$stu->student->village}}</td>
                <td>{{$stu->student->nationality}}</td>
                <td>{{$stu->student->phone_number}}</td>
                <td>{{$stu->student->health_conditions}}</td>
                <td>{{$stu->updated_at}}</td>
                <td>{{$stu->reg_notes}}</td>
                <td>{{$stu->previous_form}}</td>
                <td>{{$stu->previous_school}}</td>
                <td>{{$stu->father_name}}</td>
                <td>{{$stu->mother_name}}</td>
                <td>{{$stu->father_phone_number}}</td>
                {{-- <td>{{$stu->assigned}}</td> --}}
                <td>{{$stu->student->active}}</td>

            </tr>
        @endforeach
    </tbody>
</table>
