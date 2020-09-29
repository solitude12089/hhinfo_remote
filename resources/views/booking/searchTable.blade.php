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
                    	<th>{{$time}}</th>
                    @endforeach
                </tr>
			</thead>
			<tbody>
				<tr>
					@foreach($timeRanges as $tk =>$time)
                    	<th><input type="checkbox" value="{{$device->id}}-{{$tk}}" onclick="selectAll(this)"></th>
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
                    	<th>{{$time}}</th>
                    @endforeach
                </tr>
			</thead>
			<tbody>
				<tr class="pick-row">
					@foreach($timeRanges as $tk =>$time)
						@if(isset($device->BookingHistory_Mark[$date][$tk]))
						<td class="full">{{$device->BookingHistory_Mark[$date][$tk]->customer->phone.'-'.$device->BookingHistory_Mark[$date][$tk]->customer->name}}</td>
						@else
							@if($date<=$now['day']&&$tk<$now['range'])
								<td class="expired"></td>
							@else
								<td class="idle idle-{{$device->id}}-{{$tk}}" date="{{$date}}" range="{{$tk}}" place="{{$device->id}}"></td>
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



