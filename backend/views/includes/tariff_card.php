<div class="tariffcard__card">
    <img src="/static/upload/tariff_{$id}.jpg?updated={$updated}">

    <div class="tariffcard__name">{$name}</div>

    <div class="tariffcard__description">{$description}</div>
    <div class="tariffcard__speed">{$speed} Мбит/сек</div>
    <div class="tariffcard__price">{$price} руб/месяц</div>
    <div class="tariffcard__edit js-edit-tariff" style="display: none;"><a href="/edit/{$id}">Редактировать</a></div>
    <div class="tariffcard__delete js-delete-tariff" data-tariff-id="{$id}" style="display: none;"><a href="javascript:;">Удалить</a></div>
</div>