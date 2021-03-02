@if(count($devices) == 0)
	<div class="col-lg-12" style="font-size:30px;color:red;">該區域無設備,請確認設備清單</div>
@else
@foreach($devices as $key => $device)
<fieldset>
	<legend>{{$device->family.' - '.$device->name}}</legend>
	<table  class="table table-bordered">
			<thead>
				<tr>
				 	@foreach($timeRanges as $tk =>$time)
						@if($tk==0)
							@if(substr($time['value'],-2,2)==30)
								<th class="my_th" colspan=1>{{$time['start_key']}}</th>
							@else
								<th class="my_th" colspan=2>{{substr($time['start_key'],0,2)}}-{{substr($timeRanges[($tk)+1]['end_key'],0,2)}}</th>
							@endif
						@else
							@if(isset($timeRanges[$tk+1]))
								@if(substr($timeRanges[0]['value'],-2,2)==30)
									@if($tk%2==1)
										<th class="my_th" colspan=2>{{substr($time['start_key'],0,2)}}-{{substr($timeRanges[($tk)+1]['end_key'],0,2)}}</th>
									@endif
								@else
									@if($tk%2==0)
										<th class="my_th" colspan=2>{{substr($time['start_key'],0,2)}}-{{substr($timeRanges[($tk)+1]['end_key'],0,2)}}</th>
									@endif
								@endif
							@else
								@if(substr($time['value'],-2,2)==30)
									@if(substr($timeRanges[0]['value'],-2,2)==30)
										@if($tk%2==1)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@else
										@if($tk%2==0)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@endif
								@else
									@if(substr($timeRanges[0]['value'],-2,2)==30)
										@if($tk%2==1)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@else
										@if($tk%2==0)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@endif
								@endif

							@endif
							
							<!-- if($tk) -->
							

						@endif
                    @endforeach
                </tr>
			</thead>
			<tbody>
				<tr>
					@foreach($timeRanges as $tk =>$time)
                    	<th><input type="checkbox" value="{{$device->id}}-{{$time['value']}}" onclick="selectAll(this)"></th>
                    @endforeach
				</tr>
			</tbody>
	</table>
	@foreach($ranges as $k => $date)
		<h5>{{$date}}   {{isset($dayMap[$date])?$dayMap[$date]:''}}</h5>
		
		<table class="table table-bordered">
			<thead>
				 <tr>
				 	@foreach($timeRanges as $tk =>$time)
						@if($tk==0)
							@if(substr($time['value'],-2,2)==30)
								<th class="my_th" colspan=1>{{$time['start_key']}}</th>
							@else
								<th class="my_th" colspan=2>{{substr($time['start_key'],0,2)}}-{{substr($timeRanges[($tk)+1]['end_key'],0,2)}}</th>
							@endif
						@else
							@if(isset($timeRanges[$tk+1]))
								@if(substr($timeRanges[0]['value'],-2,2)==30)
									@if($tk%2==1)
										<th class="my_th" colspan=2>{{substr($time['start_key'],0,2)}}-{{substr($timeRanges[($tk)+1]['end_key'],0,2)}}</th>
									@endif
								@else
									@if($tk%2==0)
										<th class="my_th" colspan=2>{{substr($time['start_key'],0,2)}}-{{substr($timeRanges[($tk)+1]['end_key'],0,2)}}</th>
									@endif
								@endif
							@else
								@if(substr($time['value'],-2,2)==30)
									@if(substr($timeRanges[0]['value'],-2,2)==30)
										@if($tk%2==1)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@else
										@if($tk%2==0)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@endif
								@else
									@if(substr($timeRanges[0]['value'],-2,2)==30)
										@if($tk%2==1)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@else
										@if($tk%2==0)
											<th class="my_th" colspan=2>{{$time['start_key']}}</th>
										@endif
									@endif
								@endif

							@endif
							
							<!-- if($tk) -->
							

						@endif
                    @endforeach
                </tr>
			</thead>
			<tbody>
				<tr class="pick-row">
					@foreach($timeRanges as $tk =>$time)
						@if(isset($device->BookingHistory_Mark[$date][$time['start_key']]))
						<td class="full">
							
						</td>
						@else
							@if($date<=$now['day']&&$time['end_key']<$now['range'])
								<td class="expired"></td>
							@else
								<td class="idle idle-{{$device->id}}-{{$time['value']}}" date="{{$date}}" start="{{$time['start_key']}}"  end="{{$time['end_key']}}"  place="{{$device->id}}"></td>
							@endif
						@endif
                    	
                    @endforeach
				</tr>
			</tbody>
			
		</table>
	@endforeach	
</fieldset>
@endforeach
@endif



