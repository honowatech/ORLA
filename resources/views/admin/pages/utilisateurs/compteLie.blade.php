@if ($compteLie == null)
@else
	@foreach($compteLie as $info_compte)
	<option value="{{$info_compte->id}}">
		{{$info_compte->noms}} {{$info_compte->prenoms}}
	</option>
	@endforeach
@endif