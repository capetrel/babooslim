@if(isset($result['success']))
    <div id="flashMessage" class="success-message">{{ $result['success'][0] }} <span id="closeFlash">&#10006;</span></div>
@elseif(!is_null($result['errors']))
    <div id="flashMessage" class="error-message">Veuillez corriger les erreurs dans le formulaire <span id="closeFlash">&#10006;</span></div>
@endif