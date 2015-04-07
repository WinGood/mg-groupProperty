<?if(!empty($data['thisUserFields'])):?>
	<?foreach($data['thisUserFields'] as $property):?>
		<?foreach($data['groupProperty'] as $key => $val):?>
			<?
			if(isset($val['property'][$property['property_id']])){
				$data['groupProperty'][$key]['property'][$property['property_id']] = $property;
				$data['groupProperty'][$key]['property'][$property['property_id']]['active'] = 1;
				$data['groupProperty'][$key]['active'] = 1;
			}?>
		<?endforeach;?>
	<?endforeach;?>
<?endif;?>

<?$is_standart = true;?>
<?if(!empty($data['groupProperty'])):?>
	<?foreach($data['groupProperty'] as $group):?>
		<?if(isset($group['active'])):?>
			<?$is_standart = false;?>
			<div class="group-print-property">
				<h3><?=$group['name'];?></h3>
				<ul class="stats">
				<?foreach($group['property'] as $property):?>
					<?if(isset($property['active'])):?>
					<li>
					  <span class="name"><?=$property['name']?>:</span>
					  <span class="value">
					  <?=$property['value']?>
					  </span>
					</li>
					<?endif;?>
				<?endforeach;?>
				</ul>
			</div>
		<?endif?>
	<?endforeach;?>
<?endif?>
<?if((!empty($data['thisUserFields'])) && ($is_standart)):?>
	<?$i=0;?>
	<?$left = ''; $right = '';?>
	<?foreach($data['thisUserFields'] as $property):?>
	<?if(($property['type'] != 'string') || ($property['name'] == 'Руководство') || ($property['name'] == 'Обзор') || ($property['name'] == 'Окончание акции (дд.мм.гггг)') ){continue;}?>
	<?$content = '
		<li>
		  <span class="name">'.$property['name'].':</span>
		  <span class="value">
		  '.$property['value'].'
		  </span>
		</li>
	';?>
	<?if(($i % 2) == 0){ $left .= $content;} else { $right .= $content; }?>
	<?$i++;?>
	<?endforeach;?>
	<?endif;?>

	<?if(!empty($right)):?>
	<div class="right">
		<ul class="stats">
			<?=$right;?>
		</ul>
	</div>
	<?endif;?>

	<?if(!empty($left)):?>
	<div class="left">
		<ul class="stats">
			<?=$left;?>
		</ul>
	</div>
<?endif;?>