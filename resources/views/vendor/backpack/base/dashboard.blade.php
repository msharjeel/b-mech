@extends(backpack_view('blank'))
<div class="row">
@php
/**
    if (config('backpack.base.show_getting_started')) {
        $widgets['before_content'][] = [
            'type'        => 'view',
            'view'        => 'backpack::inc.getting_started',
        ];
    } else {
        $widgets['before_content'][] = [
            'type'        => 'jumbotron',
            'heading'     => trans('backpack::base.welcome'),
            'content'     => trans('backpack::base.use_sidebar'),
            'button_link' => backpack_url('logout'),
            'button_text' => trans('backpack::base.logout'),
        ];
    }
*/
    $widgets['before_content'][] = [ 
        'type'    => 'div',
        'class'   => 'row',
        'content' =>    [
            [
                'type'        => 'progress',
                'wrapper'     => ['class' => 'col-md-3'],
                'class'       => 'card text-white bg-primary mb-2',
                'value'       => '11.456',
                'description' => 'Registered users.',
                'progress'    => 57, // integer
                'hint'        => '8544 more until next milestone.',
            ],
            [
                'type'        => 'progress',
                'wrapper'     => ['class' => 'col-md-3'],
                'class'       => 'card text-white bg-primary mb-2',
                'value'       => '11.456',
                'description' => 'Registered Vendors.',
                'progress'    => 57, // integer
                'hint'        => '8544 more until next milestone.',
            ],
            [
                'type'        => 'progress',
                'wrapper'     => ['class' => 'col-md-3'],
                'class'       => 'card text-white bg-primary mb-2',
                'value'       => '11.456',
                'description' => 'Total vehicles',
                'progress'    => 57, // integer
                'hint'        => '8544 more until next milestone.',
            ],
            [
                'type'        => 'progress',
                'wrapper'     => ['class' => 'col-md-3'],
                'class'       => 'card text-white bg-primary mb-2',
                'value'       => '11.456',
                'description' => 'Total vehicles',
                'progress'    => 57, // integer
                'hint'        => '8544 more until next milestone.',
            ]
        ]
    ];
   
@endphp
</div>
@section('content')
@endsection
