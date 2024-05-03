@extends ('layouts.app')

@section('content')
<div class="blur-bg-overlay"></div>
    <div class="form-popup">
        <div class="form-container">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ session('resent') }}
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
