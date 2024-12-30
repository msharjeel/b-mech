@extends('layout.app')
@section('content')

<!-- @php
    var_dump($error);
@endphp -->
<style>
    html,
    body {
        height: 100%;
        width: 100%;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .mainContainer {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        flex-direction: column;
    }

    .mainContainer h1 {
        font-size: 5em;
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #933020;
        text-align: center;
    }

    .mainContainer svg {
        height: 5em;
        width: 5em;
    }
</style>
<div class="mainContainer">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
        <path fill="#933020" d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c-9.4 9.4-9.4 24.6 0 33.9l47 47-47 47c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l47-47 47 47c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-47-47 47-47c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-47 47-47-47c-9.4-9.4-24.6-9.4-33.9 0z" />
    </svg>
    <h1>Payment Failed</h1>
</div>
<script>
    document.title = "Payment Failed"
</script>
@stop