{extends file='page.tpl'}

{block name='page_title'}
  Referral Program
{/block}

{block name='page_content'}
	<div class="row">
		<div class="col-md-4 text-md-center">
			<i class="fa fa-2x fa-bullhorn" aria-hidden="true"></i> 
			<strong>Referral Code</strong>
			<hr/>
			{$code}
		</div>
		<div class="col-md-4 text-md-center">
			<span class="align-middle">
				<i class="fa fa-2x fa-handshake-o" aria-hidden="true"></i>
				<strong>No. of person Referred</strong>
			</span>
			<hr/>
		</div>
		<div class="col-md-4 text-md-center">
			<i class="fa fa-2x fa-money" aria-hidden="true"></i>
			<strong>Referral Compliments</strong>
			<hr/>
		</div>
	</div>
{/block}
