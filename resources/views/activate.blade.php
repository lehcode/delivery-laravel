@extends('template.main')

@section('title')
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                            <h3><i class="fa fa-lock fa-4x"></i></h3>
                            <h2 class="text-center">Account activation</h2>

                            @if($errors->count() > 0)
                                <div class="alert alert-danger" role="alert">
                                    {{ $errors->first() }}
                                </div>
                            @else
                                <div class="alert alert-success" role="alert">
                                    Account activated. You can log in.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
