@extends('layouts.app')
@push('styles')
<style>
    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .refresh {
        animation: rotate 1.5s linear infinite;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Game</div>

                <div class="card-body">
                    <div class="text-center">
                        <img src="{{asset('img/circle.png')}}" id = "circle" height = "250" width="250" class = "">

                        <p id = "winner" class = "display-1 none text-danger"></p>
                    </div>

                    <hr>

                    <div class="text-center">
                        <label class = "font-weight-bold h5">Your Bet</label>
                        <select class = "custom-select col-auto" id = "bet">
                            <option selected >Not In</option>
                            @foreach(range(1 , 12) as $number)
                                <option value="{{$number}}">{{$number}}</option>
                            @endforeach
                        </select>

                        <hr>

                        <p class = "font-weight-bold h5">Remaining Time</p>
                        <p id = "timer" class = "h5 text-danger">Waiting to start</p>

                        <hr>
                        <p id = "result" class = "h1"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    const timerElement  = document.getElementById('timer');
    const circleElement = document.getElementById('circle');
    const winnerElement = document.getElementById('winner');
    const betElement    = document.getElementById('bet');
    const resultElement = document.getElementById('result');


    Echo.channel('game')
        .listen( 'RemainingTimeChanged' , e => {

            timerElement.innerText = e.time;

            circleElement.classList.add('refresh');

            winnerElement.classList.add('d-none');

            resultElement.innerText = '';

            resultElement.classList.remove('text-danger' , 'text-success');

        } )
        .listen( 'WinnerNumberGenerated' , e => {

            circleElement.classList.remove('refresh');

            winnerElement.innerText = e.number;

            winnerElement.classList.remove('d-none');

            if(betElement.value == e.number)  //winner
            {
                resultElement.innerText = "You Win";
                resultElement.classList.add("text-success");
            }
            else //loser
            {
                resultElement.innerText = "You Lose";
                resultElement.classList.add("text-danger");
            }



        } );
</script>
@endpush