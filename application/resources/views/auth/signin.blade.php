<div class="mb-3">

    <label class="form-label">
        {{__('Почта')}}
    </label>

    {!!  \Orchid\Screen\Fields\Input::make('email')
        ->type('email')
        ->required()
        ->tabindex(1)
        ->autofocus()
        ->placeholder(__('Ведите почту'))
    !!}
</div>

<div class="mb-3">
    <label class="form-label w-100">
        {{__('Пароль')}}
    </label>

    {!!  \Orchid\Screen\Fields\Password::make('password')
        ->required()
        ->tabindex(2)
        ->placeholder(__('Введите пароль'))
    !!}
</div>

<div class="row align-items-center">
    <div class="mb-3 col-md-6 col-xs-12">
        <label class="form-check">
            <input type="hidden" name="remember">
            <input type="checkbox" name="remember" value="true"
                   class="form-check-input" {{ !old('remember') || old('remember') === 'true'  ? 'checked' : '' }}>
            <span class="form-check-label"> {{__('Запомнить')}}</span>
        </label>
    </div>
    <div class="mb-3 col-md-6 col-xs-12">
        <button id="button-login" type="submit" class="btn btn-default btn-block" tabindex="3">
            <x-orchid-icon path="login" class="small me-2"/>
            {{__('Войти')}}
        </button>
    </div>
</div>