@component('mail::message')

   ### Hi {{ $redeem->user->name  }},

   {!! $messageContent !!}

   If you have any clarification/questions please contact the admin team for further assistance.

   <br><br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
