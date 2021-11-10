@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h4>Custom Fields</h4>
            
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="system-field-tab" href="{{ url('/admin/custom_fields') }}">System Fields</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="category-tab" href="{{ url('/admin/custom_fields/category') }}">Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="campaign-tree-tab" href="{{ url('/admin/custom_fields/campaign_tree') }}">Campaign Tree</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="mt-4">

                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Field Name</th>
                                    <th scope="col">Field Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $x = 1;
                                @endphp
                                @foreach ($system_fields as $system_field)
                                    <tr>
                                        <td>{{$x}}</td>
                                        <td>{{$system_field->name}}</td>
                                        <td>{{$system_field->value}}</td>
                                    </tr>
                                    @php
                                        $x++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var base_url = "{{ url('/') }}";
        $(".table").DataTable();
    });
</script>
@endsection