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
				<?foreach($group['property'] as $property):?>
					<?if(isset($property['active'])):?>
					<dl class="stats-list">
						<dt>
						  <span><?=$property['name'];?>:</span>
						</dt>
						<dd>
							<span>
							<?=$property['value'];?>
							</span>
						</dd>
					</dl>
					<?endif;?>
				<?endforeach;?>
			</div>
		<?endif?>
	<?endforeach;?>
<?endif?>
<?if((!empty($data['thisUserFields'])) && ($is_standart)):?>
	<?foreach($data['thisUserFields'] as $property):?>
	<?if(($property['type'] != 'string') || ($property['name'] == 'Руководство') || ($property['name'] == 'Обзор') || ($property['name'] == 'Окончание акции (дд.мм.гггг)') ){continue;}?>
	<dl class="stats-list">
		<dt>
		  <span><?=$property['name'];?>:</span>
		</dt>
		<dd>
			<span>
			<?=$property['value'];?>
			</span>
		</dd>
	</dl>
	<?endforeach;?>
<?endif;?>