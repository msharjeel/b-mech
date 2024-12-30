@extends('layout.app')
@section('content')
<style>
    html,
    body {
        height: 100%;
        width: 100%;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Helvetica', 'Arial', sans-serif;
    }

    body {
        padding: 0 2em;
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
        color: #009A41;
        text-align: center;
    }

    .mainContainer svg {
        height: 5em;
        width: 5em;
    }

    .transactionDetails {
        width: 100%;
        font-size: 2em;
        display: grid;
        grid-template-columns: repeat(2, auto);
        gap: 1em
    }
    .head{
        font-weight: 700;
        color: #933020;
    }
</style>
<div class="mainContainer">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
        <path fill="#009A41" d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z" />
    </svg>
    <h1>Payment is Successful</h1>
    <div class="transactionDetails">
        <span class="head">Order ID: </span>
        <span class="content"><?php echo $success["OrderId"]; ?></span>
        <span class="head">Invoice ID: </span>
        <span class="content"><?php echo $success["InvoiceId"]; ?></span>
        <span class="head">Transaction Type: </span>
        <span class="content"><?php echo $success["TransactionType"]; ?></span>
        <span class="head">Invoice Creation Date: </span>
        <span class="content"><?php echo $success["InvoiceCreationDate"]; ?></span>
    </div>
</div>
<script>
    document.title = "Payment Successful"
</script>
<!-- @php
var_dump($success);
@endphp -->
@stop