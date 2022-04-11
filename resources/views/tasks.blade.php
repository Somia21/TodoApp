@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="card card-new-task">
                    <div class="card-header">New Task</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('tasks.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input id="title" name="title" type="text" maxlength="255" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" autocomplete="off" />
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="title">Deadline</label>
                                <input id="deadline" name="deadline" type="datetime-local" class="form-control{{ $errors->has('deadline') ? ' is-invalid' : '' }}"/>
                                @if ($errors->has('deadline'))
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('deadline') }}</strong>
                                </span>
                                @endif
                                <input type="hidden" name="tz" id="tz">
                            </div>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Tasks</div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tasks</th>
                                    <th>Deadline</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
    $(document).ready(function(){
        var timezone_offset_minutes = new Date().getTimezoneOffset();
        timezone_offset_minutes = timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;
        console.log("calling ajx");
        $.ajax({
            url: '{{route("tasks.index")}}',
            data: {timezone: timezone_offset_minutes},
            success: function(response){
                console.log("response",response);
                // $.each(response.tasks, function(key, value) {

                //     var title = '';
                //     var deadline = '';
                //     var complete = '';

                //     if(value.is_complete){
                //         title = '<s><td>' + value.title + '</td></s>';
                //         deadline = '<s><td>' + value.deadline + '</td></s>';
                //     }else{
                //         title = '<td>' + value.title + '</td>';
                //         deadline = '<td>' + value.deadline + '</td>';
                //         complete = '<td text-right><a href="/update/'+ value.id +'" class="btn btn-primary">Complete</a></td>';
                //     }
                //     var html = '<tr>'+title+deadline+complete+'</tr>';
                //     $('tbody').append(html);
                // });
            }
        });
    });
</script>
@endpush