<div class="section-<?php echo $pluginName;?>"><!-- $pluginName - задает название секции для разграничения JS скрипта -->
  <div class="b-modal hidden-form">
    <div class="product-table-wrapper add-cat-form">
      <div class="widget-table-title">
        <h4 class="pages-table-icon" id="modalTitle">Добавить характеристику</h4>
        <div class="b-modal_close tool-tip-bottom" title="Закрыть окно"></div>
      </div>
      <div class="widget-table-body">
        <div class="add-product-form-wrapper">

          <div class="add-img-form">
            <h3>Не распределенные по группам характеристики</h3>
            <div class="search-block">
              <input type="text" name="search" id="search" placeholder="Найти..." class="custom-input search-input">
              <a href="#" id="seacrh-btn" class="searchProd tool-tip-top" title="Поиск"></a>
              <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <div class="content-modal">
              <h3 id="result-title">Все характеристики</h3>
              <div class="propertys">
                <?if(!empty($newProperty)):?>
                <ul>
                  <?foreach($newProperty as $item):?>
                  <li>
                    <?=$item['name'];?>
                    <input type="checkbox" value="<?=$item['id'];?>" name="propertys[]">
                  </li>
                  <?endforeach;?>
                </ul>
                <?endif;?>
              </div>
            </div>
            <div class="clear"></div>

            <button class="save-button tool-tip-bottom" title="Добавить характеристики в группу"><span>Добавить</span></button>

            <div class="clear"></div>
          </div>
        </div>
      </div>
    </div>
  </div>  

  <!-- Тут начинается верстка видимой части станицы настроек плагина-->

  <div class="widget-table-body">
    <div class="widget-table-action">
      <a href="javascript:void(0);" class="custom-btn show-property-order"><span>Настройки</span></a>
      <a href="javascript:void(0);" class="custom-btn add-new-button show-add-form-group"><span>Создать группу</span></a>
      <div class="clear"></div>
    </div>
    <div class="property-order-container new">    
      <h2>Создать группу:</h2>
        <form  class="base-setting" name="base-setting" method="POST">       
          <ul class="list-option">
            <li><label><span>Название группы:</span><input style="width:300px;" type="text" id="name-new-group"></label></li>
          </ul>
          <div class="clear"></div>
        </form>
        <div class="clear"></div>
      <a href="javascript:void(0);" class="base-setting-save custom-btn"><span>Сохранить</span></a>
      <div class="clear"></div>
    </div>

    <div class="property-order-container settings">    
      <h2>Настройки плагина:</h2>
        <form  class="base-setting" name="base-setting" method="POST">       
          <ul class="list-option">
            <li><label><span>Удалять характеристики из системы:</span><input type="checkbox" name="delete_property" value="<?php echo $options["delete_property"];?>" <?php echo ($options["delete_property"]!='false')?'checked=cheked':''?>></label></li>
          </ul>
          <div class="clear"></div>
        </form>
        <div class="clear"></div>
      <a href="javascript:void(0);" class="base-setting-save custom-btn"><span>Сохранить</span></a>
      <div class="clear"></div>
    </div>
    
    <div class="wrapper-entity-setting">
      <div id="propertys">
        <?if(!empty($entity)):?>
        <?foreach($entity as $group):?>
        <div class="group" id="<?=$group['id'];?>" data-id-group="<?=$group['id'];?>">
          <div class="clearfix btns-box">
            <div class="right btns">
              <a href="javascript:void(0);" class="delete"><span>Удалить</span></a>
              <a href="javascript:void(0);" class="edit"><span>Редактировать</span></a>
              <a href="javascript:void(0);" class="add-property custom-btn add-new-button"><span>Добавить</span></a>
            </div>
          </div>
          <h3 class="clearfix">
            <span class="left title-box"><?=$group['name'];?></span>
          </h3>
          <div class="content">
          <?if(!empty($group['property'])):?>
          <ul class="list-property">
            <?foreach($group['property'] as $property):?>
            <li id="<?=$property['id'];?>">
              <?=$property['name'];?>
              <span class="delete-row">
                <a href="javascript:void(0);"></a>
              </span>
            </li>
            <?endforeach;?>
          </ul>
          <?else:?>
          <div class="well">Добавьте в группу характеристики</div>
          <?endif;?>
          </div>
        </div>
        <?endforeach;?>
        <?else:?>
        <div class="well">Создайте группу характеристик</div>
        <?endif;?>
      </div>
    </div>
  </div>
</div>