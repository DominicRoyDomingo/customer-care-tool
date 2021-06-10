<br>
<div>
  <div>
    <p>Dear <strong>{{ucwords($profile->firstname)}} {{ucwords($profile->surname)}},</strong></p>
  </div>

  <div>
    <h4 style="text-transform: uppercase">
      {{$campaignEmail->campaign->campaign_name}}
    </h4>
  </div>

  <div>
    {!!$campaignEmail->content_name!!}
  </div>

  <div>
    <br />
    <p>Thanks and Regards,</p>
    <p>
      Team : 
      <strong>{{$campaignEmail->brand->name}}</strong> <br /><img src="{{$campaignEmail->brand->logo}}" width="150px;" >
    </p>

  </div>
</div>