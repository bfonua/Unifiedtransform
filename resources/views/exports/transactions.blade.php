<table>
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Receipt no</th>
            <th>Date</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Fee</th>
            <th>Amount</th>
            <th>Category</th>
            <th>Form</th>
            <th>Form #</th>
            {{-- <th>House</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->receipt}}</td>
                <td>{{$item->pay_date}}</td>
                <td>{{$item->user->studentInfo->tct_id}}</td>
                <td>{{$item->user->lst_name}}</td>
                <td>{{$item->fees->fee_type->name}}</td>
                <td>{{$item->amount}}</td>
                <td>{{$item->fees->fee_channel->name}}</td>
                <td>{{$item->user->studentInfo->section->class->class_number.$item->user->studentInfo->section->section_number}}</td>
                <td>{{$item->user->studentInfo->form_num}}</td>
                {{-- <td>{{$item->user->studentInfo->house->house_abbrv}}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
