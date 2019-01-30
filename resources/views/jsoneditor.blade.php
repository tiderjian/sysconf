
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}} {{ $class }}">

        @include('admin::form.error')

        <div id="{{$name}}" style="width: 100%; height: 100%;"></div>

        <input type="hidden" name="{{$name}}" value="@json(old($column, $value))" />
        @include('admin::form.help-block')

        <script>
            // create the editor
            $(function(){
                var container = document.getElementById("{{ $id }}");
                var options = {{ $options }} ;
                var editor = new JSONEditor(container, options);

                // set json
                var json = @json($json);
                editor.set(json);

                // get json
                $('button[type="submit"]').click(function() {
                    var json = editor.get()
                    $('input[name={{ $id }}]').val(JSON.stringify(json))
                })
            });
        </script>
    </div>
</div>
