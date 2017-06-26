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
				<strong>No. of person referred</strong>
			</span>
			<hr/>
			<div class="text-md-left">
			Total Orders {$count}
			<hr/>
			Total Amount {$amount}
			</div>
		</div>
		<div class="col-md-4 text-md-center">
			<i class="fa fa-2x fa-money" aria-hidden="true"></i>
			<strong>Referral Compliments</strong>
			
			<hr/>

			<div class="row">
			Once meter fills you will get free compliments
			<br/><br/>
			{foreach $packs as $name => $pack}

				<div class="col-md-12">
					<!-- <div class="col-md-1">0</div> -->
					<div class="col-md-9">
						<progress class="progress progress-striped progress-{$pack['color']}" value="{$amount}" max="{$pack['AMT']}" data-toggle="popover" title="Popover title" data-content="{$name}"></progress>
					</div>
					{if $amount >= $pack['AMT']}
					<div class="col-md-1"><span class="tag tag-{$pack['color']}">{$name}</span></div>
					{else}
					<div class="col-md-1"><span class="tag tag-default">{$name}</span></div>
					{/if}
				</div>
			{/foreach}
			</div>

			{foreach $packs as $name => $pack}

				{if $amount >= $pack['AMT']}
				<a href="#">
					<span class="tag tag-{$pack['color']}">Get 99.9% offer for {$name}</span>
				</a>
				{/if}
			{/foreach}
		</div>
	</div>
{/block}
